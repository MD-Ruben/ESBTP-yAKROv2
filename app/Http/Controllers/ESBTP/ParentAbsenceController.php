<?php

namespace App\Http\Controllers\ESBTP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ESBTPParent;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPAbsence;

class ParentAbsenceController extends Controller
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Affiche la liste des absences pour un étudiant spécifique.
     *
     * @param  int  $etudiantId
     * @return \Illuminate\Http\Response
     */
    public function index($etudiantId)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Vérifier que l'étudiant appartient au parent
        $etudiant = ESBTPEtudiant::where('id', $etudiantId)
            ->whereHas('parents', function($query) use ($parent) {
                $query->where('esbtp_parents.id', $parent->id);
            })->firstOrFail();
        
        // Récupérer les absences de l'étudiant
        $absences = ESBTPAbsence::where('etudiant_id', $etudiantId)
            ->orderBy('date', 'desc')
            ->paginate(10);
        
        return view('parent.absences.index', compact('etudiant', 'absences'));
    }

    /**
     * Affiche les détails d'une absence spécifique.
     *
     * @param  int  $etudiantId
     * @param  int  $absenceId
     * @return \Illuminate\Http\Response
     */
    public function show($etudiantId, $absenceId)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Vérifier que l'étudiant appartient au parent
        $etudiant = ESBTPEtudiant::where('id', $etudiantId)
            ->whereHas('parents', function($query) use ($parent) {
                $query->where('esbtp_parents.id', $parent->id);
            })->firstOrFail();
        
        // Récupérer l'absence
        $absence = ESBTPAbsence::where('id', $absenceId)
            ->where('etudiant_id', $etudiantId)
            ->firstOrFail();
        
        return view('parent.absences.show', compact('etudiant', 'absence'));
    }

    /**
     * Affiche le formulaire pour justifier une absence.
     *
     * @param  int  $etudiantId
     * @param  int  $absenceId
     * @return \Illuminate\Http\Response
     */
    public function edit($etudiantId, $absenceId)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Vérifier que l'étudiant appartient au parent
        $etudiant = ESBTPEtudiant::where('id', $etudiantId)
            ->whereHas('parents', function($query) use ($parent) {
                $query->where('esbtp_parents.id', $parent->id);
            })->firstOrFail();
        
        // Récupérer l'absence
        $absence = ESBTPAbsence::where('id', $absenceId)
            ->where('etudiant_id', $etudiantId)
            ->firstOrFail();
        
        // Vérifier si l'absence est déjà justifiée
        if ($absence->justifie) {
            return redirect()->route('parent.absences.show', ['etudiant_id' => $etudiantId, 'absence_id' => $absenceId])
                ->with('info', 'Cette absence est déjà justifiée.');
        }
        
        return view('parent.absences.edit', compact('etudiant', 'absence'));
    }

    /**
     * Met à jour l'absence avec la justification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $etudiantId
     * @param  int  $absenceId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $etudiantId, $absenceId)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Vérifier que l'étudiant appartient au parent
        $etudiant = ESBTPEtudiant::where('id', $etudiantId)
            ->whereHas('parents', function($query) use ($parent) {
                $query->where('esbtp_parents.id', $parent->id);
            })->firstOrFail();
        
        // Récupérer l'absence
        $absence = ESBTPAbsence::where('id', $absenceId)
            ->where('etudiant_id', $etudiantId)
            ->firstOrFail();
        
        // Valider la requête
        $request->validate([
            'motif' => 'required|string|max:255',
            'commentaire' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        // Traiter le document de justification
        if ($request->hasFile('document')) {
            if ($absence->document_justificatif) {
                Storage::delete('public/' . $absence->document_justificatif);
            }
            
            $path = $request->file('document')->store('absences/justificatifs', 'public');
            $absence->document_justificatif = $path;
        }
        
        // Mettre à jour l'absence
        $absence->motif = $request->motif;
        $absence->commentaire = $request->commentaire;
        $absence->justifie = true; // La justification doit être validée par l'administration
        $absence->updated_by = $user->id;
        $absence->save();
        
        return redirect()->route('parent.absences.show', ['etudiant_id' => $etudiantId, 'absence_id' => $absenceId])
            ->with('success', 'La justification a été soumise avec succès. Elle sera examinée par l\'administration.');
    }

    /**
     * Affiche le résumé des absences pour tous les étudiants du parent.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les étudiants associés au parent
        $etudiants = ESBTPEtudiant::whereHas('parents', function($query) use ($parent) {
            $query->where('esbtp_parents.id', $parent->id);
        })->get();
        
        // Préparer les statistiques d'absence pour chaque étudiant
        $absenceStats = [];
        foreach ($etudiants as $etudiant) {
            $totalAbsences = ESBTPAbsence::where('etudiant_id', $etudiant->id)->count();
            $justifiedAbsences = ESBTPAbsence::where('etudiant_id', $etudiant->id)
                ->where('justifie', true)
                ->count();
            $unjustifiedAbsences = $totalAbsences - $justifiedAbsences;
            
            // Calculer le taux de présence
            $totalDays = ESBTPAbsence::where('etudiant_id', $etudiant->id)
                ->distinct('date')
                ->count('date');
            
            $presentDays = $totalDays - $unjustifiedAbsences;
            $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 100;
            
            $absenceStats[$etudiant->id] = [
                'total' => $totalAbsences,
                'justified' => $justifiedAbsences,
                'unjustified' => $unjustifiedAbsences,
                'attendance_rate' => $attendanceRate,
                'recent_absences' => ESBTPAbsence::where('etudiant_id', $etudiant->id)
                    ->orderBy('date', 'desc')
                    ->take(3)
                    ->get()
            ];
        }
        
        return view('parent.absences.summary', compact('etudiants', 'absenceStats'));
    }
} 