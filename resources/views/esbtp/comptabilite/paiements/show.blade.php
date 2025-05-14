@extends('layouts.app')

@section('title', 'Détails du paiement')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Détails du paiement: {{ $paiement->reference_paiement }}</h5>
            <div>
                @if($paiement->statut != 'completé')
                <a href="{{ route('esbtp.comptabilite.paiements.edit', $paiement->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                @endif
                <a href="{{ route('esbtp.comptabilite.paiements.recu', $paiement->id) }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-file-invoice me-1"></i> Générer reçu
                </a>
                <a href="{{ route('esbtp.comptabilite.paiements') }}" class="btn btn-secondary btn-sm">
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
                            <h6 class="mb-0">Informations du paiement</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Référence:</p>
                                    <p>{{ $paiement->reference_paiement }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Type:</p>
                                    <p>{{ $paiement->type_paiement }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Montant:</p>
                                    <p class="fs-4 text-primary">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Date de paiement:</p>
                                    <p>{{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Mode de paiement:</p>
                                    <p>{{ ucfirst($paiement->mode_paiement) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Numéro de transaction:</p>
                                    <p>{{ $paiement->numero_transaction ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Statut:</p>
                                    <p>
                                        @if($paiement->statut == 'completé')
                                        <span class="badge bg-success">Complété</span>
                                        @elseif($paiement->statut == 'en_attente')
                                        <span class="badge bg-warning">En attente</span>
                                        @elseif($paiement->statut == 'annulé')
                                        <span class="badge bg-danger">Annulé</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $paiement->statut }}</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Date d'échéance:</p>
                                    <p>{{ $paiement->date_echeance ? $paiement->date_echeance->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            @if($paiement->description)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <p class="mb-1 fw-bold">Description:</p>
                                    <p>{{ $paiement->description }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Informations de l'étudiant</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Nom complet:</p>
                                    <p>{{ $paiement->etudiant->nom ?? '' }} {{ $paiement->etudiant->prenom ?? '' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Matricule:</p>
                                    <p>{{ $paiement->etudiant->matricule ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Classe:</p>
                                    <p>{{ $paiement->etudiant->classe->nom ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold">Année universitaire:</p>
                                    <p>{{ $paiement->anneeUniversitaire->nom ?? $paiement->anneeUniversitaire->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('esbtp.etudiants.show', $paiement->etudiant_id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user me-1"></i> Voir le profil de l'étudiant
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('esbtp.comptabilite.paiements.recu', $paiement->id) }}" class="btn btn-success" target="_blank">
                                    <i class="fas fa-file-invoice me-1"></i> Générer reçu
                                </a>
                                
                                @if($paiement->statut != 'completé')
                                <a href="{{ route('esbtp.comptabilite.paiements.edit', $paiement->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
                                @endif
                                
                                @if($paiement->statut == 'en_attente')
                                <form action="{{ route('esbtp.comptabilite.paiements.valider', $paiement->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check-circle me-1"></i> Valider ce paiement
                                    </button>
                                </form>
                                
                                <form action="{{ route('esbtp.comptabilite.paiements.rejeter', $paiement->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter ce paiement?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times-circle me-1"></i> Rejeter ce paiement
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0">Informations système</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>ID:</strong> {{ $paiement->id }}</p>
                            <p><strong>Créé par:</strong> {{ $paiement->createur->name ?? 'N/A' }}</p>
                            <p><strong>Date de création:</strong> {{ $paiement->created_at->format('d/m/Y H:i') }}</p>
                            
                            @if($paiement->updated_at)
                            <p><strong>Dernière modification:</strong> {{ $paiement->updated_at->format('d/m/Y H:i') }}</p>
                            @endif
                            
                            @if($paiement->date_validation)
                            <p><strong>Date de validation:</strong> {{ $paiement->date_validation->format('d/m/Y H:i') }}</p>
                            <p><strong>Validé par:</strong> {{ $paiement->validateur->name ?? 'N/A' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 