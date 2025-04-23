@extends('layouts.app')

@section('title', 'Mes Étudiants')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mes Étudiants</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Mes Étudiants</li>
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

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-user-graduate me-1"></i>
                Liste des étudiants
            </div>
            <div class="d-flex">
                <form action="{{ route('students.index') }}" method="GET" class="d-flex me-2">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Rechercher un étudiant..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="{{ route('students.index') }}">Tous les étudiants</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><h6 class="dropdown-header">Par classe</h6></li>
                        <!-- Ajouter dynamiquement les classes ici -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th>Classe</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>{{ $student->matricule }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $student->user->profile_image ? asset('storage/'.$student->user->profile_image) : asset('images/default-avatar.png') }}" 
                                             alt="Photo de profil" class="rounded-circle me-2" width="32" height="32">
                                        {{ $student->nom }} {{ $student->prenom }}
                                    </div>
                                </td>
                                <td>
                                    @if($student->inscription && $student->inscription->classe)
                                        {{ $student->inscription->classe->nom }}
                                    @else
                                        Non inscrit
                                    @endif
                                </td>
                                <td>{{ $student->user->email ?? 'Non défini' }}</td>
                                <td>{{ $student->telephone ?? 'Non défini' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('grades.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-success" title="Noter">
                                            <i class="fas fa-graduation-cap"></i>
                                        </a>
                                        <a href="{{ route('attendances.mark') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-primary" title="Marquer présence">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-info" title="Détails" 
                                                data-bs-toggle="modal" data-bs-target="#studentDetailModal{{ $student->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Modal de détails de l'étudiant -->
                            <div class="modal fade" id="studentDetailModal{{ $student->id }}" tabindex="-1" aria-labelledby="studentDetailModalLabel{{ $student->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="studentDetailModalLabel{{ $student->id }}">Détails de l'étudiant</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 text-center">
                                                    <img src="{{ $student->user->profile_image ? asset('storage/'.$student->user->profile_image) : asset('images/default-avatar.png') }}" 
                                                         alt="Photo de profil" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                                    <h5>{{ $student->nom }} {{ $student->prenom }}</h5>
                                                    <p class="text-muted">Matricule: {{ $student->matricule }}</p>
                                                </div>
                                                <div class="col-md-8">
                                                    <h6 class="border-bottom pb-2 mb-3">Informations personnelles</h6>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Date de naissance:</div>
                                                        <div class="col-md-8">{{ $student->date_naissance ? \Carbon\Carbon::parse($student->date_naissance)->format('d/m/Y') : 'Non définie' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Lieu de naissance:</div>
                                                        <div class="col-md-8">{{ $student->lieu_naissance ?? 'Non défini' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Genre:</div>
                                                        <div class="col-md-8">{{ $student->sexe ?? 'Non défini' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Email:</div>
                                                        <div class="col-md-8">{{ $student->user->email ?? 'Non défini' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Téléphone:</div>
                                                        <div class="col-md-8">{{ $student->telephone ?? 'Non défini' }}</div>
                                                    </div>
                                                    
                                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Informations académiques</h6>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Classe actuelle:</div>
                                                        <div class="col-md-8">
                                                            @if($student->inscription && $student->inscription->classe)
                                                                {{ $student->inscription->classe->nom }}
                                                            @else
                                                                Non inscrit
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Filière:</div>
                                                        <div class="col-md-8">
                                                            @if($student->inscription && $student->inscription->classe && $student->inscription->classe->filiere)
                                                                {{ $student->inscription->classe->filiere->nom }}
                                                            @else
                                                                Non définie
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-md-4 fw-bold">Année d'inscription:</div>
                                                        <div class="col-md-8">
                                                            @if($student->inscription && $student->inscription->annee_universitaire)
                                                                {{ $student->inscription->annee_universitaire->nom }}
                                                            @else
                                                                Non définie
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            <div class="btn-group">
                                                <a href="{{ route('grades.create') }}?student_id={{ $student->id }}" class="btn btn-success">
                                                    <i class="fas fa-graduation-cap me-2"></i> Noter
                                                </a>
                                                <a href="{{ route('attendances.mark') }}?student_id={{ $student->id }}" class="btn btn-primary">
                                                    <i class="fas fa-clipboard-check me-2"></i> Marquer présence
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun étudiant trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 