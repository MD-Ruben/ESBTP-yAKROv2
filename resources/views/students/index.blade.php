@extends('layouts.app')

@section('title', 'Gestion des Étudiants')

@section('content')
<div class="container-fluid">
    <!-- Carte de recherche et d'ajout -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <form action="{{ route('students.index') }}" method="GET" class="d-flex">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Rechercher un étudiant..." value="{{ request('search') }}">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="ms-2">
                                    <select name="class_id" class="form-select">
                                        <option value="">Toutes les classes</option>
                                        @foreach($classes ?? [] as $class)
                                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="ms-2">
                                    <button type="submit" class="btn btn-primary">Filtrer</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('students.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-1"></i> Ajouter un étudiant
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des étudiants -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0">Liste des Étudiants</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Photo</th>
                                    <th>Nom</th>
                                    <th>Matricule</th>
                                    <th>Classe</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students ?? [] as $student)
                                    <tr>
                                        <td>{{ $student->id }}</td>
                                        <td>
                                            @if($student->profile_image)
                                                <img src="{{ asset('storage/' . $student->profile_image) }}" alt="Photo" class="rounded-circle" width="40" height="40">
                                            @else
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $student->user->name ?? 'N/A' }}</td>
                                        <td>{{ $student->registration_number }}</td>
                                        <td>{{ $student->class->name ?? 'N/A' }}</td>
                                        <td>{{ $student->user->email ?? 'N/A' }}</td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->user && $student->user->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info text-white" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-warning text-white" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Supprimer" 
                                                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet étudiant?')) { 
                                                            document.getElementById('delete-form-{{ $student->id }}').submit(); 
                                                        }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $student->id }}" action="{{ route('students.destroy', $student->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucun étudiant trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $students->links() ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 