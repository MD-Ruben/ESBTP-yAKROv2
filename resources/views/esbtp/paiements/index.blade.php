@extends('layouts.app')

@section('title', 'Gestion des Paiements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des Paiements</h3>
                    <div class="card-tools">
                        @can('create-paiements')
                        <a href="{{ route('esbtp.paiements.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nouveau Paiement
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total des paiements</span>
                                    <span class="info-box-number">{{ number_format($stats['montant_total'], 0, ',', ' ') }} FCFA</span>
                                    <span class="info-box-text">{{ $stats['total'] }} paiements</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Paiements validés</span>
                                    <span class="info-box-number">{{ number_format($stats['montant_valide'], 0, ',', ' ') }} FCFA</span>
                                    <span class="info-box-text">{{ $stats['valides'] }} paiements</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">En attente</span>
                                    <span class="info-box-number">{{ number_format($stats['montant_en_attente'], 0, ',', ' ') }} FCFA</span>
                                    <span class="info-box-text">{{ $stats['en_attente'] }} paiements</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <form action="{{ route('esbtp.paiements.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Recherche</label>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Matricule, nom, n° reçu..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Tous</option>
                                        <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        <option value="validé" {{ request('status') == 'validé' ? 'selected' : '' }}>Validé</option>
                                        <option value="rejeté" {{ request('status') == 'rejeté' ? 'selected' : '' }}>Rejeté</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_debut">Date début</label>
                                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_fin">Date fin</label>
                                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="annee_id">Année universitaire</label>
                                    <select name="annee_id" id="annee_id" class="form-control">
                                        <option value="">Toutes</option>
                                        @foreach($annees as $annee)
                                            <option value="{{ $annee->id }}" {{ request('annee_id') == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tableau des paiements -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>N° Reçu</th>
                                    <th>Étudiant</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Motif</th>
                                    <th>Mode</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paiements as $paiement)
                                    <tr>
                                        <td>{{ $paiement->numero_recu }}</td>
                                        <td>
                                            <a href="{{ route('esbtp.etudiants.show', $paiement->etudiant_id) }}">
                                                {{ $paiement->etudiant->matricule }} - 
                                                {{ $paiement->etudiant->user->name }}
                                            </a>
                                        </td>
                                        <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        <td class="text-right">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $paiement->motif }}</td>
                                        <td>{{ $paiement->mode_paiement }}</td>
                                        <td>
                                            <span class="badge badge-{{ $paiement->status_class }}">
                                                {{ $paiement->status_formatte }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('esbtp.paiements.show', $paiement->id) }}" class="btn btn-sm btn-info" title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($paiement->status != 'validé')
                                                    @can('edit-paiements')
                                                    <a href="{{ route('esbtp.paiements.edit', $paiement->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @endcan
                                                    
                                                    @can('validate-paiements')
                                                    <a href="{{ route('esbtp.paiements.valider', $paiement->id) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Valider"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir valider ce paiement ?')">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    @endcan
                                                @endif
                                                
                                                @if($paiement->status == 'validé')
                                                    <a href="{{ route('esbtp.paiements.recu', $paiement->id) }}" class="btn btn-sm btn-primary" title="Télécharger le reçu">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
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
                    <div class="mt-3">
                        {{ $paiements->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 