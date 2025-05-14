@extends('layouts.app')

@section('title', 'Détails enseignant')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Détails de l'enseignant: {{ $enseignant->name }}</h5>
            <div>
                <a href="{{ route('esbtp.enseignants.edit', $enseignant->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="{{ route('esbtp.enseignants.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="row">
                <div class="col-md-8">
                    <h6 class="border-bottom pb-2 mb-3">Informations personnelles</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Nom complet:</strong> {{ $enseignant->name }}</p>
                            <p><strong>Nom d'utilisateur:</strong> {{ $enseignant->username }}</p>
                            <p><strong>Email:</strong> {{ $enseignant->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Téléphone:</strong> {{ $enseignant->telephone ?? 'Non renseigné' }}</p>
                            <p><strong>Spécialité:</strong> {{ $enseignant->specialite ?? 'Non renseignée' }}</p>
                            <p><strong>Statut:</strong> 
                                @if($enseignant->is_active)
                                <span class="badge bg-success">Actif</span>
                                @else
                                <span class="badge bg-danger">Inactif</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <p><strong>Adresse:</strong> {{ $enseignant->adresse ?? 'Non renseignée' }}</p>
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3">Rôles et permissions</h6>
                    <div class="mb-4">
                        <p><strong>Rôles:</strong></p>
                        <div>
                            @foreach($enseignant->getRoleNames() as $role)
                                <span class="badge bg-info me-1 mb-1">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <h6 class="border-bottom pb-2 mb-3">Matières enseignées</h6>
                    <div class="mb-4">
                        @if($enseignant->matieres->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enseignant->matieres as $matiere)
                                    <tr>
                                        <td>{{ $matiere->code }}</td>
                                        <td>{{ $matiere->nom }}</td>
                                        <td>
                                            @foreach($matiere->classes as $classe)
                                                <span class="badge bg-secondary me-1">{{ $classe->code }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted fst-italic">Aucune matière assignée à cet enseignant</p>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('esbtp.enseignants.edit', $enseignant->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Modifier les informations
                                </a>
                                
                                @if($enseignant->hasRole('superAdmin'))
                                <form action="{{ route('esbtp.enseignants.demote', $enseignant->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rétrograder cet enseignant du rang de Super Admin?')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-level-down-alt me-1"></i> Rétrograder du rang de Super Admin
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('esbtp.enseignants.promote', $enseignant->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir promouvoir cet enseignant au rang de Super Admin?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-level-up-alt me-1"></i> Promouvoir au rang de Super Admin
                                    </button>
                                </form>
                                @endif
                                
                                <form action="{{ route('esbtp.enseignants.destroy', $enseignant->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant? Cette action est irréversible.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash-alt me-1"></i> Supprimer l'enseignant
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informations système</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $enseignant->id }}</p>
                            <p><strong>Date de création:</strong> {{ $enseignant->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Dernière modification:</strong> {{ $enseignant->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 