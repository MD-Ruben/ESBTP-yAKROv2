@extends('layouts.app')

@section('title', 'Détails du salaire')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Détails du salaire - {{ $salaire->user->name }}</h5>
            <div>
                <a href="{{ route('esbtp.comptabilite.salaires') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                @can('edit', $salaire)
                <a href="{{ route('esbtp.comptabilite.salaires.edit', $salaire) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                @endcan
                <a href="{{ route('esbtp.comptabilite.salaires.pdf', $salaire) }}" class="btn btn-info" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i> Fiche de paie
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Informations générales</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Employé:</div>
                                <div class="col-md-8">{{ $salaire->user->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Fonction:</div>
                                <div class="col-md-8">{{ $salaire->user->employee_role ?? 'Non spécifiée' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Période:</div>
                                <div class="col-md-8">
                                    @php
                                        $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                                    @endphp
                                    {{ $mois[$salaire->mois] }} {{ $salaire->annee }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Statut:</div>
                                <div class="col-md-8">
                                    @if($salaire->statut == 'calculé')
                                        <span class="badge bg-secondary">Calculé</span>
                                    @elseif($salaire->statut == 'validé')
                                        <span class="badge bg-primary">Validé</span>
                                    @elseif($salaire->statut == 'payé')
                                        <span class="badge bg-success">Payé</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $salaire->statut }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Date de paiement:</div>
                                <div class="col-md-8">
                                    {{ $salaire->date_paiement ? date('d/m/Y', strtotime($salaire->date_paiement)) : 'Non payé' }}
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Date de création:</div>
                                <div class="col-md-8">{{ date('d/m/Y H:i', strtotime($salaire->created_at)) }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-4 fw-bold">Dernière mise à jour:</div>
                                <div class="col-md-8">{{ date('d/m/Y H:i', strtotime($salaire->updated_at)) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($salaire->commentaire)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Commentaire</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $salaire->commentaire }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Détails du salaire</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr class="table-light">
                                            <th colspan="2" class="text-center">Éléments de rémunération</th>
                                        </tr>
                                        <tr>
                                            <td>Salaire de base</td>
                                            <td class="text-end">{{ number_format($salaire->salaire_base, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @if($salaire->heures_supplementaires > 0)
                                        <tr>
                                            <td>Heures supplémentaires</td>
                                            <td class="text-end">{{ number_format($salaire->heures_supplementaires, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                        @if($salaire->primes > 0)
                                        <tr>
                                            <td>Primes</td>
                                            <td class="text-end">{{ number_format($salaire->primes, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                        @if($salaire->indemnites > 0)
                                        <tr>
                                            <td>Indemnités</td>
                                            <td class="text-end">{{ number_format($salaire->indemnites, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                        
                                        <!-- Calculer le total brut -->
                                        @php
                                            $totalBrut = $salaire->salaire_base + 
                                                        ($salaire->heures_supplementaires ?? 0) + 
                                                        ($salaire->primes ?? 0) + 
                                                        ($salaire->indemnites ?? 0);
                                        @endphp
                                        <tr class="table-secondary">
                                            <th>Total brut</th>
                                            <th class="text-end">{{ number_format($totalBrut, 0, ',', ' ') }} FCFA</th>
                                        </tr>

                                        <tr class="table-light">
                                            <th colspan="2" class="text-center">Retenues</th>
                                        </tr>
                                        @if($salaire->retenues > 0)
                                        <tr>
                                            <td>Retenues diverses</td>
                                            <td class="text-end">{{ number_format($salaire->retenues, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                        @if($salaire->charges_sociales > 0)
                                        <tr>
                                            <td>Charges sociales</td>
                                            <td class="text-end">{{ number_format($salaire->charges_sociales, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif
                                        @if($salaire->impots > 0)
                                        <tr>
                                            <td>Impôts</td>
                                            <td class="text-end">{{ number_format($salaire->impots, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        @endif

                                        <!-- Calculer le total des retenues -->
                                        @php
                                            $totalRetenues = ($salaire->retenues ?? 0) + 
                                                           ($salaire->charges_sociales ?? 0) + 
                                                           ($salaire->impots ?? 0);
                                            $salairenet = $totalBrut - $totalRetenues;
                                        @endphp
                                        <tr class="table-secondary">
                                            <th>Total retenues</th>
                                            <th class="text-end">{{ number_format($totalRetenues, 0, ',', ' ') }} FCFA</th>
                                        </tr>

                                        <tr class="table-primary">
                                            <th>NET À PAYER</th>
                                            <th class="text-end">{{ number_format($salairenet, 0, ',', ' ') }} FCFA</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions supplémentaires -->
            @if($salaire->statut != 'payé')
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2">
                        @if($salaire->statut == 'calculé')
                        <form action="{{ route('esbtp.comptabilite.salaires.valider', $salaire) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Êtes-vous sûr de vouloir valider ce salaire ?')">
                                <i class="fas fa-check me-1"></i> Valider le salaire
                            </button>
                        </form>
                        @endif

                        @if($salaire->statut == 'validé')
                        <form action="{{ route('esbtp.comptabilite.salaires.payer', $salaire) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir marquer ce salaire comme payé ?')">
                                <i class="fas fa-money-bill-wave me-1"></i> Marquer comme payé
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('esbtp.comptabilite.salaires.destroy', $salaire) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce salaire ? Cette action est irréversible.')">
                                <i class="fas fa-trash me-1"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .alert, .card-header, nav, footer, .modal {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush