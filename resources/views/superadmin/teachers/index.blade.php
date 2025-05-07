@extends('layouts.app')

@section('title', 'Gestion des Enseignants')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Gestion des Enseignants</h1>
    <p class="mb-4">Gérez tous les enseignants de l'établissement.</p>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item active" aria-current="page">Enseignants</li>
        </ol>
    </nav>

    <!-- Affichage des messages de succès et d'erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Enseignants</h6>
            <a href="{{ route('superadmin.teachers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter un enseignant
            </a>
        </div>
        <div class="card-body">
            <!-- Filtres de recherche -->
            <form action="{{ route('superadmin.teachers.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par nom ou email" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="department_id" class="form-control">
                            <option value="">Tous les départements</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="status" class="form-control">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tableau des enseignants -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Département</th>
                            <th>Fonction</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>{{ $teacher->employee_id }}</td>
                                <td class="text-center">
                                    @if($teacher->profile_picture)
                                        <img src="{{ asset('storage/' . $teacher->profile_picture) }}" alt="Photo" class="img-profile rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('img/undraw_profile.svg') }}" alt="Photo" class="img-profile rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                </td>
                                <td>{{ $teacher->user->name }}</td>
                                <td>{{ $teacher->user->email }}</td>
                                <td>{{ $teacher->department->name ?? 'N/A' }}</td>
                                <td>{{ $teacher->designation->name ?? 'N/A' }}</td>
                                <td>
                                    @if($teacher->status == 'active')
                                        <span class="badge badge-success">Actif</span>
                                    @else
                                        <span class="badge badge-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('superadmin.teachers.show', $teacher->id) }}" class="btn btn-info btn-sm" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('superadmin.teachers.edit', $teacher->id) }}" class="btn btn-warning btn-sm" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" title="Supprimer" data-toggle="modal" data-target="#deleteModal{{ $teacher->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $teacher->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $teacher->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $teacher->id }}">Confirmation de suppression</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer l'enseignant <strong>{{ $teacher->user->name }}</strong> ?
                                                    <br>
                                                    <span class="text-danger">Cette action est irréversible.</span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('superadmin.teachers.destroy', $teacher->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucun enseignant trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $teachers->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 