@extends('layouts.app')

@section('title', 'Détails de la salle : ' . $salle->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails de la salle</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.salles.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- En-tête avec le nom de la salle et les boutons d'action -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="m-0">{{ $salle->name }} <small class="text-muted">({{ $salle->code }})</small></h1>
                                <div>
                                    <a href="{{ route('esbtp.salles.edit', $salle) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations principales -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-door-open"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Type de salle</span>
                                    <span class="info-box-number">{{ $salle->type }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Capacité</span>
                                    <span class="info-box-number">{{ $salle->capacity }} places</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon {{ $salle->is_active ? 'bg-success' : 'bg-danger' }}"><i class="fas fa-toggle-on"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Statut</span>
                                    <span class="info-box-number">{{ $salle->is_active ? 'Active' : 'Inactive' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Localisation -->
                    <div class="card mt-4">
                        <div class="card-header bg-info">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt mr-2"></i> Localisation
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Bâtiment :</strong> {{ $salle->building ?? 'Non spécifié' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Étage :</strong> 
                                        @if($salle->floor == 0)
                                            Rez-de-chaussée
                                        @elseif($salle->floor < 0)
                                            {{ abs($salle->floor) }}e sous-sol
                                        @else
                                            {{ $salle->floor }}e étage
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Description</h3>
                        </div>
                        <div class="card-body">
                            @if($salle->description)
                                <p>{{ $salle->description }}</p>
                            @else
                                <p class="text-muted">Aucune description disponible.</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Inscriptions associées (à implémenter selon les besoins) -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Utilisation</h3>
                        </div>
                        <div class="card-body">
                            @if($salle->inscriptions && $salle->inscriptions->count() > 0)
                                <p>Cette salle est actuellement utilisée par {{ $salle->inscriptions->count() }} inscription(s).</p>
                                <a href="{{ route('esbtp.inscriptions.index', ['salle_id' => $salle->id]) }}" class="btn btn-info">
                                    <i class="fas fa-list"></i> Voir les inscriptions
                                </a>
                            @else
                                <p class="text-muted">Cette salle n'est actuellement associée à aucune inscription.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette salle ?</p>
                <p><strong>{{ $salle->name }} ({{ $salle->code }})</strong></p>
                
                @if($salle->inscriptions && $salle->inscriptions->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Cette salle est actuellement utilisée par <strong>{{ $salle->inscriptions->count() }}</strong> inscription(s). La suppression ne sera pas possible tant que des inscriptions sont associées à cette salle.
                    </div>
                @endif
                
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Cette action est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.salles.destroy', $salle) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" {{ ($salle->inscriptions && $salle->inscriptions->count() > 0) ? 'disabled' : '' }}>
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 