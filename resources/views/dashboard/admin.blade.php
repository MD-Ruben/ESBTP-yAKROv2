@extends('layouts.app')

@section('title', 'Tableau de bord Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenue dans le tableau de bord Admin</h5>
                    <p class="card-text">Gestion des présences et des notes des étudiants</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Étudiants</h5>
                    <p class="card-text display-4">{{ $totalStudents ?? 0 }}</p>
                    <a href="{{ route('students.index') }}" class="btn btn-light">Voir tous</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Enseignants</h5>
                    <p class="card-text display-4">{{ $totalTeachers ?? 0 }}</p>
                    <a href="{{ route('teachers.index') }}" class="btn btn-light">Voir tous</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Présences aujourd'hui</h5>
                    <p class="card-text display-4">{{ $todayAttendances ?? 0 }}</p>
                    <a href="{{ route('attendance.mark-page') }}" class="btn btn-light">Marquer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Présences en attente</h5>
                    <p class="card-text display-4">{{ $pendingAttendances ?? 0 }}</p>
                    <a href="{{ route('attendance.mark-page') }}" class="btn btn-light">Traiter</a>
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
                        <a href="{{ route('attendance.mark-page') }}" class="btn btn-primary">Marquer les présences</a>
                        <a href="{{ route('grades.index') }}" class="btn btn-success">Gérer les notes</a>
                        <a href="{{ route('notifications.create') }}" class="btn btn-info">Envoyer une notification</a>
                        <a href="{{ route('timetables.index') }}" class="btn btn-warning">Gérer les emplois du temps</a>
                    </div>
                </div>
            </div>
        </div>
        
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
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Messagerie interne
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Envoyer un message</h5>
                            <form action="{{ route('messages.send-to-group') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="recipient_type" class="form-label">Destinataire</label>
                                    <select class="form-select" id="recipient_type" name="recipient_type" required>
                                        <option value="">Sélectionner un destinataire</option>
                                        <option value="all">Tous les utilisateurs</option>
                                        <option value="students">Tous les étudiants</option>
                                        <option value="teachers">Tous les enseignants</option>
                                        <option value="admins">Tous les administrateurs</option>
                                        <option value="class">Une classe spécifique</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="recipient_group_container" style="display: none;">
                                    <label for="recipient_group" class="form-label">Identifiant du groupe</label>
                                    <input type="text" class="form-control" id="recipient_group" name="recipient_group">
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
                        <div class="col-md-6">
                            <h5>Messages récents</h5>
                            <div class="list-group">
                                @forelse($recentMessages as $message)
                                    <a href="{{ route('messages.show', $message) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $message->subject }}</h6>
                                            <small>{{ $message->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($message->content, 80) }}</p>
                                        <small>
                                            @if($message->isGroupMessage())
                                                Envoyé à: 
                                                @if($message->recipient_type == 'all')
                                                    Tous les utilisateurs
                                                @elseif($message->recipient_type == 'students')
                                                    Tous les étudiants
                                                @elseif($message->recipient_type == 'teachers')
                                                    Tous les enseignants
                                                @elseif($message->recipient_type == 'admins')
                                                    Tous les administrateurs
                                                @elseif($message->recipient_type == 'class')
                                                    Classe #{{ $message->recipient_group }}
                                                @endif
                                            @else
                                                Envoyé à: {{ $message->recipient->name ?? 'Destinataire inconnu' }}
                                            @endif
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipientTypeSelect = document.getElementById('recipient_type');
        const recipientGroupContainer = document.getElementById('recipient_group_container');
        
        // Afficher/masquer le champ de groupe en fonction du type de destinataire
        recipientTypeSelect.addEventListener('change', function() {
            if (this.value === 'class') {
                recipientGroupContainer.style.display = 'block';
            } else {
                recipientGroupContainer.style.display = 'none';
            }
        });
    });
</script>
@endsection 