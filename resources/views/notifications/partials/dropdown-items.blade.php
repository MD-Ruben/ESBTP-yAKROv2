@if($notifications->isEmpty())
    <div class="text-center p-3">
        <small>Aucune notification</small>
    </div>
@else
    @foreach($notifications as $notification)
        <div class="notification-item {{ !$notification->read_at ? 'unread' : '' }}"
             id="notification-{{ $notification->id }}"
             @if(isset($notification->data['link']))
                 onclick="window.location.href='{{ $notification->data['link'] }}'; markAsRead('{{ $notification->id }}');"
             @else
                 onclick="markAsRead('{{ $notification->id }}');"
             @endif
             style="cursor: pointer; opacity: 1; transition: opacity 0.3s ease;">
            <div class="d-flex align-items-center mb-1">
                @if(isset($notification->data['type']) && $notification->data['type'] == 'danger')
                    <span class="notification-icon bg-danger-light text-danger me-2">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                @elseif(isset($notification->data['type']) && $notification->data['type'] == 'warning')
                    <span class="notification-icon bg-warning-light text-warning me-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                @elseif(isset($notification->data['type']) && $notification->data['type'] == 'success')
                    <span class="notification-icon bg-success-light text-success me-2">
                        <i class="fas fa-check-circle"></i>
                    </span>
                @else
                    <span class="notification-icon bg-info-light text-info me-2">
                        <i class="fas fa-info-circle"></i>
                    </span>
                @endif
                <h6 class="notification-title mb-0">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                @if(!$notification->read_at)
                    <span class="ms-auto badge bg-warning">Nouveau</span>
                @endif
            </div>
            <p class="notification-message mb-0">{{ Str::limit($notification->data['message'] ?? '', 100) }}</p>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="notification-time text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                @if(str_contains(strtolower($notification->data['title'] ?? ''), 'absence'))
                    <a href="{{ $notification->data['link'] ?? route('esbtp.mes-absences.index') }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                        Justifier
                    </a>
                @endif
            </div>
        </div>
    @endforeach
@endif

<style>
.notification-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.notification-message {
    color: #495057;
    font-size: 0.9rem;
}
.notification-item {
    padding: 12px 15px;
    border-bottom: 1px solid #f1f1f1;
    transition: all 0.3s ease;
    height: auto;
    overflow: hidden;
}
.notification-item:hover {
    background-color: rgba(1, 99, 47, 0.05);
}
.notification-item.unread {
    background-color: rgba(242, 148, 0, 0.1);
    border-left: 3px solid #f29400;
}
.notification-item.fadeOut {
    opacity: 0;
    height: 0;
    padding: 0;
    margin: 0;
    border: none;
}
.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.1);
}
.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1);
}
.bg-success-light {
    background-color: rgba(40, 167, 69, 0.1);
}
.bg-info-light {
    background-color: rgba(23, 162, 184, 0.1);
}
</style>
