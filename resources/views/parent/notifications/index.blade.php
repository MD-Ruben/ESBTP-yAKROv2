@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    <div>
                        <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
                        </a>
                        <a href="{{ route('parent.notifications.mark-all-read') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-check-double me-1"></i>Tout marquer comme lu
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($notifications->count() > 0)
                        <div class="list-group">
                            @foreach ($notifications as $notification)
                                <a href="{{ route('parent.notifications.show', $notification->id) }}" 
                                   class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'fw-bold bg-light' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-truncate">{{ Str::limit($notification->message, 100) }}</p>
                                    <div class="d-flex align-items-center mt-2">
                                        @if ($notification->etudiant_id)
                                            @php
                                                $etudiant = App\Models\ESBTPEtudiant::find($notification->etudiant_id);
                                            @endphp
                                            @if ($etudiant)
                                                <small class="text-primary">
                                                    <i class="fas fa-user-graduate me-1"></i>
                                                    {{ $etudiant->prenoms }} {{ $etudiant->nom }}
                                                </small>
                                            @endif
                                        @endif
                                        <small class="text-muted ms-auto">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ ucfirst($notification->type ?? 'Information') }}
                                        </small>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Vous n'avez aucune notification.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 