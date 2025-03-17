@extends('layouts.app')

@section('title', 'Détails du Paiement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Détails du Paiement #{{ $paiement->numero_recu }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.paiements.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>

                        @if($paiement->status == 'validé')
                        <a href="{{ route('esbtp.paiements.recu', $paiement->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-file-pdf"></i> Télécharger le reçu
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h3 class="card-title">Informations du Paiement</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Numéro de reçu</th>
                                            <td>{{ $paiement->numero_recu }}</td>
                                        </tr>
                                        <tr>
                                            <th>Montant</th>
                                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                        <tr>
                                            <th>Date de paiement</th>
                                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mode de paiement</th>
                                            <td>{{ $paiement->mode_paiement }}</td>
                                        </tr>
                                        <tr>
                                            <th>Référence</th>
                                            <td>{{ $paiement->reference_paiement ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Motif</th>
                                            <td>{{ $paiement->motif }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tranche</th>
                                            <td>{{ $paiement->tranche ?: 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Statut</th>
                                            <td>
                                                <span class="badge badge-{{ $paiement->status_class }}">
                                                    {{ $paiement->status_formatte }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($paiement->status == 'validé' && $paiement->date_validation)
                                        <tr>
                                            <th>Date de validation</th>
                                            <td>{{ $paiement->date_validation->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Validé par</th>
                                            <td>{{ $paiement->validatedBy ? $paiement->validatedBy->name : 'N/A' }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h3 class="card-title">Informations de l'Étudiant</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Matricule</th>
                                            <td>{{ $paiement->etudiant->matricule }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nom complet</th>
                                            <td>{{ $paiement->etudiant->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $paiement->etudiant->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Filière</th>
                                            <td>{{ $paiement->inscription->filiere->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Niveau</th>
                                            <td>{{ $paiement->inscription->niveauEtude->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Année universitaire</th>
                                            <td>{{ $paiement->inscription->anneeUniversitaire->libelle }}</td>
                                        </tr>
                                    </table>

                                    <div class="mt-3">
                                        <a href="{{ route('esbtp.etudiants.show', $paiement->etudiant_id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-user"></i> Voir le profil étudiant
                                        </a>
                                        <a href="{{ route('esbtp.paiements.etudiant', $paiement->etudiant_id) }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-money-bill"></i> Tous les paiements
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commentaires -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Commentaires</h3>
                                </div>
                                <div class="card-body">
                                    @if($paiement->commentaire)
                                        <div class="callout callout-info">
                                            <p>{{ $paiement->commentaire }}</p>
                                        </div>
                                    @else
                                        <p class="text-muted">Aucun commentaire</p>
                                    @endif

                                    @if($paiement->status == 'en_attente')
                                        @can('validate-paiements')
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalValider">
                                                <i class="fas fa-check"></i> Valider ce paiement
                                            </button>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalRejeter">
                                                <i class="fas fa-times"></i> Rejeter ce paiement
                                            </button>
                                        </div>
                                        @endcan
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <small class="text-muted">
                                        Créé le {{ $paiement->created_at->format('d/m/Y H:i') }}
                                        @if($paiement->createdBy)
                                            par {{ $paiement->createdBy->name }}
                                        @endif

                                        @if($paiement->updated_at->gt($paiement->created_at))
                                            <br>Dernière modification le {{ $paiement->updated_at->format('d/m/Y H:i') }}
                                            @if($paiement->updatedBy)
                                                par {{ $paiement->updatedBy->name }}
                                            @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Valider -->
@can('validate-paiements')
<div class="modal fade" id="modalValider" tabindex="-1" role="dialog" aria-labelledby="modalValiderLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="modalValiderLabel">Valider le paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir valider ce paiement ?</p>
                <p><strong>Montant :</strong> {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
                <p><strong>Étudiant :</strong> {{ $paiement->etudiant->user->name }}</p>
                <p class="text-warning">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="{{ route('esbtp.paiements.valider', $paiement->id) }}" class="btn btn-success">
                    <i class="fas fa-check"></i> Confirmer la validation
                </a>
            </div>
        </div>
    </div>
</div>
@endcan

<!-- Modal Rejeter -->
@can('validate-paiements')
<div class="modal fade" id="modalRejeter" tabindex="-1" role="dialog" aria-labelledby="modalRejeterLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="modalRejeterLabel">Rejeter le paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('esbtp.paiements.rejeter', $paiement->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="commentaire">Motif du rejet <span class="text-danger">*</span></label>
                        <textarea name="commentaire" id="commentaire" rows="3" class="form-control" required></textarea>
                        <small class="form-text text-muted">Veuillez indiquer la raison du rejet de ce paiement.</small>
                    </div>
                    <p class="text-warning">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection
