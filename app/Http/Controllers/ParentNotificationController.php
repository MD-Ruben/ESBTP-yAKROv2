<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ESBTPNotification;
use App\Models\ESBTPParent;
use App\Models\ESBTPEtudiant;
use Illuminate\Support\Facades\Auth;

class ParentNotificationController extends Controller
{
    /**
     * Constructeur.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:parent']);
    }

    /**
     * Affiche toutes les notifications du parent.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les IDs des étudiants associés à ce parent
        $etudiantIds = $parent->etudiants()->pluck('esbtp_etudiants.id')->toArray();
        
        // Récupérer toutes les notifications relatives aux étudiants de ce parent
        $notifications = ESBTPNotification::whereIn('etudiant_id', $etudiantIds)
            ->orWhere('audience', 'parents')
            ->orWhere('audience', 'all')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('parent.notifications.index', compact('notifications', 'parent'));
    }

    /**
     * Affiche les détails d'une notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les IDs des étudiants associés à ce parent
        $etudiantIds = $parent->etudiants()->pluck('esbtp_etudiants.id')->toArray();
        
        // Récupérer la notification si elle est destinée à l'un des étudiants du parent
        // ou si elle est destinée à tous les parents ou à tout le monde
        $notification = ESBTPNotification::where(function($query) use ($etudiantIds) {
                $query->whereIn('etudiant_id', $etudiantIds);
            })
            ->orWhere('audience', 'parents')
            ->orWhere('audience', 'all')
            ->findOrFail($id);
            
        // Marquer comme lue si ce n'est pas déjà fait
        if (!$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }
            
        return view('parent.notifications.show', compact('notification', 'parent'));
    }

    /**
     * Marque une notification comme lue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les IDs des étudiants associés à ce parent
        $etudiantIds = $parent->etudiants()->pluck('esbtp_etudiants.id')->toArray();
        
        // Récupérer la notification
        $notification = ESBTPNotification::where(function($query) use ($etudiantIds) {
                $query->whereIn('etudiant_id', $etudiantIds);
            })
            ->orWhere('audience', 'parents')
            ->orWhere('audience', 'all')
            ->findOrFail($id);
        
        // Marquer comme lue
        $notification->read_at = now();
        $notification->save();
        
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marque toutes les notifications comme lues.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $parent = ESBTPParent::where('user_id', $user->id)->firstOrFail();
        
        // Récupérer les IDs des étudiants associés à ce parent
        $etudiantIds = $parent->etudiants()->pluck('esbtp_etudiants.id')->toArray();
        
        // Marquer toutes les notifications non lues comme lues
        ESBTPNotification::whereNull('read_at')
            ->where(function($query) use ($etudiantIds) {
                $query->whereIn('etudiant_id', $etudiantIds)
                      ->orWhere('audience', 'parents')
                      ->orWhere('audience', 'all');
            })
            ->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
} 