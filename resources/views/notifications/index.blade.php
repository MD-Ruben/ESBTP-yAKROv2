@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    @if($notifications->isNotEmpty())
                        <button class="btn btn-link btn-sm mark-all-read">Tout marquer comme lu</button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="text-center p-4">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                            <p>Aucune notification</p>
                            </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ !$notification->read_at ? 'unread' : '' }}"
                                     onclick="markAsRead('{{ $notification->id }}')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                    @if(!$notification->read_at)
                                        <span class="badge bg-warning">Non lu</span>
                                    @endif
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
        const item = document.querySelector(`.notification-item[onclick*="${id}"]`);
        if (item) {
            item.classList.remove('unread');
            const badge = item.querySelector('.badge');
            if (badge) badge.remove();
        }
    });
}

document.querySelector('.mark-all-read')?.addEventListener('click', function() {
    fetch('{{ route("notifications.markAllAsRead") }}', {
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
