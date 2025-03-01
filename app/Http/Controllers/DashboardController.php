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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
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
        $totalStudents = Student::count();
        $totalSecretaires = User::role('secretaire')->count();
        $totalUsers = User::count();
        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Statistiques des filières, formations et niveaux d'études
        try {
            $totalFilieres = \App\Models\Filiere::count();
            $totalNiveaux = \App\Models\NiveauEtude::count();
            $totalFormations = \App\Models\Formation::count() ?? 0;
            $totalClasses = \App\Models\Classe::count() ?? 0;
        } catch (\Exception $e) {
            $totalFilieres = 0;
            $totalNiveaux = 0;
            $totalFormations = 0;
            $totalClasses = 0;
        }
            
        // Récupérer les notifications récentes seulement si la table existe
        try {
            $recentNotifications = Notification::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $recentNotifications = collect(); // Collection vide si erreur
        }
            
        // Récupérer les messages récents seulement si la table existe
        try {
            $recentMessages = Message::where(function($query) {
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
            $recentMessages = collect(); // Collection vide si erreur
        }
        
        // Statistiques de présence seulement si la table existe
        try {
            $attendanceStats = Attendance::selectRaw('DATE(date) as attendance_date, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
                ->groupBy('attendance_date')
                ->orderBy('attendance_date', 'desc')
                ->take(7)
                ->get();
        } catch (\Exception $e) {
            $attendanceStats = collect([
                [
                    'attendance_date' => now()->format('Y-m-d'),
                    'total' => 0,
                    'present' => 0
                ]
            ]); // Collection avec des données vides pour éviter les erreurs d'affichage
        }
        
        return view('dashboard.superadmin', compact(
            'totalStudents', 
            'totalSecretaires',
            'totalUsers',
            'recentUsers',
            'totalFilieres',
            'totalNiveaux',
            'totalFormations',
            'totalClasses',
            'recentNotifications',
            'recentMessages',
            'attendanceStats'
        ));
    }
    
    /**
     * Tableau de bord pour les secrétaires.
     */
    private function secretaireDashboard()
    {
        $totalStudents = Student::count();
        
        // Récupérer les présences d'aujourd'hui seulement si la table existe
        try {
            $todayAttendances = Attendance::whereDate('date', today())->count();
            $pendingAttendances = Attendance::whereDate('date', today())
                ->whereNull('status')
                ->count();
        } catch (\Exception $e) {
            $todayAttendances = 0;
            $pendingAttendances = 0;
        }
        
        // Récupérer les notifications récentes
        try {
            $recentNotifications = Notification::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $recentNotifications = collect(); // Collection vide si erreur
        }
            
        // Récupérer les messages récents
        try {
            $recentMessages = Message::where(function($query) {
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
            $recentMessages = collect(); // Collection vide si erreur
        }
        
        return view('dashboard.secretaire', compact(
            'totalStudents',
            'todayAttendances',
            'pendingAttendances',
            'recentNotifications',
            'recentMessages'
        ));
    }
    
    /**
     * Tableau de bord pour les étudiants.
     */
    private function etudiantDashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            // Au lieu de rediriger, afficher une vue spéciale pour les étudiants sans profil
            return view('dashboard.etudiant_setup', [
                'user' => $user
            ]);
        }
        
        // Récupérer l'emploi du temps d'aujourd'hui
        try {
            $todayTimetable = Timetable::where('class_id', $student->class_id)
                ->where('day', strtolower(date('l')))
                ->orderBy('start_time')
                ->with(['subject', 'class', 'teacher'])
                ->get();
        } catch (\Exception $e) {
            $todayTimetable = collect(); // Collection vide si erreur
        }
        
        // Récupérer les notifications récentes
        try {
            $recentNotifications = Notification::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere(function($q) {
                            $q->whereNull('user_id')
                            ->where(function($sq) {
                                $sq->where('type', 'info')
                                    ->orWhere('type', 'warning')
                                    ->orWhere('type', 'success');
                            });
                        });
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            // Compter les notifications non lues
            $unreadNotifications = Notification::where(function($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->orWhere(function($q) {
                            $q->whereNull('user_id')
                            ->where(function($sq) {
                                $sq->where('type', 'info')
                                    ->orWhere('type', 'warning')
                                    ->orWhere('type', 'success');
                            });
                        });
                })
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            $recentNotifications = collect(); // Collection vide si erreur
            $unreadNotifications = 0;
        }
        
        // Récupérer les derniers examens et notes
        try {
            $recentExams = \App\Models\Exam::where('class_id', $student->class_id)
                ->orderBy('date', 'desc')
                ->take(3)
                ->get();
                
            $recentGrades = Grade::where('student_id', $student->id)
                ->with(['exam', 'subject'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $recentExams = collect(); // Collection vide si erreur
            $recentGrades = collect(); // Collection vide si erreur
        }
        
        return view('dashboard.etudiant', compact(
            'student',
            'todayTimetable',
            'recentNotifications',
            'unreadNotifications',
            'recentExams',
            'recentGrades'
        ));
    }
} 