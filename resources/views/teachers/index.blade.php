@extends('layouts.app')

@section('title', 'Gestion des Enseignants')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Enseignants</h1>
        @if(auth()->user()->hasRole('admin'))
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTeacherModal">
            <i class="fas fa-user-plus"></i> Ajouter un Enseignant
        </button>
        @endif
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtres de recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recherche et Filtrage</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('teachers.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nom, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">Département</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">Tous les départements</option>
                        @foreach($departments ?? [] as $department)
                            <option value="{{ $department->id }}" 
                                {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques des enseignants -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Enseignants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeachers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Enseignants Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeTeachers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Départements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalDepartments ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Réclamations en Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingClaims ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des enseignants -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Enseignants</h6>
            <button class="btn btn-sm btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Nom Complet</th>
                            <th>Email</th>
                            <th>Département</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers ?? [] as $teacher)
                            <tr>
                                <td>{{ $teacher->id }}</td>
                                <td>
                                    <img src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : asset('images/default-avatar.png') }}" 
                                         alt="Photo" class="rounded-circle" width="40" height="40">
                                </td>
                                <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>
                                    @if($teacher->department)
                                        <span class="badge bg-info">{{ $teacher->department->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    @if($teacher->status == 'active')
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teachers.show', $teacher->id) }}" 
                                           class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('timetable.teacher', $teacher->id) }}" 
                                           class="btn btn-sm btn-primary" title="Emploi du temps">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        @if(auth()->user()->hasRole('admin'))
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editTeacherModal{{ $teacher->id }}" 
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteTeacherModal{{ $teacher->id }}" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Modal de modification -->
                                    @if(auth()->user()->hasRole('admin'))
                                        <div class="modal fade" id="editTeacherModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editTeacherModalLabel">Modifier l'Enseignant</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="first_name{{ $teacher->id }}" class="form-label">Prénom</label>
                                                                    <input type="text" class="form-control" id="first_name{{ $teacher->id }}" 
                                                                           name="first_name" value="{{ $teacher->first_name }}" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="last_name{{ $teacher->id }}" class="form-label">Nom</label>
                                                                    <input type="text" class="form-control" id="last_name{{ $teacher->id }}" 
                                                                           name="last_name" value="{{ $teacher->last_name }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="email{{ $teacher->id }}" class="form-label">Email</label>
                                                                    <input type="email" class="form-control" id="email{{ $teacher->id }}" 
                                                                           name="email" value="{{ $teacher->email }}" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="phone{{ $teacher->id }}" class="form-label">Téléphone</label>
                                                                    <input type="tel" class="form-control" id="phone{{ $teacher->id }}" 
                                                                           name="phone" value="{{ $teacher->phone }}">
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="department_id{{ $teacher->id }}" class="form-label">Département</label>
                                                                    <select class="form-select" id="department_id{{ $teacher->id }}" name="department_id">
                                                                        <option value="">Sélectionner un département</option>
                                                                        @foreach($departments ?? [] as $department)
                                                                            <option value="{{ $department->id }}" 
                                                                                {{ $teacher->department_id == $department->id ? 'selected' : '' }}>
                                                                                {{ $department->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="status{{ $teacher->id }}" class="form-label">Statut</label>
                                                                    <select class="form-select" id="status{{ $teacher->id }}" name="status">
                                                                        <option value="active" {{ $teacher->status == 'active' ? 'selected' : '' }}>Actif</option>
                                                                        <option value="inactive" {{ $teacher->status == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="photo{{ $teacher->id }}" class="form-label">Photo</label>
                                                                <input type="file" class="form-control" id="photo{{ $teacher->id }}" 
                                                                       name="photo" accept="image/*">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteTeacherModal{{ $teacher->id }}" tabindex="-1" aria-labelledby="deleteTeacherModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteTeacherModalLabel">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir supprimer l'enseignant <strong>{{ $teacher->first_name }} {{ $teacher->last_name }}</strong> ?</p>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            Cette action est irréversible.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun enseignant trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($teachers) && $teachers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Section des réclamations d'emploi du temps -->
    @if(auth()->user()->hasRole('teacher'))
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Mes Réclamations d'Emploi du Temps</h6>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createClaimModal">
                    <i class="fas fa-plus-circle"></i> Nouvelle Réclamation
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Sujet</th>
                                <th>Description</th>
                                <th>Statut</th>
                                <th>Réponse</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($claims ?? [] as $claim)
                                <tr>
                                    <td>{{ $claim->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $claim->subject }}</td>
                                    <td>{{ $claim->description }}</td>
                                    <td>
                                        @if($claim->status == 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($claim->status == 'approved')
                                            <span class="badge bg-success">Approuvée</span>
                                        @elseif($claim->status == 'rejected')
                                            <span class="badge bg-danger">Rejetée</span>
                                        @endif
                                    </td>
                                    <td>{{ $claim->response ?? 'Pas encore de réponse' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucune réclamation trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de création de réclamation -->
        <div class="modal fade" id="createClaimModal" tabindex="-1" aria-labelledby="createClaimModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createClaimModalLabel">Nouvelle Réclamation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('claims.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Sujet</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal d'ajout d'enseignant -->
    @if(auth()->user()->hasRole('admin'))
        <div class="modal fade" id="createTeacherModal" tabindex="-1" aria-labelledby="createTeacherModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createTeacherModalLabel">Ajouter un Enseignant</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label">Département</label>
                                    <select class="form-select" id="department_id" name="department_id">
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departments ?? [] as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Statut</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="active">Actif</option>
                                        <option value="inactive">Inactif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 