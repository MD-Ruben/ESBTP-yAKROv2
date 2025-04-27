@extends('layouts.app')

@section('title', 'Gestion des paiements')

@section('styles')
<style>
    .finance-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        border: none;
        margin-bottom: 20px;
    }
    
    .finance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }
    
    .finance-card .card-body {
        padding: 20px;
    }
    
    .finance-amount {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
        line-height: 1.2;
    }
    
    .finance-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0;
        opacity: 0.8;
    }
    
    .finance-icon {
        font-size: 2.5rem;
        opacity: 0.3;
        position: absolute;
        right: 20px;
        top: 20px;
    }
    
    .paiements-card {
        background: linear-gradient(135deg, #1a73e8 0%, #4285f4 100%);
        color: white;
    }
    
    .recettes-card {
        background: linear-gradient(135deg, #1e8e3e 0%, #34a853 100%);
        color: white;
    }
    
    .attente-card {
        background: linear-gradient(135deg, #fbbc04 0%, #fdd663 100%);
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des paiements</h5>
            <a href="{{ route('esbtp.comptabilite.paiements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nouveau paiement
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <!-- Filtres de recherche -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i> Filtres
                </div>
                <div class="card-body">
                    <form action="{{ route('esbtp.comptabilite.paiements') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="etudiant" class="form-label">Étudiant</label>
                            <input type="text" class="form-control" id="etudiant" name="etudiant" placeholder="Nom ou matricule" value="{{ request('etudiant') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label">Date début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin" class="form-label">Date fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                <option value="">Tous</option>
                                <option value="completé" {{ request('statut') == 'completé' ? 'selected' : '' }}>Complété</option>
                                <option value="en attente" {{ request('statut') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                <option value="annulé" {{ request('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Rechercher
                            </button>
                            <a href="{{ route('esbtp.comptabilite.paiements') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tableau des paiements -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Étudiant</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Mode de paiement</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $paiement)
                        <tr>
                            <td>{{ $paiement->reference_paiement }}</td>
                            <td>{{ $paiement->etudiant->nom }} {{ $paiement->etudiant->prenom }}</td>
                            <td>{{ $paiement->type_paiement }}</td>
                            <td class="text-end">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $paiement->mode_paiement }}</td>
                            <td>{{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}</td>
                            <td>
                                @if($paiement->statut == 'completé')
                                <span class="badge bg-success">Complété</span>
                                @elseif($paiement->statut == 'en attente')
                                <span class="badge bg-warning">En attente</span>
                                @elseif($paiement->statut == 'annulé')
                                <span class="badge bg-danger">Annulé</span>
                                @else
                                <span class="badge bg-secondary">{{ $paiement->statut }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('esbtp.comptabilite.paiements.show', $paiement->id) }}" class="btn btn-sm btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($paiement->statut != 'completé')
                                    <a href="{{ route('esbtp.comptabilite.paiements.edit', $paiement->id) }}" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    
                                    <a href="{{ route('esbtp.comptabilite.paiements.recu', $paiement->id) }}" class="btn btn-sm btn-success" title="Générer reçu" target="_blank">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    
                                    @if($paiement->statut == 'en attente')
                                    <form action="{{ route('esbtp.comptabilite.paiements.valider', $paiement->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('esbtp.comptabilite.paiements.rejeter', $paiement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir rejeter ce paiement?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucun paiement trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $paiements->withQueryString()->links() }}
            </div>
            
            <!-- Résumé financier -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Résumé financier</h5>
                </div>
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card finance-card paiements-card h-100">
                                <div class="card-body">
                                    <i class="fas fa-money-bill-wave finance-icon"></i>
                                    <p class="finance-label">Total des paiements</p>
                                    <h2 class="finance-amount">{{ number_format($paiements->sum('montant'), 0, ',', ' ') }} FCFA</h2>
                                    <p class="mb-0 opacity-75">Tous les paiements enregistrés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card finance-card recettes-card h-100">
                                <div class="card-body">
                                    <i class="fas fa-check-circle finance-icon"></i>
                                    <p class="finance-label">Paiements complétés</p>
                                    <h2 class="finance-amount">{{ number_format($paiements->where('statut', 'completé')->sum('montant'), 0, ',', ' ') }} FCFA</h2>
                                    <p class="mb-0 opacity-75">Paiements validés et complétés</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card finance-card attente-card h-100">
                                <div class="card-body">
                                    <i class="fas fa-clock finance-icon"></i>
                                    <p class="finance-label">Paiements en attente</p>
                                    <h2 class="finance-amount">{{ number_format($paiements->where('statut', 'en attente')->sum('montant'), 0, ',', ' ') }} FCFA</h2>
                                    <p class="mb-0 opacity-75">Paiements en attente de validation</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 