@extends('layouts.app')

@section('title', 'Mon Profil')

@section('page_title', 'Profil SuperAdmin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Carte d'informations personnelles -->
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-user-shield me-2"></i>Profil SuperAdmin</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($user->profile_photo_path)
                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="Photo de profil" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 150px; height: 150px; margin: 0 auto;">
                                <span style="font-size: 3rem;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $user->name }}</h4>
                        <p class="badge bg-danger">Super Administrateur</p>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>
                    
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Informations personnelles</h5>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Nom complet:</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Statut:</div>
                        <div class="col-md-8">
                            @if($user->is_active)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Date de création:</div>
                        <div class="col-md-8">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Dernière connexion:</div>
                        <div class="col-md-8">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y à H:i') : 'Jamais' }}</div>
                    </div>
                    
                    <h5 class="border-bottom pb-2 mb-3 mt-4"><i class="fas fa-user-tag me-2"></i>Rôles et permissions</h5>
                    
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Rôle:</div>
                        <div class="col-md-8">
                            <span class="badge bg-danger">Super Administrateur</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold mb-2">Permissions:</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($user->getAllPermissions() as $permission)
                                <span class="badge bg-info">{{ $permission->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <div class="row">
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-1"></i>Changer mot de passe
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="#" class="btn btn-outline-secondary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fas fa-edit me-1"></i>Modifier profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal changement de mot de passe -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">Changer mon mot de passe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('esbtp.admin.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Changer mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal édition de profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editProfileModalLabel">Modifier mon profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('esbtp.admin.update-profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Photo de profil</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                        <small class="form-text text-muted">Laissez vide pour conserver l'image actuelle.</small>
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