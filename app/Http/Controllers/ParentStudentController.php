<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPParent;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPNote;
use App\Models\ESBTPAbsence;
use App\Models\ESBTPBulletin;
use App\Models\ESBTPPaiement;

class ParentStudentController extends Controller
{
    /**
     * Constructeur du contrôleur
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Affiche les détails d'un étudiant pour le parent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Vérifier que l'étudiant appartient au parent
        $etudiant = ESBTPEtudiant::where('id', $id)
            ->whereHas('parents', function($query) use ($parent) {
                $query->where('esbtp_parents.id', $parent->id);
            })->firstOrFail();
        
        // Récupérer les données statistiques
        $attendanceStats = $this->calculateAttendanceStats($etudiant);
        $gradeStats = $this->calculateGradeStats($etudiant);
        
        // Récupérer les dernières notes
        $recentGrades = ESBTPNote::where('etudiant_id', $etudiant->id)
            ->with(['evaluation', 'matiere'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Récupérer les dernières absences
        $recentAbsences = ESBTPAbsence::where('etudiant_id', $etudiant->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
        
        // Récupérer les derniers bulletins
        $bulletins = ESBTPBulletin::where('etudiant_id', $etudiant->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Récupérer les derniers paiements
        $payments = ESBTPPaiement::where('etudiant_id', $etudiant->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('parent.students.show', compact(
            'parent',
            'etudiant',
            'attendanceStats',
            'gradeStats',
            'recentGrades',
            'recentAbsences',
            'bulletins',
            'payments'
        ));
    }
    
    /**
     * Calcule les statistiques de présence pour un étudiant.
     *
     * @param  \App\Models\ESBTPEtudiant  $etudiant
     * @return array
     */
    private function calculateAttendanceStats($etudiant)
    {
        $totalAbsences = ESBTPAbsence::where('etudiant_id', $etudiant->id)->count();
        $justifiedAbsences = ESBTPAbsence::where('etudiant_id', $etudiant->id)
            ->where('justifie', true)
            ->count();
        $unjustifiedAbsences = $totalAbsences - $justifiedAbsences;
        
        // Calculer le pourcentage de présence
        $totalDays = ESBTPAbsence::where('etudiant_id', $etudiant->id)
            ->distinct('date')
            ->count('date');
        
        $presentDays = $totalDays - $totalAbsences;
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
        
        return [
            'totalAbsences' => $totalAbsences,
            'justifiedAbsences' => $justifiedAbsences,
            'unjustifiedAbsences' => $unjustifiedAbsences,
            'attendanceRate' => $attendanceRate
        ];
    }
    
    /**
     * Calcule les statistiques de notes pour un étudiant.
     *
     * @param  \App\Models\ESBTPEtudiant  $etudiant
     * @return array
     */
    private function calculateGradeStats($etudiant)
    {
        $notes = ESBTPNote::where('etudiant_id', $etudiant->id)->get();
        
        if ($notes->isEmpty()) {
            return [
                'averageGrade' => 0,
                'highestGrade' => 0,
                'lowestGrade' => 0,
                'totalGrades' => 0
            ];
        }
        
        $totalNotes = $notes->count();
        $sommeNotes = $notes->sum('note');
        $moyenneGenerale = round($sommeNotes / $totalNotes, 2);
        $noteMax = $notes->max('note');
        $noteMin = $notes->min('note');
        
        // Compter les notes par catégorie
        $excellentes = $notes->where('note', '>=', 16)->count();
        $bonnes = $notes->where('note', '>=', 14)->where('note', '<', 16)->count();
        $moyennes = $notes->where('note', '>=', 10)->where('note', '<', 14)->count();
        $insuffisantes = $notes->where('note', '<', 10)->count();
        
        return [
            'averageGrade' => $moyenneGenerale,
            'highestGrade' => $noteMax,
            'lowestGrade' => $noteMin,
            'totalGrades' => $totalNotes,
            'excellent' => $excellentes,
            'good' => $bonnes,
            'average' => $moyennes,
            'insufficient' => $insuffisantes
        ];
    }
} 