@extends('layouts.app')

@section('title', 'Détails de la dépense')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Détails de la dépense: {{ $depense->reference ?? 'N°'.$depense->id }}</h5>
            <div>
                <a href="{{ route('esbtp.comptabilite.depenses.edit', $depense->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="#" class="btn btn-success btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </a>
                <a href="{{ route('esbtp.comptabilite.depenses') }}" class="btn btn-secondary btn-sm">
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
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Informations générales</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Libellé:</p>
                                    <p>{{ $depense->libelle }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Catégorie:</p>
                                    <p>{{ $depense->categorie ? $depense->categorie->nom : 'Non spécifiée' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Montant:</p>
                                    <p class="fs-4 text-danger">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Date de dépense:</p>
                                    <p>{{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : 'Non spécifiée' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Mode de paiement:</p>
                                    <p>{{ ucfirst($depense->mode_paiement ?? 'Non spécifié') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Référence:</p>
                                    <p>{{ $depense->reference ?? 'Non spécifiée' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Numéro de transaction:</p>
                                    <p>{{ $depense->numero_transaction ?? 'Non spécifié' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Statut:</p>
                                    <p>
                                        @if($depense->statut == 'validée')
                                        <span class="badge bg-success">Validée</span>
                                        @elseif($depense->statut == 'en attente')
                                        <span class="badge bg-warning">En attente</span>
                                        @elseif($depense->statut == 'annulée')
                                        <span class="badge bg-danger">Annulée</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $depense->statut ?? 'Non spécifié' }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($depense->description)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p class="mb-1 fw-bold">Description:</p>
                                    <p>{{ $depense->description }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($depense->fournisseur)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Informations du fournisseur</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Nom:</p>
                                    <p>{{ $depense->fournisseur->nom }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Téléphone:</p>
                                    <p>{{ $depense->fournisseur->telephone ?? 'Non spécifié' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Email:</p>
                                    <p>{{ $depense->fournisseur->email ?? 'Non spécifié' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Adresse:</p>
                                    <p>{{ $depense->fournisseur->adresse ?? 'Non spécifiée' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('esbtp.comptabilite.fournisseurs.show', $depense->fournisseur->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-building me-1"></i> Voir les détails du fournisseur
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($depense->notes_internes)
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">Notes internes</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $depense->notes_internes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('esbtp.comptabilite.depenses.edit', $depense->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
                                
                                <a href="#" class="btn btn-success" onclick="window.print()">
                                    <i class="fas fa-print me-1"></i> Imprimer ce justificatif
                                </a>
                                
                                @if($depense->path_justificatif)
                                <a href="{{ asset('storage/' . $depense->path_justificatif) }}" class="btn btn-info" target="_blank">
                                    <i class="fas fa-file-alt me-1"></i> Voir le justificatif original
                                </a>
                                @endif
                                
                                <form action="{{ route('esbtp.comptabilite.depenses.destroy', $depense->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash-alt me-1"></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Informations système</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $depense->id }}</p>
                            <p><strong>Créé par:</strong> {{ $depense->createur->name ?? 'Non spécifié' }}</p>
                            <p><strong>Date de création:</strong> {{ $depense->created_at->format('d/m/Y H:i') }}</p>
                            
                            @if($depense->updated_at && $depense->updated_at->ne($depense->created_at))
                            <p><strong>Dernière modification:</strong> {{ $depense->updated_at->format('d/m/Y H:i') }}</p>
                            @endif
                            
                            @if($depense->validateur)
                            <p><strong>Validé par:</strong> {{ $depense->validateur->name }}</p>
                            <p><strong>Date de validation:</strong> {{ $depense->date_validation ? $depense->date_validation->format('d/m/Y H:i') : 'Non spécifiée' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 