<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPParent;
use App\Models\ESBTPEtudiant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:parent');
    }

    /**
     * Affiche le tableau de bord du parent
     */
    public function index()
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre compte utilisateur n\'est pas associé à un parent.');
        }

        // Charger les étudiants associés au parent
        $parent->load(['etudiants', 'pupilles']);

        // Récupérer les notifications récentes
        $notifications = $user->notifications()->latest()->take(5)->get();

        // Récupérer les messages récents
        $messages = []; // À implémenter selon la structure de votre système de messagerie

        // Récupérer les actualités récentes
        $actualites = []; // À implémenter selon la structure de votre système d'actualités

        return view('dashboard.parent', compact('parent', 'notifications', 'messages', 'actualites'));
    }

    /**
     * Affiche les détails d'un étudiant pour le parent
     */
    public function showStudentDetails($etudiantId)
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->route('dashboard')
                ->with('error', 'Votre compte utilisateur n\'est pas associé à un parent.');
        }

        // Vérifier si l'étudiant est associé au parent
        $etudiant = $parent->etudiants()->findOrFail($etudiantId);

        // Charger les informations nécessaires
        $etudiant->load([
            'inscriptions' => function($q) {
                $q->with(['filiere', 'niveau', 'classe', 'anneeUniversitaire'])
                  ->orderBy('date_inscription', 'desc');
            },
            'absences' => function($q) {
                $q->latest()->take(10);
            },
            'notes' => function($q) {
                $q->with(['matiere', 'evaluation'])
                  ->latest()->take(10);
            }
        ]);

        // Récupérer l'inscription actuelle
        $inscriptionActuelle = $etudiant->inscriptions->first();
        
        // Récupérer les statistiques pertinentes
        $statsPresence = $this->calculateAttendanceStats($etudiant);
        $statsNotes = $this->calculateGradeStats($etudiant);

        return view('parent.student_details', compact(
            'parent', 
            'etudiant', 
            'inscriptionActuelle',
            'statsPresence',
            'statsNotes'
        ));
    }

    /**
     * Calcule les statistiques de présence pour un étudiant
     */
    private function calculateAttendanceStats($etudiant)
    {
        // Nombre total de jours de cours
        $totalJours = 0; // À calculer selon votre logique

        // Nombre d'absences
        $absences = $etudiant->absences->count();
        
        // Nombre d'absences justifiées
        $absencesJustifiees = $etudiant->absences->where('justifie', true)->count();
        
        // Taux de présence (en pourcentage)
        $tauxPresence = 0;
        if ($totalJours > 0) {
            $tauxPresence = round((($totalJours - $absences) / $totalJours) * 100);
        }
        
        return [
            'total_jours' => $totalJours,
            'absences' => $absences,
            'absences_justifiees' => $absencesJustifiees,
            'taux_presence' => $tauxPresence
        ];
    }

    /**
     * Calcule les statistiques de notes pour un étudiant
     */
    private function calculateGradeStats($etudiant)
    {
        // Récupérer toutes les notes
        $notes = $etudiant->notes;
        
        // Si aucune note, retourner des statistiques vides
        if ($notes->isEmpty()) {
            return [
                'moyenne_generale' => 0,
                'note_max' => 0,
                'note_min' => 0,
                'nombre_notes' => 0,
                'repartition' => [
                    'tres_bien' => 0, // >= 16
                    'bien' => 0,      // >= 14 et < 16
                    'assez_bien' => 0, // >= 12 et < 14
                    'passable' => 0,   // >= 10 et < 12
                    'insuffisant' => 0 // < 10
                ]
            ];
        }
        
        // Calculer la moyenne générale
        $moyenneGenerale = $notes->avg('note');
        
        // Trouver la note maximale et minimale
        $noteMax = $notes->max('note');
        $noteMin = $notes->min('note');
        
        // Calculer la répartition des notes
        $repartition = [
            'tres_bien' => $notes->filter(function ($note) { return $note->note >= 16; })->count(),
            'bien' => $notes->filter(function ($note) { return $note->note >= 14 && $note->note < 16; })->count(),
            'assez_bien' => $notes->filter(function ($note) { return $note->note >= 12 && $note->note < 14; })->count(),
            'passable' => $notes->filter(function ($note) { return $note->note >= 10 && $note->note < 12; })->count(),
            'insuffisant' => $notes->filter(function ($note) { return $note->note < 10; })->count()
        ];
        
        return [
            'moyenne_generale' => round($moyenneGenerale, 2),
            'note_max' => $noteMax,
            'note_min' => $noteMin,
            'nombre_notes' => $notes->count(),
            'repartition' => $repartition
        ];
    }

    /**
     * Affiche les paiements pour les étudiants du parent
     */
    public function payments()
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Charger les étudiants associés au parent
        $etudiants = $parent->etudiants;
        
        // Récupérer les IDs des étudiants
        $etudiantIds = $etudiants->pluck('id')->toArray();
        
        // Récupérer les paiements liés aux étudiants
        // Note: Il faudrait adapter cette partie selon votre modèle de paiement
        $paiements = []; // À implémenter selon la structure de votre système de paiement
        
        return view('parent.payments.index', compact('parent', 'etudiants', 'paiements'));
    }
    
    /**
     * Affiche les paramètres du compte parent
     */
    public function settings()
    {
        // Récupérer le parent connecté
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        return view('parent.settings.index', compact('parent', 'user'));
    }

    /**
     * Affiche la liste des notifications pour le parent
     */
    public function notifications()
    {
        // Cette méthode est conservée pour la compatibilité, mais redirige vers l'action du contrôleur dédié
        return redirect()->route('parent.notifications.index');
    }

    /**
     * Affiche la liste des messages pour le parent
     */
    public function messages()
    {
        // Cette méthode est conservée pour la compatibilité, mais redirige vers l'action du contrôleur dédié
        return redirect()->route('parent.messages.index');
    }
} 