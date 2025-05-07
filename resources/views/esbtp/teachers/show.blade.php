@extends('layouts.app')

@section('title', 'Détails de l\'enseignant')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">{{ $teacher->user->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('esbtp.teachers.index') }}">Enseignants</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-graduate me-1"></i>
                        Informations de l'enseignant
                    </div>
                    <div>
                        <a href="{{ route('esbtp.teachers.edit', $teacher->id) }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
                <div class="card-body">
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

                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">Informations personnelles</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Nom complet</th>
                                    <td>{{ $teacher->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $teacher->user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Nom d'utilisateur</th>
                                    <td>{{ $teacher->user->username }}</td>
                                </tr>
                                <tr>
                                    <th>Téléphone</th>
                                    <td>{{ $teacher->user->phone ?? 'Non renseigné' }}</td>
                                </tr>
                                <tr>
                                    <th>Statut du compte</th>
                                    <td>
                                        @if($teacher->user->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">Informations professionnelles</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Numéro d'employé</th>
                                    <td>{{ $teacher->employee_id ?? 'Non renseigné' }}</td>
                                </tr>
                                <tr>
                                    <th>Département</th>
                                    <td>{{ $teacher->department->name ?? 'Non affecté' }}</td>
                                </tr>
                                <tr>
                                    <th>Laboratoire</th>
                                    <td>{{ $teacher->laboratory->name ?? 'Non affecté' }}</td>
                                </tr>
                                <tr>
                                    <th>Grade</th>
                                    <td>{{ $teacher->grade ?? 'Non renseigné' }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>{{ $teacher->status ?? 'Non renseigné' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4 class="text-primary mb-3">Enseignement</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Heures d'enseignement</h5>
                                            <p class="card-text">
                                                <strong>Heures dues:</strong> {{ $teacher->teaching_hours_due ?? '0' }} h<br>
                                                <strong>Heures effectuées:</strong> {{ $teacher->teaching_hours_done ?? '0' }} h
                                            </p>
                                            @if($teacher->teaching_hours_due > 0)
                                                <div class="progress mt-2">
                                                    @php
                                                        $percentage = min(100, ($teacher->teaching_hours_done / $teacher->teaching_hours_due) * 100);
                                                    @endphp
                                                    <div class="progress-bar {{ $percentage >= 100 ? 'bg-success' : 'bg-info' }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ round($percentage) }}%</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Bureau</h5>
                                            <p class="card-text">{{ $teacher->office_location ?? 'Non renseigné' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">Site web</h5>
                                            <p class="card-text">
                                                @if($teacher->website)
                                                    <a href="{{ $teacher->website }}" target="_blank">{{ $teacher->website }}</a>
                                                @else
                                                    Non renseigné
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">Spécialités</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @if(is_array($teacher->specialties) && count($teacher->specialties) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($teacher->specialties as $specialty)
                                                <span class="badge bg-secondary">{{ $specialty }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="card-text">Aucune spécialité renseignée</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h4 class="text-primary mb-3">Intérêts de recherche</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    @if(is_array($teacher->research_interests) && count($teacher->research_interests) > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($teacher->research_interests as $interest)
                                                <span class="badge bg-info text-dark">{{ $interest }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="card-text">Aucun intérêt de recherche renseigné</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($teacher->bio)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4 class="text-primary mb-3">Biographie</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $teacher->bio }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h4 class="text-primary mb-3">Informations système</h4>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th style="width: 20%">ID</th>
                                    <td>{{ $teacher->id }}</td>
                                </tr>
                                <tr>
                                    <th>ID Utilisateur</th>
                                    <td>{{ $teacher->user_id }}</td>
                                </tr>
                                <tr>
                                    <th>Créé par</th>
                                    <td>{{ optional($teacher->createdBy)->name ?? 'Système' }}</td>
                                </tr>
                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $teacher->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière mise à jour</th>
                                    <td>{{ $teacher->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Mis à jour par</th>
                                    <td>{{ optional($teacher->updatedBy)->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('esbtp.teachers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'enseignant <strong>{{ $teacher->user->name }}</strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action désactivera également le compte utilisateur associé et supprimera toutes les associations avec cet enseignant.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.teachers.destroy', $teacher->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 