@extends('layouts.app')

@section('title', 'Gestion des paiements')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-money-bill-wave text-primary me-2"></i>Gestion des paiements
                    </h5>
                    <a href="{{ route('comptabilite.paiements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouveau paiement
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
                                    <form method="GET" action="{{ route('comptabilite.paiements.index') }}" class="row g-3">
                                        <div class="col-md-3">
                                            <label for="etudiant_id" class="form-label">Étudiant</label>
                                            <select class="form-select" id="etudiant_id" name="etudiant_id">
                                                <option value="">Tous les étudiants</option>
                                                @foreach($etudiants ?? [] as $etudiant)
                                                    <option value="{{ $etudiant->id }}" {{ request('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                                        {{ $etudiant->user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="classe_id" class="form-label">Classe</label>
                                            <select class="form-select" id="classe_id" name="classe_id">
                                                <option value="">Toutes les classes</option>
                                                @foreach($classes ?? [] as $classe)
                                                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                                        {{ $classe->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="type_paiement" class="form-label">Type de paiement</label>
                                            <select class="form-select" id="type_paiement" name="type_paiement">
                                                <option value="">Tous les types</option>
                                                <option value="Frais de scolarité" {{ request('type_paiement') == 'Frais de scolarité' ? 'selected' : '' }}>Frais de scolarité</option>
                                                <option value="Frais d'inscription" {{ request('type_paiement') == "Frais d'inscription" ? 'selected' : '' }}>Frais d'inscription</option>
                                                <option value="Frais d'examen" {{ request('type_paiement') == "Frais d'examen" ? 'selected' : '' }}>Frais d'examen</option>
                                                <option value="Autre" {{ request('type_paiement') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="statut" class="form-label">Statut</label>
                                            <select class="form-select" id="statut" name="statut">
                                                <option value="">Tous les statuts</option>
                                                <option value="payé" {{ request('statut') == 'payé' ? 'selected' : '' }}>Payé</option>
                                                <option value="en attente" {{ request('statut') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                                <option value="rejeté" {{ request('statut') == 'rejeté' ? 'selected' : '' }}>Rejeté</option>
                                                <option value="retard" {{ request('statut') == 'retard' ? 'selected' : '' }}>En retard</option>
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
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-search me-2"></i>Filtrer
                                            </button>
                                            <a href="{{ route('comptabilite.paiements.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo me-2"></i>Réinitialiser
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des paiements -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Étudiant</th>
                                    <th>Classe</th>
                                    <th>Type</th>
                                    <th>Montant</th>
                                    <th>Méthode</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paiements ?? [] as $paiement)
                                    <tr>
                                        <td>{{ $paiement->id }}</td>
                                        <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        <td>{{ $paiement->etudiant->user->name ?? 'N/A' }}</td>
                                        <td>{{ $paiement->etudiant->classe->nom ?? 'N/A' }}</td>
                                        <td>{{ $paiement->type_paiement }}</td>
                                        <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $paiement->methode_paiement }}</td>
                                        <td>
                                            <span class="badge {{ $paiement->statut == 'payé' ? 'bg-success' : ($paiement->statut == 'en attente' ? 'bg-warning' : ($paiement->statut == 'rejeté' ? 'bg-danger' : 'bg-secondary')) }}">
                                                {{ ucfirst($paiement->statut) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('comptabilite.paiements.show', $paiement->id) }}" class="btn btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($paiement->statut == 'en attente')
                                                    <a href="{{ route('paiements.valider', $paiement->id) }}" class="btn btn-success">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $paiement->id }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('paiements.recu', $paiement->id) }}" class="btn btn-primary" target="_blank">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                <h5>Aucun paiement trouvé</h5>
                                                <p class="text-muted">Essayez de modifier vos filtres ou d'ajouter un nouveau paiement</p>
                                                <a href="{{ route('comptabilite.paiements.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fas fa-plus me-2"></i>Nouveau paiement
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($paiements) && $paiements->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $paiements->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals de rejet (un pour chaque paiement) -->
@if(isset($paiements))
    @foreach($paiements as $paiement)
        <div class="modal fade" id="rejectModal{{ $paiement->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $paiement->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel{{ $paiement->id }}">Rejeter le paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('paiements.rejeter', $paiement->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="raison" class="form-label">Raison du rejet</label>
                                <textarea class="form-control" id="raison" name="raison" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">Rejeter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection 