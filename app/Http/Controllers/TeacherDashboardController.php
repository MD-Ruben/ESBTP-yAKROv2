<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPEmploiTemps;
use App\Models\ESBTPAttendance;
use App\Models\ESBTPNote;
use App\Models\ESBTPEvaluation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherDashboardController extends Controller
{
    /**
     * Constructeur avec middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:teacher|enseignant']);
    }

    /**
     * Afficher le tableau de bord de l'enseignant
     */
    public function index()
    {
        $user = Auth::user();
        $enseignantNom = $user->name;

        // 1. Récupérer les séances de cours à venir (pour les 7 prochains jours)
        $upcomingClasses = $this->getUpcomingClasses($enseignantNom);
        
        // 2. Calculer le taux de présence de l'enseignant
        $attendanceStats = $this->getAttendanceStats($enseignantNom);
        
        // 3. Récupérer les notifications (si implémentées)
        $notifications = $this->getNotifications();
        
        // 4. Définir les jours de la semaine en français pour l'affichage
        $joursSemaine = [
            0 => 'Lundi',
            1 => 'Mardi',
            2 => 'Mercredi',
            3 => 'Jeudi',
            4 => 'Vendredi',
            5 => 'Samedi', 
            6 => 'Dimanche'
        ];

        return view('dashboard.teacher', compact(
            'upcomingClasses', 
            'attendanceStats',
            'notifications',
            'joursSemaine'
        ));
    }

    /**
     * Afficher l'emploi du temps de l'enseignant
     */
    public function showTimetable()
    {
        $user = Auth::user();
        $enseignantNom = $user->name;
        
        // Récupérer toutes les séances de cours de l'enseignant
        $seances = ESBTPSeanceCours::where('enseignant', $enseignantNom)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->with(['emploiTemps.classe', 'matiere'])
            ->get();
        
        // Organiser les séances par jour
        $emploiTempsSemaine = [];
        foreach ([0, 1, 2, 3, 4, 5, 6] as $jour) {
            $emploiTempsSemaine[$jour] = $seances->where('jour', $jour)->sortBy('heure_debut');
        }
        
        // Définir les jours de la semaine en français pour l'affichage
        $joursSemaine = [
            0 => 'Lundi',
            1 => 'Mardi',
            2 => 'Mercredi',
            3 => 'Jeudi',
            4 => 'Vendredi',
            5 => 'Samedi', 
            6 => 'Dimanche'
        ];
        
        return view('teacher.timetable', compact('emploiTempsSemaine', 'joursSemaine', 'enseignantNom'));
    }

    /**
     * Afficher les notes saisies par l'enseignant
     */
    public function showGrades()
    {
        $user = Auth::user();
        $enseignantNom = $user->name;
        
        // Récupérer les évaluations créées par cet enseignant
        $evaluations = ESBTPEvaluation::where('enseignant', $enseignantNom)
            ->with(['matiere', 'classe'])
            ->orderBy('date', 'desc')
            ->paginate(10);
        
        // Récupérer les dernières notes saisies par cet enseignant
        $recentGrades = ESBTPNote::whereHas('evaluation', function($query) use ($enseignantNom) {
                $query->where('enseignant', $enseignantNom);
            })
            ->with(['etudiant', 'evaluation.matiere'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('teacher.grades', compact('evaluations', 'recentGrades', 'enseignantNom'));
    }

    /**
     * Afficher les présences enregistrées par l'enseignant
     */
    public function showAttendance()
    {
        $user = Auth::user();
        $enseignantNom = $user->name;
        
        // Récupérer les séances de cours pour lesquelles l'enseignant a enregistré des présences
        $seances = ESBTPSeanceCours::where('enseignant', $enseignantNom)
            ->whereHas('attendances')
            ->with(['emploiTemps.classe', 'matiere', 'attendances.etudiant'])
            ->orderBy('date', 'desc')
            ->paginate(10);
        
        // Récupérer les statistiques de présence par classe
        $classeStats = DB::table('esbtp_attendances')
            ->join('esbtp_seance_cours', 'esbtp_attendances.seance_cours_id', '=', 'esbtp_seance_cours.id')
            ->join('esbtp_emploi_temps', 'esbtp_seance_cours.emploi_temps_id', '=', 'esbtp_emploi_temps.id')
            ->join('esbtp_classes', 'esbtp_emploi_temps.classe_id', '=', 'esbtp_classes.id')
            ->where('esbtp_seance_cours.enseignant', $enseignantNom)
            ->select(
                'esbtp_classes.nom as classe',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN esbtp_attendances.status = "present" THEN 1 ELSE 0 END) as presents'),
                DB::raw('SUM(CASE WHEN esbtp_attendances.status = "absent" THEN 1 ELSE 0 END) as absents'),
                DB::raw('SUM(CASE WHEN esbtp_attendances.status = "late" THEN 1 ELSE 0 END) as retards')
            )
            ->groupBy('esbtp_classes.nom')
            ->get();
        
        return view('teacher.attendance', compact('seances', 'classeStats', 'enseignantNom'));
    }

    /**
     * Récupérer les séances de cours à venir pour l'enseignant
     */
    private function getUpcomingClasses($enseignantNom)
    {
        $today = Carbon::today();
        $inAWeek = Carbon::today()->addDays(7);
        
        try {
            return ESBTPSeanceCours::where('enseignant', $enseignantNom)
                ->whereBetween('date', [$today->format('Y-m-d'), $inAWeek->format('Y-m-d')])
                ->with(['matiere', 'emploiTemps.classe'])
                ->orderBy('date')
                ->orderBy('heure_debut')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des séances à venir: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Calculer les statistiques de présence pour l'enseignant
     */
    private function getAttendanceStats($enseignantNom)
    {
        try {
            $seances = ESBTPSeanceCours::where('enseignant', $enseignantNom)->get();
            $totalSeances = $seances->count();
            
            // Compter les séances où l'enseignant était présent (présence marquée)
            $presentSeances = $seances->filter(function($seance) {
                return $seance->presence_enseignant === true;
            })->count();
            
            // Calculer le taux de présence
            $attendanceRate = $totalSeances > 0 ? ($presentSeances / $totalSeances) * 100 : 0;
            
            return [
                'totalCourses' => $totalSeances,
                'attendedCourses' => $presentSeances,
                'absentCourses' => $totalSeances - $presentSeances,
                'attendanceRate' => $attendanceRate
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur lors du calcul des statistiques de présence: ' . $e->getMessage());
            return [
                'totalCourses' => 0,
                'attendedCourses' => 0,
                'absentCourses' => 0,
                'attendanceRate' => 0
            ];
        }
    }

    /**
     * Récupérer les notifications pour l'enseignant
     */
    private function getNotifications()
    {
        try {
            return \App\Models\Notification::where('user_id', Auth::id())
                ->orWhere(function($query) {
                    $query->where('recipient_type', 'teacher')
                        ->whereNull('recipient_id');
                })
                ->orWhere(function($query) {
                    $query->where('recipient_type', 'all');
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des notifications: ' . $e->getMessage());
            return collect();
        }
    }
} 