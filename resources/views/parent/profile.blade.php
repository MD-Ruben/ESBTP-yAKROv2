@extends('layouts.app')

@section('title', 'Profil Parent')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-8 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="fw-bold mb-0">Mon profil parent</h2>
                                <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i> Retour au tableau de bord
                                </a>
                            </div>
                            <p class="text-muted mb-4">Gérez vos informations personnelles et consultez les détails de vos enfants inscrits à l'établissement.</p>
                        </div>
                        <div class="col-md-4 d-none d-md-block" style="background: linear-gradient(135deg, var(--esbtp-green-light), var(--esbtp-green)); min-height: 180px;">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                <img src="https://img.freepik.com/free-vector/family-concept-illustration_114360-2047.jpg" alt="Parent Profile" class="img-fluid" style="max-height: 160px; opacity: 0.9;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <i class="fas fa-user-circle text-primary me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if(isset($user->profile_image))
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Photo de profil" class="rounded-circle img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                <i class="fas fa-user text-secondary" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                        <p class="text-muted">Parent</p>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                            <div class="text-muted me-3" style="width: 30px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <span>{{ $user->email }}</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                            <div class="text-muted me-3" style="width: 30px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Téléphone</small>
                                <span>{{ $user->phone ?? 'Non renseigné' }}</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0 py-3 d-flex border-0">
                            <div class="text-muted me-3" style="width: 30px;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Membre depuis</small>
                                <span>{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fas fa-edit me-2"></i> Modifier mon profil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mes enfants -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-child text-success me-2"></i>
                        <h5 class="card-title mb-0 fw-bold">Mes enfants</h5>
                    </div>
                    <span class="badge bg-success rounded-pill">{{ $children->count() }} enfant(s)</span>
                </div>
                <div class="card-body">
                    @if($children->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Photo</th>
                                        <th>Nom</th>
                                        <th>Classe</th>
                                        <th>Matricule</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($children as $child)
                                        <tr>
                                            <td>
                                                @if(isset($child->user->profile_image))
                                                    <img src="{{ asset('storage/' . $child->user->profile_image) }}" alt="Photo" class="rounded-circle" width="40" height="40">
                                                @else
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="fw-medium">{{ $child->user->name }}</td>
                                            <td>{{ $child->class->name ?? 'Non assignée' }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $child->registration_number }}</span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('parent.child.grades', $child->id) }}" class="btn btn-sm btn-outline-primary" title="Notes">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </a>
                                                    <a href="{{ route('parent.child.attendance', $child->id) }}" class="btn btn-sm btn-outline-info" title="Présences">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </a>
                                                    <a href="{{ route('parent.child.timetable', $child->id) }}" class="btn btn-sm btn-outline-success" title="Emploi du temps">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-child text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="text-muted">Aucun enfant associé</h5>
                            <p class="text-muted">Veuillez contacter l'administration pour associer vos enfants à votre compte.</p>
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#contactAdminModal">
                                <i class="fas fa-envelope me-2"></i> Contacter l'administration
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification du profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="editProfileModalLabel">Modifier mon profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Photo de profil</label>
                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                        <small class="text-muted">Formats acceptés : JPG, PNG. Max 2MB.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de contact avec l'administration -->
<div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="contactAdminModalLabel">Contacter l'administration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="recipient_type" value="admin">
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Sujet</label>
                        <input type="text" class="form-control" id="subject" name="subject" value="Association d'enfants à mon compte parent" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Décrivez votre demande..."></textarea>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        font-weight: 600;
        color: #555;
    }
    
    .table tbody tr {
        transition: all 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: var(--esbtp-light-green);
    }
</style>
@endsection 