@extends('layouts.app')

@section('title', 'Tableau de bord Secrétaire')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenue dans le tableau de bord Secrétaire</h5>
                    <p class="card-text">Gestion administrative de l'ESBTP-yAKRO</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Étudiants</h5>
                    <p class="card-text display-4">{{ $totalStudents ?? 0 }}</p>
                    <a href="{{ route('esbtp.students.index') }}" class="btn btn-light">Voir</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Présences d'aujourd'hui</h5>
                    <p class="card-text display-4">{{ $todayAttendances ?? 0 }}</p>
                    <a href="{{ route('esbtp.attendances.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Présences en attente</h5>
                    <p class="card-text display-4">{{ $pendingAttendances ?? 0 }}</p>
                    <a href="{{ route('esbtp.attendances.pending') }}" class="btn btn-light">Traiter</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Notifications récentes
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentNotifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $notification->title }}</strong>
                                    <p class="mb-0">{{ Str::limit($notification->message, 50) }}</p>
                                </div>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item">Aucune notification récente</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Messages récents
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($recentMessages as $message)
                            <a href="{{ route('messages.show', $message) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $message->subject }}</h6>
                                    <small>{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ Str::limit($message->content, 80) }}</p>
                                <small>
                                    De: {{ $message->sender->name ?? 'Système' }}
                                </small>
                            </a>
                        @empty
                            <div class="list-group-item">
                                <p class="mb-0">Aucun message récent</p>
                            </div>
                        @endforelse
                        <div class="mt-3">
                            <a href="{{ route('messages.inbox') }}" class="btn btn-outline-primary">Voir tous les messages</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Actions rapides
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('esbtp.students.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Inscrire un nouvel étudiant
                        </a>
                        <a href="{{ route('esbtp.attendances.today') }}" class="btn btn-success">
                            <i class="fas fa-clipboard-check"></i> Marquer les présences du jour
                        </a>
                        <a href="{{ route('esbtp.bulletins.index') }}" class="btn btn-info">
                            <i class="fas fa-file-alt"></i> Générer des bulletins
                        </a>
                        <a href="{{ route('esbtp.timetables.index') }}" class="btn btn-secondary">
                            <i class="fas fa-calendar-alt"></i> Consulter les emplois du temps
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Envoyer un message
                </div>
                <div class="card-body">
                    <form action="{{ route('messages.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient_type" class="form-label">Destinataire</label>
                            <select class="form-select" id="recipient_type" name="recipient_type" required>
                                <option value="">Sélectionner un destinataire</option>
                                <option value="superAdmin">Super Admin</option>
                                <option value="etudiant">Tous les étudiants</option>
                                <option value="specific_user">Utilisateur spécifique</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="specific_user_container" style="display: none;">
                            <label for="recipient_id" class="form-label">Sélectionner un utilisateur</label>
                            <select class="form-select" id="recipient_id" name="recipient_id">
                                <option value="">Choisir un utilisateur</option>
                                @foreach(App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Message</label>
                            <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Afficher/masquer les conteneurs de sélection spécifiques
        const recipientType = document.getElementById('recipient_type');
        const specificUserContainer = document.getElementById('specific_user_container');
        
        recipientType.addEventListener('change', function() {
            if (this.value === 'specific_user') {
                specificUserContainer.style.display = 'block';
            } else {
                specificUserContainer.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection 