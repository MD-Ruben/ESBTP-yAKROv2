<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Grade;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Teacher;
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
        
        if ($user->isSuperAdmin()) {
            return $this->superAdminDashboard();
        } elseif ($user->user_type === 'admin') {
            return $this->adminDashboard();
        } elseif ($user->isTeacher()) {
            return $this->teacherDashboard();
        } elseif ($user->isStudent()) {
            return $this->studentDashboard();
        } elseif ($user->isParent()) {
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
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalUsers = User::count();
        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentCertificates = Certificate::with(['student.user', 'certificateType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $recentNotifications = Notification::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Récupérer les messages récents
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
        
        // Statistiques de présence
        $attendanceStats = Attendance::selectRaw('DATE(date) as attendance_date, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc')
            ->take(7)
            ->get();
            
        // Récupérer toutes les classes pour le formulaire de messagerie
        $classes = \App\Models\SchoolClass::all();
        
        // Récupérer tous les étudiants pour le formulaire de messagerie
        $students = Student::with('user')->get();
        
        return view('dashboard.superadmin', compact(
            'totalStudents', 
            'totalTeachers',
            'totalAdmins',
            'totalUsers',
            'recentUsers',
            'recentCertificates', 
            'recentNotifications',
            'recentMessages',
            'attendanceStats',
            'classes',
            'students'
        ));
    }
    
    /**
     * Tableau de bord pour les administrateurs.
     */
    private function adminDashboard()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        
        // Récupérer les présences d'aujourd'hui
        $todayAttendances = Attendance::whereDate('date', today())->count();
        $pendingAttendances = Attendance::whereDate('date', today())
            ->whereNull('status')
            ->count();
        
        // Récupérer les notifications récentes
        $recentNotifications = Notification::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Récupérer les messages récents
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
        
        return view('dashboard.admin', compact(
            'totalStudents', 
            'totalTeachers',
            'todayAttendances',
            'pendingAttendances',
            'recentNotifications',
            'recentMessages'
        ));
    }
    
    /**
     * Tableau de bord pour les enseignants.
     */
    private function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        if (!$teacher) {
            // Au lieu de rediriger, afficher une vue spéciale pour les enseignants sans profil
            return view('dashboard.teacher_setup', [
                'user' => $user
            ]);
        }
        
        // Récupérer l'emploi du temps d'aujourd'hui
        $todayTimetable = Timetable::where('teacher_id', $teacher->id)
            ->where('day', strtolower(date('l')))
            ->orderBy('start_time')
            ->with(['subject', 'class', 'section'])
            ->get();
        
        // Récupérer les notifications récentes
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
        
        // Compter le nombre de classes enseignées
        $classesTaught = Timetable::where('teacher_id', $teacher->id)
            ->distinct('class_id')
            ->count('class_id');
        
        // Compter le nombre total d'étudiants dans les classes enseignées
        $classIds = Timetable::where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('class_id');
        
        $totalStudents = Student::whereIn('class_id', $classIds)->count();
        
        return view('dashboard.teacher', compact(
            'teacher',
            'todayTimetable',
            'recentNotifications',
            'unreadNotifications',
            'classesTaught',
            'totalStudents'
        ));
    }
    
    /**
     * Tableau de bord pour les étudiants.
     */
    private function studentDashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            // Au lieu de rediriger, afficher une vue spéciale pour les étudiants sans profil
            return view('dashboard.student_setup', [
                'user' => $user
            ]);
        }
        
        $todayClasses = Timetable::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('day', strtolower(date('l')))
            ->orderBy('start_time')
            ->get();
        
        $recentGrades = Grade::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        $attendanceStats = Attendance::where('student_id', $student->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        
        $totalAttendance = array_sum($attendanceStats);
        $attendancePercentage = $totalAttendance > 0 
            ? round(($attendanceStats['present'] ?? 0) / $totalAttendance * 100) 
            : 0;
        
        $notifications = Notification::where(function($query) use ($student) {
                $query->where('recipient_id', $student->id)
                    ->orWhere('recipient_type', 'all')
                    ->orWhere('recipient_type', 'students');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard.student', compact(
            'student',
            'todayClasses',
            'recentGrades',
            'attendancePercentage',
            'notifications'
        ));
    }
    
    /**
     * Tableau de bord pour les parents.
     */
    public function parentDashboard()
    {
        $user = Auth::user();
        $guardian = $user->guardian;
        
        if (!$guardian) {
            // Au lieu de rediriger, afficher une vue spéciale pour les parents sans profil
            return view('dashboard.parent_setup', [
                'user' => $user
            ]);
        }
        
        $children = Student::where('guardian_id', $guardian->id)->get();
        
        $notifications = Notification::where(function($query) {
                $query->where('recipient_type', 'all')
                    ->orWhere('recipient_type', 'parents');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard.parent', compact(
            'guardian',
            'children',
            'notifications'
        ));
    }
    
    /**
     * Liste des enfants pour un parent.
     */
    public function children()
    {
        $user = Auth::user();
        $guardian = $user->guardian;
        
        if (!$guardian) {
            return redirect()->route('dashboard')
                ->with('error', 'Profil parent non trouvé.');
        }
        
        $children = Student::where('guardian_id', $guardian->id)
            ->with(['class', 'section', 'user'])
            ->get();
        
        return view('parent.children', compact('children'));
    }
    
    /**
     * Affiche la page de profil du parent.
     */
    public function parentProfile()
    {
        $user = Auth::user();
        $parent = \App\Models\ParentModel::where('user_id', $user->id)->first();
        
        // Si le parent n'a pas encore de profil complet, afficher une vue de configuration
        if (!$parent) {
            return view('dashboard.parent_setup', [
                'user' => $user
            ]);
        }
        
        // Récupérer les enfants associés à ce parent
        $children = Student::whereHas('parents', function($query) use ($parent) {
            $query->where('parent_id', $parent->id);
        })->with(['user', 'class'])->get();
        
        return view('parent.profile', [
            'user' => $user,
            'parent' => $parent,
            'children' => $children
        ]);
    }
    
    /**
     * Met à jour le profil de l'utilisateur.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        // Mise à jour des informations de base
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        
        // Traitement de l'image de profil si fournie
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile-images', 'public');
            $user->profile_image = $imagePath;
        }
        
        // Mise à jour du mot de passe si fourni
        if ($request->filled('password')) {
            $user->password = $request->password;
        }
        
        $user->save();
        
        // Redirection en fonction du rôle
        if ($user->isParent()) {
            return redirect()->route('parent.profile')->with('success', 'Profil mis à jour avec succès.');
        } elseif ($user->isTeacher()) {
            return redirect()->route('teacher.profile')->with('success', 'Profil mis à jour avec succès.');
        } elseif ($user->isStudent()) {
            return redirect()->route('student.profile')->with('success', 'Profil mis à jour avec succès.');
        } else {
            return redirect()->route('dashboard')->with('success', 'Profil mis à jour avec succès.');
        }
    }
} 