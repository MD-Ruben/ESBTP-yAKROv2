@extends('layouts.app')

@section('title', 'Matière : ' . $matiere->name . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la matière : {{ $matiere->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.matieres.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-list me-1"></i>Liste des matières
                        </a>
                        <a href="{{ route('esbtp.matieres.edit', $matiere) }}" class="btn btn-primary">
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
                                                <td>{{ $matiere->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Code :</th>
                                                <td>{{ $matiere->code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Coefficient :</th>
                                                <td>{{ $matiere->coefficient_default }}</td>
                                            </tr>
                                            <tr>
                                                <th>Volume horaire :</th>
                                                <td>{{ $matiere->total_heures_default }} heures</td>
                                            </tr>
                                            <tr>
                                                <th>Répartition :</th>
                                                <td>
                                                    <span class="badge bg-info">CM : {{ $matiere->heures_cm_default }} h</span>
                                                    <span class="badge bg-success">TD : {{ $matiere->heures_td_default }} h</span>
                                                    <span class="badge bg-warning">TP : {{ $matiere->heures_tp_default }} h</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Unité d'enseignement :</th>
                                                <td>
                                                    @if($matiere->uniteEnseignement)
                                                        <a href="{{ route('esbtp.unites-enseignement.show', $matiere->uniteEnseignement) }}">
                                                            {{ $matiere->uniteEnseignement->name }} ({{ $matiere->uniteEnseignement->code }})
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Non assignée</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Statut :</th>
                                                <td>
                                                    @if($matiere->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date de création :</th>
                                                <td>{{ $matiere->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dernière mise à jour :</th>
                                                <td>{{ $matiere->updated_at->format('d/m/Y H:i') }}</td>
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
                                    @if($matiere->description)
                                        <div class="p-3 bg-light rounded">
                                            {{ $matiere->description }}
                                        </div>
                                    @else
                                        <p class="text-muted text-center my-3">
                                            <i class="fas fa-info-circle me-1"></i>Aucune description fournie pour cette matière.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Formations associées -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Formations associées ({{ $matiere->formations->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($matiere->formations->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Code</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matiere->formations as $formation)
                                                        <tr>
                                                            <td>{{ $formation->name }}</td>
                                                            <td>{{ $formation->code }}</td>
                                                            <td>
                                                                @if($formation->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('esbtp.formations.show', $formation) }}" class="btn btn-sm btn-info">
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
                                            <i class="fas fa-info-circle me-1"></i>Aucune formation associée à cette matière.
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Niveaux d'études associés -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Niveaux d'études associés ({{ $matiere->niveauxEtudes->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($matiere->niveauxEtudes->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Code</th>
                                                        <th>Diplôme</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matiere->niveauxEtudes as $niveau)
                                                        <tr>
                                                            <td>{{ $niveau->name }}</td>
                                                            <td>{{ $niveau->code }}</td>
                                                            <td>{{ $niveau->diplome ?? '-' }}</td>
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
                                            <i class="fas fa-info-circle me-1"></i>Aucun niveau d'étude associé à cette matière.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Enseignants associés -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Enseignants associés ({{ $matiere->enseignants->count() }})</h6>
                                    <a href="{{ route('esbtp.matieres.edit', $matiere) }}?section=enseignants" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Gérer les enseignants
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($matiere->enseignants->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Matricule</th>
                                                        <th>Spécialité</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matiere->enseignants as $enseignant)
                                                        <tr>
                                                            <td>{{ $enseignant->user->name }}</td>
                                                            <td>{{ $enseignant->matricule }}</td>
                                                            <td>{{ $enseignant->specialite ?? '-' }}</td>
                                                            <td>
                                                                <a href="{{ route('esbtp.enseignants.show', $enseignant) }}" class="btn btn-sm btn-info">
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
                                            <i class="fas fa-info-circle me-1"></i>Aucun enseignant associé à cette matière.
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Séances de cours -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Séances de cours ({{ $matiere->seancesCours->count() }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($matiere->seancesCours->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Emploi du temps</th>
                                                        <th>Jour</th>
                                                        <th>Horaire</th>
                                                        <th>Type</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matiere->seancesCours as $seance)
                                                        <tr>
                                                            <td>
                                                                @if($seance->emploiTemps && $seance->emploiTemps->classe)
                                                                    {{ $seance->emploiTemps->classe->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @switch($seance->jour)
                                                                    @case('lundi')
                                                                        Lundi
                                                                        @break
                                                                    @case('mardi')
                                                                        Mardi
                                                                        @break
                                                                    @case('mercredi')
                                                                        Mercredi
                                                                        @break
                                                                    @case('jeudi')
                                                                        Jeudi
                                                                        @break
                                                                    @case('vendredi')
                                                                        Vendredi
                                                                        @break
                                                                    @case('samedi')
                                                                        Samedi
                                                                        @break
                                                                    @default
                                                                        {{ $seance->jour }}
                                                                @endswitch
                                                            </td>
                                                            <td>{{ $seance->heure_debut }} - {{ $seance->heure_fin }}</td>
                                                            <td>
                                                                @switch($seance->type)
                                                                    @case('cm')
                                                                        <span class="badge bg-info">Cours magistral</span>
                                                                        @break
                                                                    @case('td')
                                                                        <span class="badge bg-success">Travaux dirigés</span>
                                                                        @break
                                                                    @case('tp')
                                                                        <span class="badge bg-warning">Travaux pratiques</span>
                                                                        @break
                                                                    @case('evaluation')
                                                                        <span class="badge bg-danger">Évaluation</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge bg-secondary">{{ $seance->type }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('esbtp.seances-cours.edit', $seance) }}" class="btn btn-sm btn-info">
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
                                            <i class="fas fa-info-circle me-1"></i>Aucune séance de cours programmée pour cette matière.
                                        </p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Évaluations -->
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Évaluations ({{ $matiere->evaluations->count() }})</h6>
                                    <a href="{{ route('esbtp.evaluations.create') }}?matiere_id={{ $matiere->id }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Ajouter une évaluation
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if($matiere->evaluations->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Titre</th>
                                                        <th>Classe</th>
                                                        <th>Type</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matiere->evaluations as $evaluation)
                                                        <tr>
                                                            <td>{{ $evaluation->titre }}</td>
                                                            <td>
                                                                @if($evaluation->classe)
                                                                    {{ $evaluation->classe->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @switch($evaluation->type)
                                                                    @case('devoir')
                                                                        <span class="badge bg-primary">Devoir</span>
                                                                        @break
                                                                    @case('examen')
                                                                        <span class="badge bg-danger">Examen</span>
                                                                        @break
                                                                    @case('tp')
                                                                        <span class="badge bg-warning">TP</span>
                                                                        @break
                                                                    @default
                                                                        <span class="badge bg-secondary">{{ $evaluation->type }}</span>
                                                                @endswitch
                                                            </td>
                                                            <td>{{ $evaluation->date->format('d/m/Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('esbtp.evaluations.show', $evaluation) }}" class="btn btn-sm btn-info">
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
                                            <i class="fas fa-info-circle me-1"></i>Aucune évaluation créée pour cette matière.
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
                        <a href="{{ route('esbtp.matieres.edit', $matiere) }}" class="btn btn-primary">
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
                <p>Êtes-vous sûr de vouloir supprimer cette matière ?</p>
                <p><strong>Nom :</strong> {{ $matiere->name }}</p>
                
                @if($matiere->seancesCours->count() > 0 || $matiere->evaluations->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette matière est liée à :
                        <ul class="mb-0 mt-1">
                            @if($matiere->seancesCours->count() > 0)
                                <li>{{ $matiere->seancesCours->count() }} séance(s) de cours</li>
                            @endif
                            @if($matiere->evaluations->count() > 0)
                                <li>{{ $matiere->evaluations->count() }} évaluation(s)</li>
                            @endif
                        </ul>
                        La suppression de cette matière pourrait causer des erreurs dans le système. Assurez-vous de supprimer ces éléments liés avant de continuer.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.matieres.destroy', $matiere) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 