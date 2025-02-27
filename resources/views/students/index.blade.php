@extends('layouts.app')

@section('title', 'Gestion des Étudiants')

@section('content')
<!-- Hero Section -->
<div class="hero-section mb-5">
    <div class="hero-bg" style="background: linear-gradient(135deg, var(--esbtp-green), #0a8a4a); border-radius: 15px; margin: 0 15px;">
        <div class="container-fluid py-5">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white px-4">
                    <h1 class="display-4 fw-bold mb-3">Gestion des Étudiants</h1>
                    <p class="lead mb-4">Gérez efficacement tous vos étudiants, consultez leurs informations et suivez leur progression académique.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('students.create') }}" class="btn btn-light btn-lg px-4 py-2">
                            <i class="fas fa-plus-circle me-2"></i> Ajouter un étudiant
                        </a>
                        <a href="#student-list" class="btn btn-outline-light btn-lg px-4 py-2">
                            <i class="fas fa-list me-2"></i> Voir la liste
                        </a>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white rounded-circle p-2 me-3">
                                <i class="fas fa-graduation-cap text-success fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white">{{ $students->total() ?? 0 }} Étudiants</h5>
                                <small class="text-white-50">Inscrits dans notre établissement</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="position-relative">
                        <div class="position-absolute top-0 start-50 translate-middle-x" style="z-index: 1;">
                            <img src="https://img.freepik.com/free-vector/college-university-students-group-young-happy-people-standing-isolated-white-background_575670-66.jpg" alt="Students" class="img-fluid rounded-3 shadow-lg" style="max-height: 350px;">
                        </div>
                        <div class="position-absolute top-50 start-0 translate-middle-y" style="z-index: 0; opacity: 0.7;">
                            <div class="bg-white rounded-circle shadow-sm" style="width: 120px; height: 120px;"></div>
                        </div>
                        <div class="position-absolute bottom-0 end-0" style="z-index: 0; opacity: 0.7;">
                            <div class="bg-warning rounded-circle shadow-sm" style="width: 80px; height: 80px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Carte de recherche -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <form action="{{ route('students.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                                <div class="input-group flex-grow-1">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher un étudiant..." value="{{ request('search') }}">
                                </div>
                                <div>
                                    <select name="class_id" class="form-select">
                                        <option value="">Toutes les classes</option>
                                        @foreach($classes ?? [] as $class)
                                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-filter me-2"></i> Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des étudiants -->
    <div class="row" id="student-list">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-esbtp-green">
                        <i class="fas fa-users me-2"></i> Liste des Étudiants
                    </h5>
                    <a href="{{ route('students.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
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
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fw-medium">{{ $student->user->name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $student->registration_number }}</span></td>
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
                                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Supprimer" 
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
                                        <td colspan="9" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-user-graduate text-muted mb-3" style="font-size: 3rem;"></i>
                                                <h5 class="text-muted">Aucun étudiant trouvé</h5>
                                                <p class="text-muted">Ajoutez des étudiants pour les voir apparaître ici</p>
                                                <a href="{{ route('students.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus-circle me-2"></i> Ajouter un étudiant
                                                </a>
                                            </div>
                                        </td>
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

<!-- Ajout de styles personnalisés -->
<style>
    .hero-bg {
        position: relative;
        overflow: hidden;
    }
    
    .hero-bg::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .hero-bg::after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: -30px;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .text-esbtp-green {
        color: var(--esbtp-green);
    }
    
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