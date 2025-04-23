@extends('layouts.app')

@section('title', 'Tableau de bord financier')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-chart-line text-primary me-2"></i>Tableau de bord financier
                    </h5>
                    <div>
                        <a href="{{ route('comptabilite.paiements.create') }}" class="btn btn-sm btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>Nouveau paiement
                        </a>
                        <a href="{{ route('comptabilite.depenses.create') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-plus me-1"></i>Nouvelle dépense
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres de période -->
                    <form method="GET" action="{{ route('comptabilite.index') }}" class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_debut">Période du</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                       value="{{ $debut ?? now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date_fin">au</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                       value="{{ $fin ?? now()->endOfMonth()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                        </div>
                    </form>

                    <!-- Résumé financier -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Recettes</h5>
                                    <h2 class="display-6">{{ number_format($totalRecettes ?? 0, 0, ',', ' ') }} FCFA</h2>
                                    <p class="card-text">Total des paiements reçus</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Dépenses</h5>
                                    <h2 class="display-6">{{ number_format($totalDepenses ?? 0, 0, ',', ' ') }} FCFA</h2>
                                    <p class="card-text">Total des dépenses effectuées</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ ($balance ?? 0) >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Balance</h5>
                                    <h2 class="display-6">{{ number_format($balance ?? 0, 0, ',', ' ') }} FCFA</h2>
                                    <p class="card-text">Différence recettes - dépenses</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions récentes -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Paiements récents</h5>
                                    <a href="{{ route('comptabilite.paiements.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                                </div>
                                <div class="card-body">
                                    @if(isset($recentesPaiements) && $recentesPaiements->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Étudiant</th>
                                                        <th>Montant</th>
                                                        <th>Type</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentesPaiements as $paiement)
                                                        <tr>
                                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                                            <td>{{ $paiement->etudiant->user->name ?? 'N/A' }}</td>
                                                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                            <td>{{ $paiement->type_paiement }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">Aucun paiement récent</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Dépenses récentes</h5>
                                    <a href="{{ route('comptabilite.depenses.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
                                </div>
                                <div class="card-body">
                                    @if(isset($recentesDepenses) && $recentesDepenses->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Description</th>
                                                        <th>Catégorie</th>
                                                        <th>Montant</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentesDepenses as $depense)
                                                        <tr>
                                                            <td>{{ $depense->date->format('d/m/Y') }}</td>
                                                            <td>{{ $depense->description ?? $depense->libelle }}</td>
                                                            <td>{{ $depense->categorie }}</td>
                                                            <td>{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">Aucune dépense récente</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Étudiants avec paiements en retard -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Étudiants avec paiements en retard</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($etudiantsEnRetard) && $etudiantsEnRetard->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Étudiant</th>
                                                        <th>Classe</th>
                                                        <th>Montant dû</th>
                                                        <th>Date limite</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($etudiantsEnRetard as $etudiant)
                                                        <tr>
                                                            <td>{{ $etudiant->user->name }}</td>
                                                            <td>{{ $etudiant->classe->nom ?? 'Non assignée' }}</td>
                                                            <td>{{ number_format($etudiant->paiements->sum('montant'), 0, ',', ' ') }} FCFA</td>
                                                            <td>{{ $etudiant->paiements->first()->date_echeance->format('d/m/Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('comptabilite.paiements.create', ['etudiant_id' => $etudiant->id]) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-plus"></i> Paiement
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-success">Aucun étudiant en retard de paiement</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rapports et statistiques -->
                    <div class="row">
                        <div class="col-12 text-center mb-3">
                            <h4>Rapports et statistiques</h4>
                            <p class="text-muted">Visualisez les tendances financières de l'école</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Évolution mensuelle des finances</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px; background-color: #f8f9fa; border-radius: 8px; display: flex; justify-content: center; align-items: center;">
                                        <div class="text-center">
                                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                            <p>Graphique d'évolution mensuelle</p>
                                            <a href="{{ route('comptabilite.rapports') }}" class="btn btn-sm btn-primary">
                                                Voir les rapports détaillés
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Répartition des dépenses</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px; background-color: #f8f9fa; border-radius: 8px; display: flex; justify-content: center; align-items: center;">
                                        <div class="text-center">
                                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                            <p>Graphique de répartition</p>
                                            <a href="{{ route('comptabilite.rapports') }}" class="btn btn-sm btn-primary">
                                                Voir les rapports détaillés
                                            </a>
                                        </div>
                                    </div>
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

@push('scripts')
<script>
    // Scripts pour les graphiques pourront être ajoutés ici ultérieurement
    // Utilisation de Chart.js recommandée
</script>
@endpush 