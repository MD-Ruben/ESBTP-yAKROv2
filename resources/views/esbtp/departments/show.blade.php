@extends('layouts.app')

@section('title', 'Détails du Département')
@section('page_title', 'Informations du Département')

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Carte d'information du département -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ $department->name }}</h5>
                <span class="badge {{ $department->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $department->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            <div class="card-body text-center">
                @if($department->logo)
                    <img src="{{ asset('storage/' . $department->logo) }}" class="img-fluid rounded mb-3" style="max-height: 200px;" alt="Logo du département">
                @else
                    <div class="bg-light rounded p-4 mb-3">
                        <i class="fas fa-building fa-5x text-secondary"></i>
                        <p class="text-muted mt-2">Aucun logo</p>
                    </div>
                @endif
                
                <h4>{{ $department->name }}</h4>
                <h6 class="text-muted">Code: {{ $department->code }}</h6>
                
                @if($department->head_name)
                    <p class="mt-3">
                        <i class="fas fa-user-tie me-2"></i> <strong>Responsable:</strong> {{ $department->head_name }}
                    </p>
                @endif
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('esbtp.departments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <div>
                        <a href="{{ route('esbtp.departments.edit', $department) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Description et détails -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Description</h5>
            </div>
            <div class="card-body">
                @if($department->description)
                    <div class="mb-4">
                        {!! $department->description !!}
                    </div>
                @else
                    <p class="text-muted">Aucune description disponible pour ce département.</p>
                @endif
                
                <hr>
                
                <h5>Informations complémentaires</h5>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong><i class="fas fa-calendar-alt me-2"></i> Créé le:</strong>
                            <p>{{ $department->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong><i class="fas fa-edit me-2"></i> Dernière modification:</strong>
                            <p>{{ $department->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $department->specialties_count ?? 0 }}</h3>
                                <p>Spécialités</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <a href="{{ route('esbtp.specialties.index', ['department_id' => $department->id]) }}" class="small-box-footer">
                                Voir les spécialités <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $department->cycles_count ?? 0 }}</h3>
                                <p>Cycles</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <a href="{{ route('esbtp.cycles.index', ['department_id' => $department->id]) }}" class="small-box-footer">
                                Voir les cycles <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $department->students_count ?? 0 }}</h3>
                                <p>Étudiants</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Voir les étudiants <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
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
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le département <strong>{{ $department->name }}</strong> ?</p>
                <p class="text-danger">Cette action est réversible, mais supprimera temporairement l'accès à ce département.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.departments.destroy', $department) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 