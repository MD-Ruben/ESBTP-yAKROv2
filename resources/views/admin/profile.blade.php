@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-gradient" style="background-color: var(--esbtp-green-light); border-bottom: none; padding: 1.5rem;">
                    <h4 class="text-dark mb-0">
                        <i class="fas fa-user-circle me-2"></i> Mon Profil
                    </h4>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Profile Sidebar -->
                        <div class="col-lg-4 border-end">
                            <div class="p-4 text-center d-flex flex-column align-items-center justify-content-center h-100 bg-light">
                                <div class="position-relative mb-4">
                                    <div class="profile-photo-container rounded-circle overflow-hidden border shadow-sm" style="width: 180px; height: 180px;">
                                        @if($user->profile_photo_path)
                                            <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <img src="{{ asset('images/avatar.jpg') }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                                        @endif
                                    </div>
                                    <div class="photo-overlay">
                                        <label for="profile_photo" class="position-absolute bottom-0 end-0 bg-white rounded-circle p-2 shadow-sm edit-photo-btn" data-bs-toggle="tooltip" title="Changer la photo">
                                            <i class="fas fa-camera text-primary"></i>
                                        </label>
                                    </div>
                                </div>

                                <h3 class="fw-bold text-dark mb-1">{{ $user->name }}</h3>
                                <div class="badge bg-primary text-white mb-3 px-3 py-2 rounded-pill">
                                    <i class="fas fa-id-badge me-1"></i>
                                    {{ $user->roles->first()->name ?? 'Utilisateur' }}
                                </div>

                                <div class="profile-stats d-flex flex-column gap-2 text-start w-100 mt-3">
                                    <div class="d-flex align-items-center p-2 rounded-3 bg-white shadow-sm border">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="background-color: var(--esbtp-light-green); width: 40px; height: 40px;">
                                            <i class="fas fa-envelope text-success"></i>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Email</div>
                                            <div class="text-dark">{{ $user->email }}</div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center p-2 rounded-3 bg-white shadow-sm border">
                                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="background-color: var(--esbtp-light-orange); width: 40px; height: 40px;">
                                            <i class="fas fa-clock text-warning"></i>
                                        </div>
                                        <div>
                                            <div class="small text-muted">Compte créé le</div>
                                            <div class="text-dark">{{ $user->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Content -->
                        <div class="col-lg-8">
                            <div class="p-4">
                                <ul class="nav nav-pills nav-fill mb-4" id="profileTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active px-4 py-3 rounded-3" id="info-tab" data-bs-toggle="pill" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                            <i class="fas fa-user me-2"></i> Informations Personnelles
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link px-4 py-3 rounded-3" id="professional-tab" data-bs-toggle="pill" data-bs-target="#professional" type="button" role="tab" aria-controls="professional" aria-selected="false">
                                            <i class="fas fa-briefcase me-2"></i> Informations Professionnelles
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link px-4 py-3 rounded-3" id="system-tab" data-bs-toggle="pill" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">
                                            <i class="fas fa-cog me-2"></i> Informations Système
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link px-4 py-3 rounded-3" id="password-tab" data-bs-toggle="pill" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                            <i class="fas fa-lock me-2"></i> Modifier mot de passe
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content mt-4" id="profileTabsContent">
                                    <!-- Onglet Informations Personnelles -->
                                    <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                                            @csrf
                                            @method('PUT')

                                            <!-- Hidden profile photo input -->
                                            <input type="file" class="d-none @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                                            @error('profile_photo')
                                                <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
                                            @enderror

                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label for="first_name" class="form-label fw-medium">Prénom</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border"><i class="fas fa-user text-muted"></i></span>
                                                        <input type="text" class="form-control form-control-lg @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="Votre prénom">
                                                    </div>
                                                    @error('first_name')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-4">
                                                    <label for="last_name" class="form-label fw-medium">Nom</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border"><i class="fas fa-user text-muted"></i></span>
                                                        <input type="text" class="form-control form-control-lg @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="Votre nom">
                                                    </div>
                                                    @error('last_name')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label for="name" class="form-label fw-medium">Nom d'affichage</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-id-card text-muted"></i></span>
                                                    <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nom d'affichage">
                                                </div>
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="email" class="form-label fw-medium">Email</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-envelope text-muted"></i></span>
                                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Votre adresse email">
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="phone" class="form-label fw-medium">Téléphone</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-phone text-muted"></i></span>
                                                    <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Votre numéro de téléphone">
                                                </div>
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-8 mb-4">
                                                    <label for="address" class="form-label fw-medium">Adresse</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                        <input type="text" class="form-control form-control-lg @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Votre adresse">
                                                    </div>
                                                    @error('address')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-4 mb-4">
                                                    <label for="city" class="form-label fw-medium">Ville</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border"><i class="fas fa-city text-muted"></i></span>
                                                        <input type="text" class="form-control form-control-lg @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $user->city) }}" placeholder="Votre ville">
                                                    </div>
                                                    @error('city')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label for="birth_date" class="form-label fw-medium">Date de naissance</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-calendar-alt text-muted"></i></span>
                                                    <input type="date" class="form-control form-control-lg @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                                                </div>
                                                @error('birth_date')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="d-grid mt-4">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-save me-2"></i> Mettre à jour le profil
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Onglet Informations Professionnelles -->
                                    <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                                        <form action="{{ route('admin.profile.update.professional') }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-4">
                                                <label for="position" class="form-label fw-medium">Poste / Titre</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-briefcase text-muted"></i></span>
                                                    <input type="text" class="form-control form-control-lg @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $user->position ?? 'Directeur des Études') }}" placeholder="Votre poste ou titre">
                                                </div>
                                                @error('position')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="department" class="form-label fw-medium">Département</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-building text-muted"></i></span>
                                                    <input type="text" class="form-control form-control-lg @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department', $user->department ?? 'Administration') }}" placeholder="Votre département">
                                                </div>
                                                @error('department')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="office_location" class="form-label fw-medium">Emplacement du bureau</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-door-open text-muted"></i></span>
                                                    <input type="text" class="form-control form-control-lg @error('office_location') is-invalid @enderror" id="office_location" name="office_location" value="{{ old('office_location', $user->office_location ?? '') }}" placeholder="Emplacement de votre bureau">
                                                </div>
                                                @error('office_location')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label for="employee_id" class="form-label fw-medium">ID Employé</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border"><i class="fas fa-id-badge text-muted"></i></span>
                                                        <input type="text" class="form-control form-control-lg @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" value="{{ old('employee_id', $user->employee_id ?? '') }}" placeholder="Votre ID employé">
                                                    </div>
                                                    @error('employee_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-4">
                                                    <label for="appointment_date" class="form-label fw-medium">Date de nomination</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light border"><i class="fas fa-calendar-check text-muted"></i></span>
                                                        <input type="date" class="form-control form-control-lg @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', $user->appointment_date ? $user->appointment_date->format('Y-m-d') : '') }}">
                                                    </div>
                                                    @error('appointment_date')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="d-grid mt-4">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-save me-2"></i> Mettre à jour les informations professionnelles
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Onglet Informations Système -->
                                    <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations du compte</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-medium">ID du compte:</div>
                                                    <div class="col-md-8">{{ $user->id }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-medium">Nom d'utilisateur:</div>
                                                    <div class="col-md-8">{{ $user->username }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-medium">Rôle:</div>
                                                    <div class="col-md-8">
                                                        @foreach($user->roles as $role)
                                                            <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-medium">Statut du compte:</div>
                                                    <div class="col-md-8">
                                                        @if($user->is_active)
                                                            <span class="badge bg-success">Actif</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactif</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-medium">Date de création:</div>
                                                    <div class="col-md-8">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4 fw-medium">Dernière modification:</div>
                                                    <div class="col-md-8">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 fw-medium">Dernière connexion:</div>
                                                    <div class="col-md-8">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : 'Jamais' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i> Permissions</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Module</th>
                                                                <th class="text-center">Voir</th>
                                                                <th class="text-center">Créer</th>
                                                                <th class="text-center">Modifier</th>
                                                                <th class="text-center">Supprimer</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($user->getAllPermissions()->groupBy(function($permission) {
                                                                return explode('.', $permission->name)[0];
                                                            }) as $module => $permissions)
                                                                <tr>
                                                                    <td class="fw-medium">{{ ucfirst($module) }}</td>
                                                                    <td class="text-center">
                                                                        @if($permissions->where('name', $module.'.view')->count() > 0)
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                        @else
                                                                            <i class="fas fa-times-circle text-danger"></i>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($permissions->where('name', $module.'.create')->count() > 0)
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                        @else
                                                                            <i class="fas fa-times-circle text-danger"></i>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($permissions->where('name', $module.'.edit')->count() > 0)
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                        @else
                                                                            <i class="fas fa-times-circle text-danger"></i>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($permissions->where('name', $module.'.delete')->count() > 0)
                                                                            <i class="fas fa-check-circle text-success"></i>
                                                                        @else
                                                                            <i class="fas fa-times-circle text-danger"></i>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Onglet Modifier mot de passe -->
                                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                        <form action="{{ route('admin.password.update') }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="mb-4">
                                                <label for="current_password" class="form-label fw-medium">Mot de passe actuel</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-lock text-muted"></i></span>
                                                    <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror password-field" id="current_password" name="current_password" placeholder="Votre mot de passe actuel">
                                                    <button type="button" class="input-group-text bg-light border toggle-password"><i class="fas fa-eye text-muted"></i></button>
                                                </div>
                                                @error('current_password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="password" class="form-label fw-medium">Nouveau mot de passe</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-key text-muted"></i></span>
                                                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror password-field" id="password" name="password" placeholder="Votre nouveau mot de passe">
                                                    <button type="button" class="input-group-text bg-light border toggle-password"><i class="fas fa-eye text-muted"></i></button>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-4">
                                                <label for="password_confirmation" class="form-label fw-medium">Confirmer le mot de passe</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border"><i class="fas fa-key text-muted"></i></span>
                                                    <input type="password" class="form-control form-control-lg password-field" id="password_confirmation" name="password_confirmation" placeholder="Confirmez votre nouveau mot de passe">
                                                    <button type="button" class="input-group-text bg-light border toggle-password"><i class="fas fa-eye text-muted"></i></button>
                                                </div>
                                            </div>

                                            <div class="d-grid mt-4">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-key me-2"></i> Mettre à jour le mot de passe
                                                </button>
                                            </div>
                                        </form>
                                    </div>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Profile photo upload
        const profilePhotoInput = document.getElementById('profile_photo');
        const profileImage = document.querySelector('.profile-photo-container img');
        const editPhotoBtn = document.querySelector('.edit-photo-btn');

        if (editPhotoBtn && profilePhotoInput) {
            editPhotoBtn.addEventListener('click', function(e) {
                e.preventDefault();
                profilePhotoInput.click();
            });
        }

        if (profilePhotoInput && profileImage) {
            profilePhotoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                        // Add animation
                        profileImage.classList.add('animate__animated', 'animate__fadeIn');
                        setTimeout(() => {
                            profileImage.classList.remove('animate__animated', 'animate__fadeIn');
                        }, 1000);
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const passwordField = this.parentElement.querySelector('.password-field');
                const icon = this.querySelector('i');

                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* Custom styles for profile page */
    .profile-photo-container {
        transition: all 0.3s ease;
    }

    .profile-photo-container:hover {
        transform: scale(1.02);
    }

    .photo-overlay .edit-photo-btn {
        transition: all 0.3s ease;
        cursor: pointer;
        opacity: 0.9;
    }

    .photo-overlay .edit-photo-btn:hover {
        transform: scale(1.1);
        opacity: 1;
    }

    .nav-pills .nav-link {
        color: var(--esbtp-text);
        background-color: var(--esbtp-light-green);
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link:hover {
        background-color: rgba(1, 99, 47, 0.2);
    }

    .nav-pills .nav-link.active {
        background-color: var(--esbtp-green);
        color: white;
    }

    .form-control:focus {
        border-color: var(--esbtp-green);
        box-shadow: 0 0 0 0.25rem rgba(1, 99, 47, 0.25);
    }

    .toggle-password {
        cursor: pointer;
    }

    .btn-primary {
        background-color: var(--esbtp-green);
        border-color: var(--esbtp-green);
    }

    .btn-primary:hover {
        background-color: var(--esbtp-green-dark);
        border-color: var(--esbtp-green-dark);
    }

    .badge {
        font-weight: 500;
    }

    .rounded-4 {
        border-radius: 0.75rem !important;
    }

    /* Smooth transitions */
    .tab-pane.fade {
        transition: opacity 0.3s ease-in-out;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .col-lg-4.border-end {
            border-right: none !important;
            border-bottom: 1px solid #dee2e6;
        }
    }
</style>
@endpush
