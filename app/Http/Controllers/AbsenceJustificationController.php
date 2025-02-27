<?php

namespace App\Http\Controllers;

use App\Models\AbsenceJustification;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AbsenceJustificationController extends Controller
{
    /**
     * Afficher la liste des justifications d'absence.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return redirect()->route('dashboard')
                    ->with('error', 'Profil étudiant non trouvé.');
            }
            
            $justifications = AbsenceJustification::where('student_id', $student->id)
                ->with('attendance')
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('absences.justifications.index', compact('justifications'));
        } elseif ($user->isAdmin() || $user->isSuperAdmin()) {
            $justifications = AbsenceJustification::with(['student.user', 'attendance'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return view('absences.justifications.admin', compact('justifications'));
        }
        
        return redirect()->route('dashboard')
            ->with('error', 'Vous n\'avez pas accès à cette page.');
    }

    /**
     * Afficher le formulaire de création d'une justification d'absence.
     */
    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return redirect()->route('dashboard')
                ->with('error', 'Seuls les étudiants peuvent justifier des absences.');
        }
        
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil étudiant non trouvé.');
        }
        
        // Récupérer les absences non justifiées
        $absences = Attendance::where('student_id', $student->id)
            ->where('status', 'absent')
            ->whereDoesntHave('justifications')
            ->orderBy('date', 'desc')
            ->get();
            
        return view('absences.justifications.create', compact('absences'));
    }

    /**
     * Enregistrer une nouvelle justification d'absence.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isStudent()) {
            return redirect()->route('dashboard')
                ->with('error', 'Seuls les étudiants peuvent justifier des absences.');
        }
        
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil étudiant non trouvé.');
        }
        
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'reason' => 'required|string',
            'document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);
        
        // Vérifier que l'absence appartient bien à l'étudiant
        $attendance = Attendance::find($request->attendance_id);
        if (!$attendance || $attendance->student_id !== $student->id) {
            return redirect()->route('justifications.create')
                ->with('error', 'Absence non trouvée ou non autorisée.');
        }
        
        // Traiter le document justificatif
        $documentPath = null;
        if ($request->hasFile('document')) {
            $document = $request->file('document');
            $documentPath = $document->store('justifications', 'public');
        }
        
        // Créer la justification
        AbsenceJustification::create([
            'student_id' => $student->id,
            'attendance_id' => $request->attendance_id,
            'reason' => $request->reason,
            'document_path' => $documentPath,
            'status' => 'pending',
        ]);
        
        return redirect()->route('justifications.index')
            ->with('success', 'Justification d\'absence soumise avec succès.');
    }

    /**
     * Afficher une justification d'absence.
     */
    public function show(AbsenceJustification $justification)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student || $justification->student_id !== $student->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Vous n\'avez pas accès à cette justification.');
            }
        } elseif (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas accès à cette justification.');
        }
        
        return view('absences.justifications.show', compact('justification'));
    }

    /**
     * Traiter une justification d'absence (approuver ou rejeter).
     */
    public function process(Request $request, AbsenceJustification $justification)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les droits pour traiter les justifications.');
        }
        
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_comment' => 'nullable|string',
        ]);
        
        $justification->update([
            'status' => $request->status,
            'admin_comment' => $request->admin_comment,
        ]);
        
        // Si la justification est approuvée, mettre à jour le statut de présence
        if ($request->status === 'approved') {
            $attendance = $justification->attendance;
            $attendance->update([
                'status' => 'justified',
            ]);
        }
        
        return redirect()->route('justifications.index')
            ->with('success', 'Justification d\'absence traitée avec succès.');
    }

    /**
     * Supprimer une justification d'absence.
     */
    public function destroy(AbsenceJustification $justification)
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->isStudent()) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student || $justification->student_id !== $student->id) {
                return redirect()->route('dashboard')
                    ->with('error', 'Vous n\'avez pas accès à cette justification.');
            }
            
            // Un étudiant ne peut supprimer que les justifications en attente
            if (!$justification->isPending()) {
                return redirect()->route('justifications.index')
                    ->with('error', 'Vous ne pouvez pas supprimer une justification déjà traitée.');
            }
        } elseif (!$user->isAdmin() && !$user->isSuperAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas accès à cette justification.');
        }
        
        // Supprimer le document si présent
        if ($justification->document_path) {
            Storage::disk('public')->delete($justification->document_path);
        }
        
        $justification->delete();
        
        return redirect()->route('justifications.index')
            ->with('success', 'Justification d\'absence supprimée avec succès.');
    }
}
