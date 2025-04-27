@extends('layouts.app')

@section('title', 'Gestion des dépenses')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Gestion des dépenses
                    </h5>
                    <a href="{{ route('esbtp.comptabilite.depenses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle dépense
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="accordion mb-4" id="accordionFilters">
                        <div class="accordion-item border-0 shadow-sm">
                            <h2 class="accordion-header" id="headingFilters">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
                                    <i class="fas fa-filter me-2"></i>Filtres
                                </button>
                            </h2>
                            <div id="collapseFilters" class="accordion-collapse collapse" aria-labelledby="headingFilters" data-bs-parent="#accordionFilters">
                                <div class="accordion-body">
                                    <form method="GET" action="{{ route('esbtp.comptabilite.depenses') }}" class="row g-3">
                                        <div class="col-md-3">
                                            <label for="categorie" class="form-label">Catégorie</label>
                                            <select class="form-select" id="categorie" name="categorie">
                                                <option value="">Toutes les catégories</option>
                                                @foreach($categories ?? [] as $categorie)
                                                    <option value="{{ $categorie }}" {{ request('categorie') == $categorie ? 'selected' : '' }}>
                                                        {{ $categorie }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="date_debut" class="form-label">Date début</label>
                                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="date_fin" class="form-label">Date fin</label>
                                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-search me-2"></i>Filtrer
                                            </button>
                                            <a href="{{ route('esbtp.comptabilite.depenses') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo me-2"></i>Réinitialiser
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des dépenses -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                    <th>Bénéficiaire</th>
                                    <th>Référence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depenses ?? [] as $depense)
                                    <tr>
                                        <td>{{ $depense->id }}</td>
                                        <td>{{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : 'N/A' }}</td>
                                        <td>{{ $depense->description ?? $depense->libelle }}</td>
                                        <td>{{ $depense->categorie ? $depense->categorie->nom : 'N/A' }}</td>
                                        <td>{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $depense->beneficiaire ?? 'N/A' }}</td>
                                        <td>{{ $depense->reference ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('esbtp.comptabilite.depenses.show', $depense->id) }}" class="btn btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-primary" onclick="window.print()">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                <h5>Aucune dépense trouvée</h5>
                                                <p class="text-muted">Essayez de modifier vos filtres ou d'ajouter une nouvelle dépense</p>
                                                <a href="{{ route('esbtp.comptabilite.depenses.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus me-2"></i>Nouvelle dépense
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($depenses) && $depenses->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $depenses->appends(request()->query())->links() }}
                        </div>
                    @endif

                    <!-- Statistiques -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistiques des dépenses</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Total des dépenses</h6>
                                            <h3 class="text-danger">
                                                {{ number_format($depenses->sum('montant') ?? 0, 0, ',', ' ') }} FCFA
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Moyenne par dépense</h6>
                                            <h3>
                                                {{ number_format($depenses->avg('montant') ?? 0, 0, ',', ' ') }} FCFA
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="card-title">Nombre de dépenses</h6>
                                            <h3>
                                                {{ $depenses->count() ?? 0 }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 text-center">
                                <a href="{{ route('esbtp.comptabilite.rapports') }}" class="btn btn-primary">
                                    <i class="fas fa-chart-pie me-2"></i>Voir les rapports détaillés
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 