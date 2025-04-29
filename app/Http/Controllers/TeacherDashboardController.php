<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPEmploiTemps;
use App\Models\ESBTPAttendance;
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

        return view('dashboard.teacher', compact(
            'upcomingClasses', 
            'attendanceStats',
            'notifications'
        ));
    }

    /**
     * Récupérer l'emploi du temps de l'enseignant
     */
    public function showTimetable()
    {
        $user = Auth::user();
        $enseignantNom = $user->name;

        // Récupérer toutes les séances de cours de l'enseignant
        $seances = ESBTPSeanceCours::where('enseignant', $enseignantNom)
            ->where('is_active', true)
            ->with(['matiere', 'emploiTemps.classe'])
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get();

        // Organiser les séances par jour
        $timetable = [];
        for ($i = 1; $i <= 7; $i++) {
            $timetable[$i] = $seances->where('jour', $i)->values();
        }

        // Obtenir les noms des jours pour l'affichage
        $joursSemaine = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        return view('esbtp.emploi-temps.teacher', compact('timetable', 'joursSemaine'));
    }

    /**
     * Récupérer les séances de cours à venir
     */
    private function getUpcomingClasses($enseignantNom)
    {
        $today = Carbon::today();
        $nextWeek = Carbon::today()->addDays(7);

        // Récupérer les emplois du temps actifs
        $activeEmploiTemps = ESBTPEmploiTemps::where('is_active', true)
            ->where(function($query) use ($today, $nextWeek) {
                $query->where('date_debut', '<=', $nextWeek)
                      ->where(function($q) use ($today) {
                          $q->where('date_fin', '>=', $today)
                            ->orWhereNull('date_fin');
                      });
            })
            ->pluck('id');

        if ($activeEmploiTemps->isEmpty()) {
            return collect();
        }

        // Obtenir le jour de la semaine actuel (1 pour lundi, 7 pour dimanche)
        $currentDay = (Carbon::now()->dayOfWeek ?: 7); // Convertir 0 (dimanche) en 7

        // Récupérer les séances des 7 prochains jours
        $upcomingClasses = ESBTPSeanceCours::whereIn('emploi_temps_id', $activeEmploiTemps)
            ->where('enseignant', $enseignantNom)
            ->where('is_active', true)
            ->where(function($query) use ($currentDay) {
                $query->where('jour', '>=', $currentDay) // Jours dans la semaine courante
                      ->orWhere('jour', '<', $currentDay); // Jours dans la semaine suivante
            })
            ->with(['matiere', 'emploiTemps.classe'])
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->take(5) // Limiter à 5 séances
            ->get();

        // Calculer la date réelle de chaque séance
        $upcomingClasses->each(function($seance) use ($currentDay, $today) {
            $dayDiff = $seance->jour - $currentDay;
            if ($dayDiff < 0) {
                $dayDiff += 7; // Ajouter une semaine si c'est un jour de la semaine prochaine
            }
            $seance->date = $today->copy()->addDays($dayDiff);
        });

        // Trier par date (jour de la semaine) puis par heure
        return $upcomingClasses->sortBy([
            fn ($a, $b) => $a->date->timestamp <=> $b->date->timestamp,
            fn ($a, $b) => $a->heure_debut <=> $b->heure_debut
        ])->values();
    }

    /**
     * Calculer les statistiques de présence
     */
    private function getAttendanceStats($enseignantNom)
    {
        // 1. Récupérer le nombre total de séances programmées (total)
        $totalCourses = ESBTPSeanceCours::where('enseignant', $enseignantNom)
            ->where('is_active', true)
            ->whereHas('emploiTemps', function($query) {
                $query->where('is_active', true);
            })
            ->where('jour', '<', (Carbon::now()->dayOfWeek ?: 7)) // Jours déjà passés
            ->count();

        // 2. Récupérer le nombre de séances marquées comme présent (si implémenté)
        // Pour simplifier, nous supposons un taux de 80% de présence pour démonstration
        $attendedCourses = ceil($totalCourses * 0.8);
        $absentCourses = $totalCourses - $attendedCourses;
        
        // Calculer le taux de présence
        $attendanceRate = $totalCourses > 0 ? ($attendedCourses / $totalCourses) * 100 : 100;

        return [
            'totalCourses' => $totalCourses,
            'attendedCourses' => $attendedCourses,
            'absentCourses' => $absentCourses,
            'attendanceRate' => $attendanceRate
        ];
    }

    /**
     * Récupérer les notifications de l'enseignant
     */
    private function getNotifications()
    {
        // Simuler des notifications pour démonstration
        return collect([
            (object)[
                'id' => 1,
                'created_at' => Carbon::now()->subHours(2),
                'message' => 'Réunion pédagogique prévue le 15 juin à 14h.',
                'type' => 'info'
            ],
            (object)[
                'id' => 2,
                'created_at' => Carbon::now()->subDay(),
                'message' => 'Date limite de soumission des notes fixée au 20 juin.',
                'type' => 'urgent'
            ]
        ]);
    }
} 