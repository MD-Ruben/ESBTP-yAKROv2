<?php

namespace App\Http\Controllers\ESBTP;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Grade;
use App\Models\Message;
use App\Models\Notification;
use App\Models\ESBTPEtudiant;
use App\Models\User;
use App\Models\Filiere;
use App\Models\NiveauEtude;
use App\Models\Formation;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Constructeur qui applique le middleware auth et role:superAdmin.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:superAdmin']);
    }

    /**
     * Affiche le tableau de bord du superAdmin.
     */
    public function dashboard()
    {
        $totalStudents = ESBTPEtudiant::count();
        $totalSecretaires = User::role('secretaire')->count();
        $totalUsers = User::count();
        
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Statistiques des filières, formations et niveaux d'études
        try {
            $totalFilieres = Filiere::count();
            $totalNiveaux = NiveauEtude::count();
            $totalFormations = Formation::count() ?? 0;
            $totalClasses = Classe::count() ?? 0;
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
} 