@extends('layouts.app')

@section('title', 'Paramètres du compte')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Paramètres du compte</h5>
                    <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Tableau de bord
                    </a>
                </div>
                <div class="card-body">
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

                    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                                <i class="fas fa-user me-1"></i>Profil
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                <i class="fas fa-lock me-1"></i>Mot de passe
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                                <i class="fas fa-bell me-1"></i>Notifications
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="settingsTabsContent">
                        <!-- Onglet Profil -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="row">
                                <div class="col-md-4 mb-4 mb-md-0">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="position-relative d-inline-block mb-3">
                                                <img src="{{ $parent->photo_url ?? asset('images/default-profile.png') }}" alt="Photo de profil" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                                <label for="profile-photo" class="position-absolute bottom-0 end-0 mb-1 me-1 bg-primary text-white rounded-circle p-2" style="cursor: pointer;">
                                                    <i class="fas fa-camera"></i>
                                                </label>
                                                <input type="file" id="profile-photo" name="profile_photo" class="d-none">
                                            </div>
                                            <h5 class="mb-1">{{ $parent->nom }} {{ $parent->prenoms }}</h5>
                                            <p class="text-muted mb-3">Parent</p>
                                            <div class="d-flex justify-content-center">
                                                <p class="badge bg-info me-2">{{ count($etudiants) }} Étudiant(s)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="mb-3">Informations personnelles</h6>
                                            <form action="{{ route('parent.settings.update-profile') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="nom" class="form-label">Nom</label>
                                                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $parent->nom) }}">
                                                        @error('nom')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="prenoms" class="form-label">Prénoms</label>
                                                        <input type="text" class="form-control @error('prenoms') is-invalid @enderror" id="prenoms" name="prenoms" value="{{ old('prenoms', $parent->prenoms) }}">
                                                        @error('prenoms')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="telephone" class="form-label">Téléphone</label>
                                                        <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $parent->telephone) }}">
                                                        @error('telephone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" readonly>
                                                        <small class="text-muted">L'email ne peut pas être modifié</small>
                                                        @error('email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="adresse" class="form-label">Adresse</label>
                                                    <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="3">{{ old('adresse', $parent->adresse) }}</textarea>
                                                    @error('adresse')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="profession" class="form-label">Profession</label>
                                                        <input type="text" class="form-control @error('profession') is-invalid @enderror" id="profession" name="profession" value="{{ old('profession', $parent->profession) }}">
                                                        @error('profession')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="genre" class="form-label">Genre</label>
                                                        <select class="form-select @error('genre') is-invalid @enderror" id="genre" name="genre">
                                                            <option value="">Sélectionner</option>
                                                            <option value="M" {{ old('genre', $parent->genre) == 'M' ? 'selected' : '' }}>Masculin</option>
                                                            <option value="F" {{ old('genre', $parent->genre) == 'F' ? 'selected' : '' }}>Féminin</option>
                                                        </select>
                                                        @error('genre')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-save me-1"></i>Enregistrer les modifications
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Mot de passe -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">Changer votre mot de passe</h6>
                                    <form action="{{ route('parent.settings.update-password') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password">
                                                @error('new_password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                                                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="alert alert-info">
                                                <strong>Conseils pour un mot de passe sécurisé :</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Utilisez au moins 8 caractères</li>
                                                    <li>Incluez au moins une lettre majuscule</li>
                                                    <li>Incluez au moins un chiffre</li>
                                                    <li>Incluez au moins un caractère spécial</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-lock me-1"></i>Mettre à jour le mot de passe
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Notifications -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">Préférences de notifications</h6>
                                    <form action="{{ route('parent.settings.update-notifications') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notify_absences" name="notify_absences" {{ $parent->settings->notify_absences ?? true ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notify_absences">Notifications d'absences</label>
                                            </div>
                                            <div class="form-text ms-4">Recevoir une notification lorsque votre enfant est marqué absent</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notify_grades" name="notify_grades" {{ $parent->settings->notify_grades ?? true ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notify_grades">Notifications de notes</label>
                                            </div>
                                            <div class="form-text ms-4">Recevoir une notification lorsque de nouvelles notes sont attribuées</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notify_messages" name="notify_messages" {{ $parent->settings->notify_messages ?? true ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notify_messages">Notifications de messages</label>
                                            </div>
                                            <div class="form-text ms-4">Recevoir une notification lorsqu'un message est reçu</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notify_payments" name="notify_payments" {{ $parent->settings->notify_payments ?? true ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notify_payments">Notifications de paiements</label>
                                            </div>
                                            <div class="form-text ms-4">Recevoir une notification pour les rappels de paiement et confirmations</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="notify_announcements" name="notify_announcements" {{ $parent->settings->notify_announcements ?? true ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notify_announcements">Annonces de l'école</label>
                                            </div>
                                            <div class="form-text ms-4">Recevoir une notification pour les annonces générales de l'école</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Préférence de canal de notification</label>
                                            <div class="ms-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="channel_email" name="channels[]" value="email" {{ in_array('email', $parent->settings->channels ?? ['email']) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="channel_email">Email</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="channel_sms" name="channels[]" value="sms" {{ in_array('sms', $parent->settings->channels ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="channel_sms">SMS</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="channel_app" name="channels[]" value="app" {{ in_array('app', $parent->settings->channels ?? ['app']) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="channel_app">Application (Notifications dans l'application)</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Enregistrer les préférences
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
@endsection