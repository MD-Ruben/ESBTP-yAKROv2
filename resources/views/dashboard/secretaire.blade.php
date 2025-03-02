@extends('layouts.app')

@section('title', 'Tableau de bord Secrétaire')

@section('content')
<div class="container-fluid px-4">
    <h1 class="my-4">Bienvenue, {{ $user->name }}</h1>
    <p class="text-muted">Gestion administrative ESBTP-yAKRO</p>

    <div class="row">
        @if(isset($totalStudents))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Étudiants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('etudiants.index') }}" class="btn btn-sm btn-primary mt-3">Gérer les étudiants</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($todayAttendances))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Présences aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayAttendances }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('attendances.today') }}" class="btn btn-sm btn-success mt-3">Gérer les présences</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($pendingJustifications))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Justifications en attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingJustifications }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('attendances.justifications') }}" class="btn btn-sm btn-warning mt-3">Traiter les justifications</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        @if(isset($totalTimetables))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Emplois du temps</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTimetables }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-info mt-3">Gérer les emplois du temps</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($todayClasses))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Cours aujourd'hui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayClasses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('timetables.today') }}" class="btn btn-sm btn-primary mt-3">Voir les cours d'aujourd'hui</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($pendingBulletins))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Bulletins en attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingBulletins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('bulletins.pending') }}" class="btn btn-sm btn-danger mt-3">Traiter les bulletins</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Étudiants récents -->
    @if(isset($recentStudents) && $recentStudents->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Étudiants récemment inscrits</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Matricule</th>
                                    <th>Classe</th>
                                    <th>Date d'inscription</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents as $etudiant)
                                <tr>
                                    <td>{{ $etudiant->nom }}</td>
                                    <td>{{ $etudiant->prenom }}</td>
                                    <td>{{ $etudiant->matricule }}</td>
                                    <td>{{ $etudiant->classe->nom ?? 'Non assigné' }}</td>
                                    <td>{{ $etudiant->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('etudiants.show', $etudiant->id) }}" class="btn btn-sm btn-info">Détails</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Messages récents -->
    @if(isset($recentMessages) && $recentMessages->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Messages récents</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($recentMessages as $message)
                        <a href="{{ route('messages.show', $message->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $message->subject }}</h5>
                                <small>{{ $message->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                            <small>De: {{ $message->sender->name ?? 'Système' }}</small>
                        </a>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('messages.index') }}" class="btn btn-primary">Voir tous les messages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulaire pour envoyer un message -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Envoyer un message</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('messages.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient_type" class="form-label">Destinataire</label>
                            <select class="form-select" id="recipient_type" name="recipient_type" required>
                                <option value="">Sélectionner un destinataire</option>
                                <option value="all">Tous les utilisateurs</option>
                                <option value="etudiant">Tous les étudiants</option>
                                <option value="specific_user">Étudiant spécifique</option>
                                <option value="specific_class">Classe spécifique</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="specific_user_container" style="display: none;">
                            <label for="recipient_id" class="form-label">Sélectionner un étudiant</label>
                            <select class="form-select" id="recipient_id" name="recipient_id">
                                <option value="">Choisir un étudiant</option>
                                @foreach(App\Models\ESBTPEtudiant::all() as $etudiant)
                                    <option value="{{ $etudiant->user_id }}">{{ $etudiant->nom }} {{ $etudiant->prenom }} ({{ $etudiant->matricule }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3" id="specific_class_container" style="display: none;">
                            <label for="recipient_group" class="form-label">Sélectionner une classe</label>
                            <select class="form-select" id="recipient_group" name="recipient_group">
                                <option value="">Choisir une classe</option>
                                @foreach(App\Models\ESBTPClasse::all() as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
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
        const specificClassContainer = document.getElementById('specific_class_container');
        
        recipientType.addEventListener('change', function() {
            if (this.value === 'specific_user') {
                specificUserContainer.style.display = 'block';
                specificClassContainer.style.display = 'none';
            } else if (this.value === 'specific_class') {
                specificUserContainer.style.display = 'none';
                specificClassContainer.style.display = 'block';
            } else {
                specificUserContainer.style.display = 'none';
                specificClassContainer.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection 