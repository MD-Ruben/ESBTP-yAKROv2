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
     * Affiche le tableau de bord en fonction du rôle de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('superAdmin')) {
            return $this->superAdminDashboard();
        } elseif ($user->hasRole('secretaire')) {
            return $this->secretaireDashboard();
        } elseif ($user->hasRole('etudiant')) {
            return $this->etudiantDashboard();
        } elseif ($user->hasRole('parent')) {
            return $this->parentDashboard();
        }
        
        // Vue par défaut si aucun rôle spécifique n'est trouvé
        return view('dashboard.index', [
            'user' => $user
        ]);
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
        
        // Étudiants - Les secrétaires peuvent voir et créer des étudiants
        if ($user->can('view students')) {
            try {
                $data['totalStudents'] = ESBTPEtudiant::count();
                $data['recentStudents'] = ESBTPEtudiant::orderBy('created_at', 'desc')->take(5)->get();
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
            $data['classe'] = $etudiant->classe;
            if ($data['classe']) {
                $data['filiere'] = $data['classe']->filiere;
                $data['niveau'] = $data['classe']->niveauEtude;
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
        if ($user->can('view own timetable')) {
            try {
                $data['todayClasses'] = ESBTPEmploiTemps::where('classe_id', $etudiant->classe_id)
                    ->whereDate('date', today())
                    ->orderBy('heure_debut')
                    ->get();
            } catch (\Exception $e) {
                $data['todayClasses'] = collect();
            }
        }
        
        // Présences de l'étudiant
        if ($user->can('view own attendances')) {
            try {
                $data['attendanceStats'] = Attendance::where('etudiant_id', $etudiant->id)
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->get()
                    ->pluck('total', 'status')
                    ->toArray();
                
                $total = array_sum($data['attendanceStats']);
                $present = $data['attendanceStats']['present'] ?? 0;
                
                $data['attendancePercentage'] = $total > 0 ? round(($present / $total) * 100) : 0;
            } catch (\Exception $e) {
                $data['attendanceStats'] = [];
                $data['attendancePercentage'] = 0;
            }
        }
        
        // Notes récentes
        if ($user->can('view own grades')) {
            try {
                $data['recentGrades'] = Grade::where('etudiant_id', $etudiant->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $data['recentGrades'] = collect();
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
} 