@extends('layouts.app')

@section('title', 'Rapports financiers')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Rapports financiers
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Imprimer
                        </button>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-file-pdf me-1"></i>Exporter PDF
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Sélection de période -->
                    <form method="GET" action="{{ route('esbtp.comptabilite.rapports') }}" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="annee" class="form-label">Année</label>
                            <select class="form-select" id="annee" name="annee" onchange="this.form.submit()">
                                @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}" {{ ($annee ?? date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>

                    <!-- Résumé annuel -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Résumé financier pour l'année {{ $annee ?? date('Y') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total des recettes</h5>
                                                    <h2 class="display-6">{{ number_format(array_sum($recettes ?? [0]), 0, ',', ' ') }} FCFA</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Total des dépenses</h5>
                                                    <h2 class="display-6">{{ number_format(array_sum($depenses ?? [0]), 0, ',', ' ') }} FCFA</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card {{ (array_sum($recettes ?? [0]) - array_sum($depenses ?? [0])) >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">Balance</h5>
                                                    <h2 class="display-6">{{ number_format(array_sum($recettes ?? [0]) - array_sum($depenses ?? [0]), 0, ',', ' ') }} FCFA</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique recettes/dépenses par mois -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Évolution mensuelle des recettes et dépenses</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 400px; background-color: #f8f9fa; border-radius: 8px; display: flex; justify-content: center; align-items: center;" id="monthly-chart">
                                        <div class="text-center">
                                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                            <p>Le graphique d'évolution mensuelle sera affiché ici</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Tableau des données -->
                                    <div class="table-responsive mt-4">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mois</th>
                                                    @foreach($mois ?? [] as $m)
                                                        <th>{{ $m }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th>Recettes</th>
                                                    @foreach($recettes ?? [] as $recette)
                                                        <td>{{ number_format($recette, 0, ',', ' ') }} FCFA</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <th>Dépenses</th>
                                                    @foreach($depenses ?? [] as $depense)
                                                        <td>{{ number_format($depense, 0, ',', ' ') }} FCFA</td>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <th>Balance</th>
                                                    @if(isset($recettes) && isset($depenses))
                                                        @foreach(array_map(function($a, $b) { return $a - $b; }, $recettes, $depenses) as $balance)
                                                            <td class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ number_format($balance, 0, ',', ' ') }} FCFA
                                                            </td>
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Répartition des paiements et dépenses -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Répartition des paiements par type</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px; background-color: #f8f9fa; border-radius: 8px; display: flex; justify-content: center; align-items: center;" id="payments-chart">
                                        <div class="text-center">
                                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                            <p>Le graphique de répartition sera affiché ici</p>
                                        </div>
                                    </div>

                                    <!-- Tableau des données -->
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Montant</th>
                                                    <th>Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($paiementsParType))
                                                    @php
                                                        $totalPaiements = $paiementsParType->sum('total');
                                                    @endphp
                                                    @foreach($paiementsParType as $paiement)
                                                        <tr>
                                                            <td>{{ $paiement->type_paiement }}</td>
                                                            <td>{{ number_format($paiement->total, 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                {{ number_format($totalPaiements > 0 ? ($paiement->total / $totalPaiements * 100) : 0, 1) }}%
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center">Aucune donnée disponible</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Répartition des dépenses par catégorie</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px; background-color: #f8f9fa; border-radius: 8px; display: flex; justify-content: center; align-items: center;" id="expenses-chart">
                                        <div class="text-center">
                                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                            <p>Le graphique de répartition sera affiché ici</p>
                                        </div>
                                    </div>

                                    <!-- Tableau des données -->
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Catégorie</th>
                                                    <th>Montant</th>
                                                    <th>Pourcentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($depensesParCategorie))
                                                    @php
                                                        $totalDepenses = $depensesParCategorie->sum('total');
                                                    @endphp
                                                    @foreach($depensesParCategorie as $depense)
                                                        <tr>
                                                            <td>{{ $depense->categorie }}</td>
                                                            <td>{{ number_format($depense->total, 0, ',', ' ') }} FCFA</td>
                                                            <td>
                                                                {{ number_format($totalDepenses > 0 ? ($depense->total / $totalDepenses * 100) : 0, 1) }}%
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="3" class="text-center">Aucune donnée disponible</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paiements par classe -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Paiements par classe</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($paiementsParClasse) && $paiementsParClasse->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Classe</th>
                                                        <th>Montant total payé</th>
                                                        <th>Pourcentage du total</th>
                                                        <th>Représentation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalClasses = $paiementsParClasse->sum();
                                                    @endphp
                                                    @foreach($paiementsParClasse as $classe => $montant)
                                                        <tr>
                                                            <td>{{ $classe }}</td>
                                                            <td>{{ number_format($montant, 0, ',', ' ') }} FCFA</td>
                                                            <td>{{ number_format($totalClasses > 0 ? ($montant / $totalClasses * 100) : 0, 1) }}%</td>
                                                            <td>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                                         style="width: {{ $totalClasses > 0 ? ($montant / $totalClasses * 100) : 0 }}%" 
                                                                         aria-valuenow="{{ $totalClasses > 0 ? ($montant / $totalClasses * 100) : 0 }}" 
                                                                         aria-valuemin="0" 
                                                                         aria-valuemax="100"></div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-light">
                                                        <th>Total</th>
                                                        <th>{{ number_format($totalClasses, 0, ',', ' ') }} FCFA</th>
                                                        <th>100%</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Aucune donnée disponible pour les paiements par classe.
                                        </div>
                                    @endif
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