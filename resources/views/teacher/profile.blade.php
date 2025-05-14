@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mon Profil</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Mon Profil</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-4">
            <!-- Carte Profil -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations Personnelles
                </div>
                <div class="card-body text-center">
                    <img class="img-account-profile rounded-circle mb-3" 
                         src="{{ Auth::user()->profile_image ? asset('storage/'.Auth::user()->profile_image) : asset('images/default-avatar.png') }}" 
                         alt="Photo de profil" style="width: 150px; height: 150px; object-fit: cover;">
                    <h5 class="my-3">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-1">{{ Auth::user()->email }}</p>
                    <p class="text-muted mb-4">{{ Auth::user()->phone ?? 'Aucun numéro de téléphone' }}</p>
                    <div class="d-flex justify-content-center mb-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-1"></i> Modifier le profil
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Carte Rôles et Permissions -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i>
                    Rôles et Autorisations
                </div>
                <div class="card-body">
                    <h5 class="mb-3">Rôles:</h5>
                    <div class="mb-3">
                        @forelse(Auth::user()->getRoleNames() as $role)
                            <span class="badge bg-primary mb-1">{{ $role }}</span>
                        @empty
                            <span class="text-muted">Aucun rôle assigné</span>
                        @endforelse
                    </div>
                    
                    <h5 class="mb-3">Autorisations:</h5>
                    <div>
                        @forelse(Auth::user()->getAllPermissions() as $permission)
                            <span class="badge bg-secondary mb-1">{{ $permission->name }}</span>
                        @empty
                            <span class="text-muted">Aucune autorisation directe</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <!-- Carte d'info de compte -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informations du Compte
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-striped">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 30%;">Nom d'utilisateur:</th>
                                <td>{{ Auth::user()->username }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Adresse email:</th>
                                <td>{{ Auth::user()->email }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Date d'inscription:</th>
                                <td>{{ Auth::user()->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Dernière connexion:</th>
                                <td>{{ Auth::user()->last_login_at ?? 'Jamais' }}</td>
                            </tr>
                            <tr>
                                <th scope="row">Statut du compte:</th>
                                <td>
                                    @if(Auth::user()->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Carte Emploi du temps -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Emploi du temps cette semaine
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Jour</th>
                                    <th>Matière</th>
                                    <th>Classe</th>
                                    <th>Horaire</th>
                                    <th>Salle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $hasClasses = false;
                                    $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                                @endphp
                                
                                @foreach($days as $day)
                                    @if(isset($weeklyTimetable[$day]) && count($weeklyTimetable[$day]) > 0)
                                        @php $hasClasses = true; @endphp
                                        @foreach($weeklyTimetable[$day] as $seance)
                                            <tr>
                                                <td>{{ $day }}</td>
                                                <td>{{ $seance->matiere->nom ?? 'N/A' }}</td>
                                                <td>{{ $seance->classe->nom ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}</td>
                                                <td>{{ $seance->salle ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                                
                                @if(!$hasClasses)
                                    <tr>
                                        <td colspan="5" class="text-center">Aucun cours programmé cette semaine</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <a href="{{ route('timetables.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-alt me-1"></i> Voir l'emploi du temps complet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification du profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('teacher.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Modifier mon profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ Auth::user()->username }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ Auth::user()->phone }}">
                    </div>
                    
                    <hr>
                    <h6>Changer le mot de passe</h6>
                    <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer le mot de passe.</p>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection 