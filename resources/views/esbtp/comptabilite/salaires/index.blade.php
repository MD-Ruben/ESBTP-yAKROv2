@extends('layouts.app')

@section('title', 'Gestion des salaires')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des salaires</h5>
            <a href="{{ route('esbtp.comptabilite.salaires.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nouveau salaire
            </a>
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

            <!-- Filtres -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <i class="fas fa-filter me-1"></i> Filtrer les salaires
                </div>
                <div class="card-body">
                    <form action="{{ route('esbtp.comptabilite.salaires') }}" method="GET" class="row align-items-end">
                        <div class="col-md-3 mb-2">
                            <label for="search" class="form-label">Recherche par nom</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nom de l'employé">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="mois" class="form-label">Mois</label>
                            <select name="mois" id="mois" class="form-select">
                                <option value="">Tous les mois</option>
                                <option value="1" {{ request('mois') == '1' ? 'selected' : '' }}>Janvier</option>
                                <option value="2" {{ request('mois') == '2' ? 'selected' : '' }}>Février</option>
                                <option value="3" {{ request('mois') == '3' ? 'selected' : '' }}>Mars</option>
                                <option value="4" {{ request('mois') == '4' ? 'selected' : '' }}>Avril</option>
                                <option value="5" {{ request('mois') == '5' ? 'selected' : '' }}>Mai</option>
                                <option value="6" {{ request('mois') == '6' ? 'selected' : '' }}>Juin</option>
                                <option value="7" {{ request('mois') == '7' ? 'selected' : '' }}>Juillet</option>
                                <option value="8" {{ request('mois') == '8' ? 'selected' : '' }}>Août</option>
                                <option value="9" {{ request('mois') == '9' ? 'selected' : '' }}>Septembre</option>
                                <option value="10" {{ request('mois') == '10' ? 'selected' : '' }}>Octobre</option>
                                <option value="11" {{ request('mois') == '11' ? 'selected' : '' }}>Novembre</option>
                                <option value="12" {{ request('mois') == '12' ? 'selected' : '' }}>Décembre</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="annee" class="form-label">Année</label>
                            <select name="annee" id="annee" class="form-select">
                                <option value="">Toutes les années</option>
                                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                    <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label for="statut" class="form-label">Statut</label>
                            <select name="statut" id="statut" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="calculé" {{ request('statut') == 'calculé' ? 'selected' : '' }}>Calculé</option>
                                <option value="validé" {{ request('statut') == 'validé' ? 'selected' : '' }}>Validé</option>
                                <option value="payé" {{ request('statut') == 'payé' ? 'selected' : '' }}>Payé</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 d-flex">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i> Filtrer
                            </button>
                            <a href="{{ route('esbtp.comptabilite.salaires') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tableau des salaires -->
            <div class="table-responsive">
                <table class="table table-striped table-hover border">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Employé</th>
                            <th>Période</th>
                            <th>Salaire base</th>
                            <th>Heures supp.</th>
                            <th>Primes</th>
                            <th>Retenues</th>
                            <th>Montant net</th>
                            <th>Date paiement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaires as $salaire)
                        <tr>
                            <td>{{ $salaire->id }}</td>
                            <td>{{ $salaire->user->name ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $months = [
                                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars',
                                        4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                                        7 => 'Juillet', 8 => 'Août', 9 => 'Septembre',
                                        10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                                    ];
                                @endphp
                                {{ $months[$salaire->mois] ?? 'N/A' }} {{ $salaire->annee }}
                            </td>
                            <td>{{ number_format($salaire->salaire_base, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($salaire->heures_supplementaires, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($salaire->primes, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($salaire->retenues, 0, ',', ' ') }} FCFA</td>
                            <td class="fw-bold">{{ number_format($salaire->montant_net, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $salaire->date_paiement ? date('d/m/Y', strtotime($salaire->date_paiement)) : 'Non payé' }}</td>
                            <td>
                                @if($salaire->statut == 'payé')
                                    <span class="badge bg-success">Payé</span>
                                @elseif($salaire->statut == 'validé')
                                    <span class="badge bg-info">Validé</span>
                                @elseif($salaire->statut == 'calculé')
                                    <span class="badge bg-warning text-dark">Calculé</span>
                                @else
                                    <span class="badge bg-secondary">{{ $salaire->statut }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('esbtp.comptabilite.salaires.show', $salaire->id) }}" class="btn btn-sm btn-info me-1" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('esbtp.comptabilite.salaires.edit', $salaire->id) }}" class="btn btn-sm btn-primary me-1" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($salaire->statut == 'calculé')
                                    <form action="{{ route('esbtp.comptabilite.salaires.update-status', ['id' => $salaire->id, 'status' => 'validé']) }}" method="POST" class="d-inline me-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-info" title="Valider">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @elseif($salaire->statut == 'validé')
                                    <form action="{{ route('esbtp.comptabilite.salaires.update-status', ['id' => $salaire->id, 'status' => 'payé']) }}" method="POST" class="d-inline me-1">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success" title="Marquer comme payé">
                                            <i class="fas fa-money-bill"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $salaire->id }}" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $salaire->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $salaire->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $salaire->id }}">Confirmation de suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer ce salaire pour <strong>{{ $salaire->user->name ?? 'N/A' }}</strong> de <strong>{{ $months[$salaire->mois] ?? 'N/A' }} {{ $salaire->annee }}</strong> ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('esbtp.comptabilite.salaires.destroy', $salaire->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">Aucun salaire trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $salaires->appends(request()->query())->links() }}
            </div>

            <!-- Résumé financier -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Total des salaires</h5>
                            <h2 class="text-primary">{{ number_format($totalSalaires, 0, ',', ' ') }} FCFA</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Salaires payés</h5>
                            <h2 class="text-success">{{ number_format($totalPayes, 0, ',', ' ') }} FCFA</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Salaires en attente</h5>
                            <h2 class="text-warning">{{ number_format($totalEnAttente, 0, ',', ' ') }} FCFA</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 