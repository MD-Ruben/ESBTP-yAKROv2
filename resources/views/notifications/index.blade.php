@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Notifications</h5>
                    @if($notifications->isNotEmpty())
                        <button class="btn btn-outline-secondary btn-sm mark-all-read">
                            <i class="fas fa-check-double me-1"></i> Tout marquer comme lu
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="text-center p-5">
                            <div class="empty-state mb-3">
                                <i class="fas fa-bell-slash fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">Aucune notification</h6>
                            <p class="small text-muted">Vous n'avez pas encore reçu de notifications</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ !$notification->read_at ? 'unread' : '' }}"
                                     id="notification-{{ $notification->id }}"
                                     @if(isset($notification->data['link']))
                                         onclick="window.location.href='{{ $notification->data['link'] }}'; markAsRead('{{ $notification->id }}');"
                                     @else
                                         onclick="markAsRead('{{ $notification->id }}');"
                                     @endif
                                     style="cursor: pointer;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
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
                                                <h6 class="mb-0 fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                                @if(!$notification->read_at)
                                                    <span class="ms-2 badge bg-warning">Nouveau</span>
                                                @endif
                                            </div>
                                            <p class="notification-message">{{ $notification->data['message'] ?? '' }}</p>
                                            <div class="d-flex align-items-center mt-2">
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center justify-content-end">
                                            @if(str_contains(strtolower($notification->data['title'] ?? ''), 'absence'))
                                                <a href="{{ $notification->data['link'] ?? route('esbtp.mes-absences.index') }}" class="btn btn-primary btn-sm" onclick="event.stopPropagation();">
                                                    <i class="fas fa-file-alt me-1"></i> Justifier l'absence
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center p-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.notification-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}
.notification-message {
    color: #495057;
    margin-bottom: 0;
}
.notification-item {
    padding: 15px;
    transition: all 0.2s ease;
}
.notification-item:hover {
    background-color: rgba(1, 99, 47, 0.05);
}
.notification-item.unread {
    background-color: rgba(242, 148, 0, 0.1);
    border-left: 3px solid #f29400;
}
.empty-state {
    padding: 20px;
    border-radius: 50%;
    background-color: #f8f9fa;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
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
@endpush

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`{{ url('notifications') }}/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(() => {
        const item = document.querySelector(`#notification-${id}`);
        if (item) {
            item.classList.remove('unread');
            const badge = item.querySelector('.badge');
            if (badge) badge.remove();
        }
    });
}

document.querySelector('.mark-all-read')?.addEventListener('click', function(e) {
    e.preventDefault();

    @if(auth()->check() && auth()->user()->hasRole('etudiant'))
    fetch('{{ route("esbtp.mes-notifications.markAllAsRead") }}', {
    @else
    fetch('{{ route("notifications.markAllAsRead") }}', {
    @endif
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(() => {
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.classList.remove('unread');
            const badge = item.querySelector('.badge');
            if (badge) badge.remove();
        });
    });
});
</script>
@endpush
