<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ESBTPNotification;
use Carbon\Carbon;

class ESBTPNotificationController extends Controller
{
    // Maximum number of notifications to keep per user
    protected const MAX_NOTIFICATIONS_PER_USER = 50;

    // Number of days to keep read notifications before pruning
    protected const DAYS_TO_KEEP_READ_NOTIFICATIONS = 7;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Si la requête est AJAX (pour le dropdown), retourner une vue partielle
        if (request()->ajax()) {
            $notifications = $user->notifications()->latest()->take(5)->get();
            return view('notifications.partials.dropdown-items', compact('notifications'));
        }

        // Sinon, retourner la vue complète avec pagination
        $notifications = $user->notifications()->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Prune old read notifications
        $this->pruneOldReadNotifications($user);

        // Ensure we don't exceed the maximum notifications per user
        $this->limitUserNotifications($user);

        // Si la requête est AJAX et qu'elle vient du dropdown, supprimer la notification du DOM
        if (request()->ajax() && request()->header('X-Source') === 'dropdown') {
            return response()->json([
                'success' => true,
                'hide' => true,
                'id' => $id
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        // Prune old read notifications
        $this->pruneOldReadNotifications($user);

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Remove old read notifications after a certain time period
     */
    protected function pruneOldReadNotifications($user)
    {
        // Supprimer les notifications lues il y a plus de 7 jours
        $user->notifications()
            ->whereNotNull('read_at')
            ->where('read_at', '<=', Carbon::now()->subDays(self::DAYS_TO_KEEP_READ_NOTIFICATIONS))
            ->delete();
    }

    /**
     * Ensure user doesn't have too many notifications by removing oldest ones
     */
    protected function limitUserNotifications($user)
    {
        $count = $user->notifications()->count();

        if ($count > self::MAX_NOTIFICATIONS_PER_USER) {
            $excess = $count - self::MAX_NOTIFICATIONS_PER_USER;

            // D'abord, supprimons les notifications lues les plus anciennes
            $readCount = $user->notifications()
                ->whereNotNull('read_at')
                ->orderBy('created_at')
                ->limit($excess)
                ->delete();

            // Si on a toujours des notifications en excès, supprimer les plus anciennes non lues
            if ($readCount < $excess) {
                $user->notifications()
                    ->orderBy('created_at')
                    ->limit($excess - $readCount)
                    ->delete();
            }
        }
    }
}
