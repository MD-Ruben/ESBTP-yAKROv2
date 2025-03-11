@if($notifications->isEmpty())
    <div class="text-center p-3">
        <small>Aucune notification</small>
    </div>
@else
    @foreach($notifications as $notification)
        <div class="notification-item {{ !$notification->read_at ? 'unread' : '' }}"
             onclick="markAsRead('{{ $notification->id }}')">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
            </div>
            <p class="mb-0">{{ $notification->data['message'] ?? '' }}</p>
        </div>
    @endforeach
@endif
