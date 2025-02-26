<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Grade;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Timetable;
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
        
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isTeacher()) {
            return $this->teacherDashboard();
        } elseif ($user->isStudent()) {
            return $this->studentDashboard();
        } elseif ($user->isParent()) {
            return $this->parentDashboard();
        }
        
        // Redirection par défaut
        return view('dashboard.default');
    }
    
    /**
     * Tableau de bord pour les administrateurs.
     */
    private function adminDashboard()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $recentCertificates = Certificate::with(['student.user', 'certificateType'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $recentNotifications = Notification::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Statistiques de présence
        $attendanceStats = Attendance::selectRaw('DATE(date) as attendance_date, COUNT(*) as total, SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present')
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'desc')
            ->take(7)
            ->get();
        
        return view('dashboard.admin', compact(
            'totalStudents', 
            'totalTeachers', 
            'recentCertificates', 
            'recentNotifications',
            'attendanceStats'
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
            return redirect()->route('dashboard.default')
                ->with('error', 'Profil enseignant non trouvé.');
        }
        
        $todayClasses = Timetable::where('teacher_id', $teacher->id)
            ->where('day', strtolower(date('l')))
            ->orderBy('start_time')
            ->get();
        
        $recentAttendances = Attendance::where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard.teacher', compact(
            'teacher',
            'todayClasses',
            'recentAttendances'
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
            return redirect()->route('dashboard.default')
                ->with('error', 'Profil étudiant non trouvé.');
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
            return redirect()->route('dashboard.default')
                ->with('error', 'Profil parent non trouvé.');
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
            return redirect()->route('dashboard.default')
                ->with('error', 'Profil parent non trouvé.');
        }
        
        $children = Student::where('guardian_id', $guardian->id)
            ->with(['class', 'section', 'user'])
            ->get();
        
        return view('parent.children', compact('children'));
    }
} 