@extends('layouts.app')

@section('title', 'Tableau de bord Super Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenue dans le tableau de bord Super Admin</h5>
                    <p class="card-text">Gestion complète du système ESBTP-yAKRO</p>
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
                    <a href="{{ route('esbtp.students.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Secrétaires</h5>
                    <p class="card-text display-4">{{ $totalSecretaires ?? 0 }}</p>
                    <a href="{{ route('users.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Filières</h5>
                    <p class="card-text display-4">{{ $totalFilieres ?? 0 }}</p>
                    <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Niveaux d'études</h5>
                    <p class="card-text display-4">{{ $totalNiveaux ?? 0 }}</p>
                    <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4">{{ $totalUsers ?? 0 }}</p>
                    <a href="{{ route('users.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Formations</h5>
                    <p class="card-text display-4">{{ $totalFormations ?? 0 }}</p>
                    <a href="{{ route('esbtp.formations.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5 class="card-title">Classes</h5>
                    <p class="card-text display-4">{{ $totalClasses ?? 0 }}</p>
                    <a href="{{ route('esbtp.classes.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Utilisateurs récents
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $user->name }}</strong> ({{ $user->email }})
                                    <span class="badge bg-secondary">
                                        @if($user->hasRole('superAdmin'))
                                            Super Admin
                                        @elseif($user->hasRole('secretaire'))
                                            Secrétaire
                                        @elseif($user->hasRole('etudiant'))
                                            Étudiant
                                        @else
                                            {{ $user->roles->first()->name ?? 'Non défini' }}
                                        @endif
                                    </span>
                                </div>
                                <small>{{ $user->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun utilisateur récent</li>
                        @endforelse
                    </ul>
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
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Statistiques de présence
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" width="400" height="200"></canvas>
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
                                    @if(isset($message->recipient_type))
                                        Envoyé à: 
                                        @if($message->recipient_type == 'all')
                                            Tous les utilisateurs
                                        @elseif($message->recipient_type == 'etudiant')
                                            Tous les étudiants
                                        @elseif($message->recipient_type == 'secretaire')
                                            Tous les secrétaires
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
    
    <div class="row">
        <div class="col-md-12 mb-4">
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
                                <option value="all">Tous les utilisateurs</option>
                                <option value="etudiant">Tous les étudiants</option>
                                <option value="secretaire">Tous les secrétaires</option>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        // Graphique de présence
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    @foreach($attendanceStats as $stat)
                        '{{ \Carbon\Carbon::parse($stat['attendance_date'])->format('d/m') }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Taux de présence (%)',
                    data: [
                        @foreach($attendanceStats as $stat)
                            {{ $stat['total'] > 0 ? round(($stat['present'] / $stat['total']) * 100) : 0 }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection 