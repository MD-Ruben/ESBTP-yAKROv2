@extends('layouts.app')

@section('title', 'Formation : ' . $formation->name . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la formation : {{ $formation->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.formations.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-list me-1"></i>Liste des formations
                        </a>
                        <a href="{{ route('esbtp.formations.edit', $formation) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations générales</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%;">Nom :</th>
                                                <td>{{ $formation->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Code :</th>
                                                <td>{{ $formation->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Statut :</th>
                                                <td>
                                                    @if($formation->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Filières :</th>
                                                <td>
                                                    @if($formation->filieres->count() > 0)
                                                        @foreach($formation->filieres as $filiere)
                                                            <span class="badge bg-primary">{{ $filiere->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">Aucune filière associée</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Niveaux d'études :</th>
                                                <td>
                                                    @if($formation->niveauxEtudes->count() > 0)
                                                        @foreach($formation->niveauxEtudes as $niveau)
                                                            <span class="badge bg-info">{{ $niveau->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">Aucun niveau d'étude associé</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date de création :</th>
                                                <td>{{ $formation->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dernière mise à jour :</th>
                                                <td>{{ $formation->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-align-left me-2"></i>Description</h6>
                                </div>
                                <div class="card-body">
                                    @if($formation->description)
                                        <div class="p-3 bg-light rounded">
                                            {{ $formation->description }}
                                        </div>
                                    @else
                                        <p class="text-muted text-center my-3">
                                            <i class="fas fa-info-circle me-1"></i>Aucune description fournie pour cette formation.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Filières associées -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-sitemap me-2"></i>Filières associées ({{ $formation->filieres->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($formation->filieres->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Code</th>
                                                        <th>Type</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formation->filieres as $filiere)
                                                        <tr>
                                                            <td>{{ $filiere->name }}</td>
                                                            <td>{{ $filiere->code }}</td>
                                                            <td>
                                                                @if($filiere->type == 'technique')
                                                                    <span class="badge bg-info">Technique</span>
                                                                @elseif($filiere->type == 'professionnelle')
                                                                    <span class="badge bg-success">Professionnelle</span>
                                                                @elseif($filiere->type == 'universitaire')
                                                                    <span class="badge bg-primary">Universitaire</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ $filiere->type }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('esbtp.filieres.show', $filiere) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center my-3">
                                            <i class="fas fa-info-circle me-1"></i>Aucune filière associée à cette formation.
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Niveaux d'études associés -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Niveaux d'études associés ({{ $formation->niveauxEtudes->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($formation->niveauxEtudes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Code</th>
                                                        <th>Année</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formation->niveauxEtudes as $niveau)
                                                        <tr>
                                                            <td>{{ $niveau->name }}</td>
                                                            <td>{{ $niveau->code }}</td>
                                                            <td>
                                                                @if($niveau->niveau == 1)
                                                                    <span class="badge bg-secondary">1ère année</span>
                                                                @else
                                                                    <span class="badge bg-info">{{ $niveau->niveau }}ème année</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('esbtp.niveaux-etudes.show', $niveau) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center my-3">
                                            <i class="fas fa-info-circle me-1"></i>Aucun niveau d'étude associé à cette formation.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Matières associées -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-book me-2"></i>Matières associées ({{ $formation->matieres->count() }})</h6>
                                    <a href="{{ route('esbtp.matieres.create', ['formation_id' => $formation->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Ajouter une matière
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($formation->matieres->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Code</th>
                                                        <th>Coefficient</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formation->matieres as $matiere)
                                                        <tr>
                                                            <td>{{ $matiere->name }}</td>
                                                            <td>{{ $matiere->code }}</td>
                                                            <td>{{ $matiere->coefficient_default }}</td>
                                                            <td>
                                                                <a href="{{ route('esbtp.matieres.show', $matiere) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center my-3">
                                            <i class="fas fa-info-circle me-1"></i>Aucune matière associée à cette formation.
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Classes associées -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Classes associées ({{ $formation->classes->count() }})</h6>
                                    <a href="{{ route('esbtp.classes.create', ['formation_id' => $formation->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Ajouter une classe
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($formation->classes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Filière</th>
                                                        <th>Niveau d'étude</th>
                                                        <th>Année académique</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formation->classes as $classe)
                                                        <tr>
                                                            <td>{{ $classe->name }}</td>
                                                            <td>{{ $classe->filiere->name ?? '-' }}</td>
                                                            <td>{{ $classe->niveauEtude->name ?? '-' }}</td>
                                                            <td>{{ $classe->anneeAcademique->name ?? '-' }}</td>
                                                            <td>
                                                                <a href="{{ route('esbtp.classes.show', $classe) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center my-3">
                                            <i class="fas fa-info-circle me-1"></i>Aucune classe associée à cette formation.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                        <a href="{{ route('esbtp.formations.edit', $formation) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette formation ?</p>
                <p><strong>Nom :</strong> {{ $formation->name }}</p>
                
                @if($formation->classes->count() > 0 || $formation->matieres->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette formation est liée à :
                        <ul class="mb-0 mt-1">
                            @if($formation->classes->count() > 0)
                                <li>{{ $formation->classes->count() }} classe(s)</li>
                            @endif
                            @if($formation->matieres->count() > 0)
                                <li>{{ $formation->matieres->count() }} matière(s)</li>
                            @endif
                        </ul>
                        La suppression de cette formation pourrait causer des erreurs dans le système. Assurez-vous de supprimer ou de réaffecter ces éléments avant de continuer.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.formations.destroy', $formation) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection