<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Grade;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Timetable;
use App\Models\User;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPParent;
use App\Models\ESBTPClasse;
use App\Models\ESBTPFiliere;
use App\Models\ESBTPFormation;
use App\Models\ESBTPNiveauEtude;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPBulletin;
use App\Models\ESBTPEmploiTemps;
use App\Models\ESBTPAnnonce;
use App\Models\ESBTPSeanceCours;
use App\Models\ESBTPAttendance;
use App\Models\ESBTPNote;
use App\Models\ESBTPAnneeUniversitaire;
use App\Models\ESBTPMessage;
use App\Models\ESBTPStudent;
use App\Models\ESBTPAcademicYear;
use App\Models\ESBTPExam;
use App\Models\ESBTPGrade;
use App\Models\ESBTPSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Constructeur qui applique le middleware auth.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche le tableau de bord principal.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Vérifier si l'utilisateur est un super admin
        if ($user->hasRole('superAdmin')) {
            return $this->superAdminDashboard();
        }
        
        // Vérifier si l'utilisateur est un secrétaire
        if ($user->hasRole('secretaire')) {
            return $this->secretaireDashboard();
        }
        
        // Vérifier si l'utilisateur est un enseignant
        if ($user->hasRole(['teacher', 'enseignant'])) {
            return redirect()->route('teacher.dashboard');
        }
        
        // Vérifier si l'utilisateur est un étudiant
        if ($user->hasRole('etudiant')) {
            return $this->etudiantDashboard();
        }

        // Si aucun rôle spécifique n'est trouvé, afficher un tableau de bord générique
        return view('dashboard.index');
    }

    /**
     * Tableau de bord pour les super administrateurs.
     */
    private function superAdminDashboard()
    {
        $user = Auth::user();
        $data = [
            'user' => $user,
            'totalUsers' => User::count()
        ];

        // Inscriptions en attente
        $data['pendingInscriptionsCount'] = \App\Models\ESBTPInscription::where('status', 'pending')->count();

        // Sections selon les permissions

        // Étudiants
        if ($user->can('view students')) {
            $data['totalStudents'] = ESBTPEtudiant::count();
            $data['recentStudents'] = ESBTPEtudiant::orderBy('created_at', 'desc')->take(5)->get();
        }

        // Secrétaires
        if ($user->can('view users')) {
            $data['totalSecretaires'] = User::role('secretaire')->count();
        }

        // Filières
        if ($user->can('view filieres')) {
            try {
                $data['totalFilieres'] = ESBTPFiliere::count();
                $data['recentFilieres'] = ESBTPFiliere::orderBy('created_at', 'desc')->take(5)->get();
            } catch (\Exception $e) {
                $data['totalFilieres'] = 0;
                $data['recentFilieres'] = collect();
            }
        }

        // Formations
        if ($user->can('view formations')) {
            try {
                $data['totalFormations'] = ESBTPFormation::count();
            } catch (\Exception $e) {
                $data['totalFormations'] = 0;
            }
        }

        // Niveaux d'études
        if ($user->can('view niveaux etudes')) {
            try {
                $data['totalNiveaux'] = ESBTPNiveauEtude::count();
            } catch (\Exception $e) {
                $data['totalNiveaux'] = 0;
            }
        }

        // Classes
        if ($user->can('view classes')) {
            try {
                $data['totalClasses'] = ESBTPClasse::count();
            } catch (\Exception $e) {
                $data['totalClasses'] = 0;
            }
        }

        // Matières
        if ($user->can('view matieres')) {
            try {
                $data['totalMatieres'] = ESBTPMatiere::count();
            } catch (\Exception $e) {
                $data['totalMatieres'] = 0;
            }
        }

        // Examens
        if ($user->can('view exams')) {
            try {
                $data['totalExamens'] = ESBTPEvaluation::count();
                $data['upcomingExamens'] = ESBTPEvaluation::where('date', '>=', now())
                    ->orderBy('date')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['totalExamens'] = 0;
                $data['upcomingExamens'] = collect();
            }
        }

        // Bulletins
        if ($user->can('view bulletins')) {
            try {
                $data['totalBulletins'] = ESBTPBulletin::count();
            } catch (\Exception $e) {
                $data['totalBulletins'] = 0;
            }
        }

        // Emplois du temps
        if ($user->can('view timetables')) {
            try {
                $data['totalEmploisTemps'] = ESBTPEmploiTemps::count();
            } catch (\Exception $e) {
                $data['totalEmploisTemps'] = 0;
            }
        }

        // Présences
        if ($user->can('view attendances')) {
            try {
                $data['todayAttendances'] = Attendance::whereDate('date', today())->count();
                $data['pendingAttendances'] = Attendance::whereDate('date', today())
                    ->whereNull('status')
                    ->count();
            } catch (\Exception $e) {
                $data['todayAttendances'] = 0;
                $data['pendingAttendances'] = 0;
            }
        }

        // Messages
        if ($user->can('receive messages')) {
            try {
                $data['recentMessages'] = Message::where(function($query) {
                        $query->where('recipient_type', 'admins')
                            ->whereNull('recipient_group');
                    })
                    ->orWhere(function($query) {
                        $query->where('recipient_type', 'all')
                            ->whereNull('recipient_group');
                    })
                    ->orWhere('recipient_id', Auth::id())
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentMessages'] = collect();
            }
        }

        // Notifications
        try {
            $data['recentNotifications'] = Notification::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $data['recentNotifications'] = collect();
        }

        return view('dashboard.superadmin', $data);
    }

    /**
     * Tableau de bord pour les secrétaires.
     */
    private function secretaireDashboard()
    {
        $user = Auth::user();
        $data = [
            'user' => $user
        ];

        // Inscriptions en attente
        $data['pendingInscriptionsCount'] = \App\Models\ESBTPInscription::where('status', 'pending')->count();

        // Étudiants
        if ($user->can('view students')) {
            try {
                $data['totalStudents'] = ESBTPEtudiant::count();
                $data['recentStudents'] = ESBTPEtudiant::with(['inscriptions' => function($q) {
                        $q->orderBy('created_at', 'desc');
                    }])
                    ->whereHas('inscriptions')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['totalStudents'] = 0;
                $data['recentStudents'] = collect();
            }
        }

        // Présences - Les secrétaires peuvent gérer les présences
        if ($user->can('view attendances')) {
            try {
                $data['todayAttendances'] = Attendance::whereDate('date', today())->count();
                $data['pendingJustifications'] = Attendance::whereDate('date', '>=', now()->subDays(7))
                    ->where('status', 'absent')
                    ->where('justified', false)
                    ->count();
            } catch (\Exception $e) {
                $data['todayAttendances'] = 0;
                $data['pendingJustifications'] = 0;
            }
        }

        // Emplois du temps - Les secrétaires peuvent créer et consulter les emplois du temps
        if ($user->can('view timetables')) {
            try {
                $data['totalTimetables'] = ESBTPEmploiTemps::count();
                $data['todayClasses'] = ESBTPEmploiTemps::whereDate('date', today())->count();
            } catch (\Exception $e) {
                $data['totalTimetables'] = 0;
                $data['todayClasses'] = 0;
            }
        }

        // Bulletins - Les secrétaires peuvent générer et consulter les bulletins
        if ($user->can('view bulletins')) {
            try {
                $data['pendingBulletins'] = ESBTPBulletin::where('status', 'pending')->count();
                $data['totalBulletins'] = ESBTPBulletin::count();
            } catch (\Exception $e) {
                $data['pendingBulletins'] = 0;
                $data['totalBulletins'] = 0;
            }
        }

        // Messages récents adressés aux secrétaires
        if ($user->can('receive messages')) {
            try {
                $data['recentMessages'] = Message::where(function($query) {
                        $query->where('recipient_type', 'secretaires')
                            ->whereNull('recipient_group');
                    })
                    ->orWhere(function($query) {
                        $query->where('recipient_type', 'all')
                            ->whereNull('recipient_group');
                    })
                    ->orWhere('recipient_id', Auth::id())
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentMessages'] = collect();
            }
        }

        return view('dashboard.secretaire', $data);
    }

    /**
     * Tableau de bord pour les étudiants.
     */
    private function etudiantDashboard()
    {
        $user = Auth::user();

        // Récupérer les informations de l'étudiant
        try {
            $etudiant = ESBTPEtudiant::where('user_id', $user->id)->firstOrFail();
        } catch (\Exception $e) {
            // Rediriger vers une page de configuration si le profil étudiant n'existe pas
            return view('dashboard.etudiant_setup', ['user' => $user]);
        }

        $data = [
            'user' => $user,
            'etudiant' => $etudiant
        ];

        // Récupérer la classe, filière et niveau d'étude si disponibles
        try {
            $data['classe'] = $etudiant->classe_active;
            if ($data['classe']) {
                $data['filiere'] = $data['classe']->filiere;
                $data['niveau'] = $data['classe']->niveau;
            }
        } catch (\Exception $e) {
            $data['classe'] = null;
            $data['filiere'] = null;
            $data['niveau'] = null;
        }

        // Notifications non lues
        try {
            $data['unreadNotifications'] = Notification::where('user_id', $user->id)
                ->where('read', false)
                ->count();
        } catch (\Exception $e) {
            $data['unreadNotifications'] = 0;
        }

        // Examens à venir si l'étudiant peut voir ses examens
        if ($user->can('view own exams')) {
            try {
                $data['upcomingExams'] = ESBTPEvaluation::where('date', '>=', now())
                    ->whereHas('classe', function($query) use ($etudiant) {
                        $query->where('id', $etudiant->classe_id);
                    })
                    ->orderBy('date')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['upcomingExams'] = collect();
            }
        }

        // Emploi du temps de l'étudiant
        if ($user->can('view own timetable') && $data['classe']) {
            try {
                // Récupérer l'emploi du temps actif de la classe
                $emploiTemps = ESBTPEmploiTemps::where('classe_id', $data['classe']->id)
                    ->where('is_active', true)
                    ->where('is_current', true)
                    ->first();

                if ($emploiTemps) {
                    // Récupérer le jour de la semaine actuel (0 = Lundi, 6 = Dimanche)
                    $jourSemaine = (now()->dayOfWeek + 6) % 7;

                    // Récupérer toutes les séances et les organiser par jour
                    $seances = [];
                    $allSeances = ESBTPSeanceCours::where('emploi_temps_id', $emploiTemps->id)
                        ->with(['matiere', 'enseignant'])
                        ->orderBy('heure_debut')
                        ->get();

                    foreach ($allSeances as $seance) {
                        if (!isset($seances[$seance->jour])) {
                            $seances[$seance->jour] = [];
                        }
                        $seances[$seance->jour][] = $seance;
                    }

                    $data['seances'] = $seances;
                    $data['emploiTemps'] = $emploiTemps;
                    $data['seancesDuJour'] = $seances[$jourSemaine] ?? [];

                    // Ajouter des informations de debug
                    \Log::info('Emploi du temps trouvé', [
                        'emploi_temps_id' => $emploiTemps->id,
                        'jour_semaine' => $jourSemaine,
                        'nombre_seances' => count($data['seancesDuJour'])
                    ]);
                } else {
                    $data['seances'] = [];
                    $data['seancesDuJour'] = [];
                    \Log::warning('Aucun emploi du temps actif trouvé pour la classe', [
                        'classe_id' => $data['classe']->id
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la récupération des séances', [
                    'error' => $e->getMessage(),
                    'classe_id' => $data['classe']->id ?? null
                ]);
                $data['seances'] = [];
                $data['seancesDuJour'] = [];
            }
        } else {
            $data['seances'] = [];
            $data['seancesDuJour'] = [];
        }

        // Présences de l'étudiant
        if ($user->can('view own attendances')) {
            try {
                $data['attendanceStats'] = ESBTPAttendance::where('etudiant_id', $etudiant->id)
                    ->selectRaw('statut, count(*) as total')
                    ->groupBy('statut')
                    ->get()
                    ->pluck('total', 'statut')
                    ->toArray();

                $total = array_sum($data['attendanceStats']);
                $present = ($data['attendanceStats']['present'] ?? 0) + ($data['attendanceStats']['retard'] ?? 0);

                $data['attendancePercentage'] = $total > 0 ? round(($present / $total) * 100) : 0;
            } catch (\Exception $e) {
                $data['attendanceStats'] = [];
                $data['attendancePercentage'] = 0;
                \Log::error('Erreur lors de la récupération des statistiques de présence', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Notes récentes
        if ($user->can('view own grades')) {
            try {
                $data['recentGrades'] = ESBTPNote::where('etudiant_id', $etudiant->id)
                    ->with(['evaluation.matiere'])
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentGrades'] = collect();
                \Log::error('Erreur lors de la récupération des notes récentes', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return view('dashboard.etudiant', $data);
    }

    /**
     * Tableau de bord pour les parents.
     */
    private function parentDashboard()
    {
        $user = Auth::user();

        // Récupérer les informations du parent
        try {
            $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        } catch (\Exception $e) {
            // Rediriger vers une page de configuration si le profil parent n'existe pas
            return view('dashboard.parent_setup', ['user' => $user]);
        }

        $data = [
            'user' => $user,
            'parent' => $parent
        ];

        // Récupérer les étudiants liés à ce parent
        try {
            $data['etudiants'] = $parent->etudiants;
            $data['totalEtudiants'] = $data['etudiants']->count();
        } catch (\Exception $e) {
            $data['etudiants'] = collect();
            $data['totalEtudiants'] = 0;
        }

        // Notifications non lues
        try {
            $data['unreadNotifications'] = Notification::where('user_id', $user->id)
                ->where('read', false)
                ->count();
        } catch (\Exception $e) {
            $data['unreadNotifications'] = 0;
        }

        // Bulletins récents des enfants
        if ($user->can('view children bulletins')) {
            try {
                $etudiantIds = $data['etudiants']->pluck('id')->toArray();
                $data['recentBulletins'] = ESBTPBulletin::whereIn('etudiant_id', $etudiantIds)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentBulletins'] = collect();
            }
        }

        // Présences des enfants
        if ($user->can('view children attendances')) {
            try {
                $etudiantIds = $data['etudiants']->pluck('id')->toArray();
                $data['recentAbsences'] = Attendance::whereIn('etudiant_id', $etudiantIds)
                    ->where('status', 'absent')
                    ->orderBy('date', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentAbsences'] = collect();
            }
        }

        // Messages adressés au parent
        if ($user->can('receive messages')) {
            try {
                $data['recentMessages'] = Message::where(function($query) {
                        $query->where('recipient_type', 'parents')
                            ->whereNull('recipient_group');
                    })
                    ->orWhere(function($query) {
                        $query->where('recipient_type', 'all')
                            ->whereNull('recipient_group');
                    })
                    ->orWhere('recipient_id', Auth::id())
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentMessages'] = collect();
            }
        }

        return view('dashboard.parent', $data);
    }

    /**
     * Affiche le tableau de bord de l'étudiant
     */
    public function studentDashboard()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Vous n\'êtes pas un étudiant');
        }

        // Récupérer l'année académique actuelle ou la plus récente
        $academicYear = ESBTPAcademicYear::where('is_current', true)->first()
            ?? ESBTPAcademicYear::orderBy('end_date', 'desc')->first();

        // Définir les dates par défaut (toute l'année académique)
        $defaultStartDate = $academicYear ? $academicYear->start_date : now()->startOfYear();
        $defaultEndDate = $academicYear ? $academicYear->end_date : now()->endOfYear();

        // Récupérer les présences/absences de l'étudiant
        $allAttendances = ESBTPAttendance::with(['seanceCours.matiere', 'seanceCours.schedule'])
            ->where('student_id', $student->id)
            ->whereBetween('date', [$defaultStartDate, $defaultEndDate])
            ->get();

        $absences = $allAttendances->where('statut', 'absent');
        $presences = $allAttendances->where('statut', 'present');
        $retards = $allAttendances->where('statut', 'retard');
        $excuses = $allAttendances->where('statut', 'excuse');

        // Récupérer les dernières absences pour affichage
        $recentAbsences = $absences->sortByDesc('date')->take(5);

        // Récupérer les prochains examens
        $upcomingExams = ESBTPExam::whereHas('course', function ($query) use ($student) {
            $query->whereHas('classe', function ($q) use ($student) {
                $q->where('id', $student->classe_id);
            });
        })
        ->where('date', '>=', now())
        ->orderBy('date', 'asc')
        ->take(5)
        ->get();

        // Récupérer les dernières notes
        $recentGrades = ESBTPGrade::where('student_id', $student->id)
            ->with(['exam.course'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Récupérer l'emploi du temps pour aujourd'hui
        $today = now()->format('Y-m-d');
        $dayOfWeek = now()->dayOfWeek;

        $todaySchedule = ESBTPSchedule::whereHas('classe', function ($query) use ($student) {
            $query->where('id', $student->classe_id);
        })
        ->where('day_of_week', $dayOfWeek)
        ->with(['course.teacher', 'course.subject'])
        ->orderBy('start_time')
        ->get();

        return view('dashboard.etudiant', compact(
            'student',
            'presences',
            'absences',
            'retards',
            'excuses',
            'recentAbsences',
            'upcomingExams',
            'recentGrades',
            'todaySchedule'
        ));
    }
}
