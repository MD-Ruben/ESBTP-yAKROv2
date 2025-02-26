@extends('layouts.app')

@section('title', 'Gestion des Enseignants')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Enseignants</h1>
        @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Ajouter un Enseignant
            </a>
        @endif
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Carte pour la recherche et le filtrage -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recherche et Filtrage</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('teachers.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nom, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="department" class="form-label">Département</label>
                    <select class="form-select" id="department" name="department">
                        <option value="">Tous les départements</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
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
                                Total des Départements</div>
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
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des enseignants -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Enseignants</h6>
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
                                    @if($teacher->photo)
                                        <img src="{{ asset('storage/' . $teacher->photo) }}" 
                                             alt="Photo" class="rounded-circle" width="40" height="40">
                                    @else
                                        <img src="{{ asset('images/default-avatar.png') }}" 
                                             alt="Default" class="rounded-circle" width="40" height="40">
                                    @endif
                                </td>
                                <td>{{ $teacher->first_name }} {{ $teacher->last_name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->department->name ?? 'N/A' }}</td>
                                <td>
                                    @if($teacher->is_active)
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
                                        <a href="{{ route('teachers.schedule', $teacher->id) }}" 
                                           class="btn btn-sm btn-success" title="Emploi du temps">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <a href="{{ route('teachers.edit', $teacher->id) }}" 
                                               class="btn btn-sm btn-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteModal{{ $teacher->id }}"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Modal de suppression -->
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                        <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir supprimer l'enseignant : 
                                                           <strong>{{ $teacher->first_name }} {{ $teacher->last_name }}</strong> ?</p>
                                                        <p class="text-danger">Cette action est irréversible.</p>
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

    <!-- Section des réclamations (visible uniquement pour les enseignants) -->
    @if(auth()->check() && auth()->user()->role === 'teacher')
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Mes Réclamations d'Emploi du Temps</h6>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newClaimModal">
                    <i class="fas fa-plus-circle"></i> Nouvelle Réclamation
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                    <td>{{ $claim->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $claim->subject }}</td>
                                    <td>{{ $claim->description }}</td>
                                    <td>
                                        @if($claim->status === 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($claim->status === 'approved')
                                            <span class="badge bg-success">Approuvée</span>
                                        @elseif($claim->status === 'rejected')
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

        <!-- Modal pour nouvelle réclamation -->
        <div class="modal fade" id="newClaimModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvelle Réclamation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('schedule-claims.store') }}" method="POST">
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
                            <button type="submit" class="btn btn-primary">Soumettre</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection 