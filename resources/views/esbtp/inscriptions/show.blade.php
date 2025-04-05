@extends('layouts.app')

@section('title', 'Détails de l\'inscription')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'inscription</h5>
                    <div>
                        @if($inscription->status === 'en_attente')
                            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#validationModal">
                                <i class="fas fa-check me-1"></i>Valider l'inscription
                            </button>
                            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#paiementModal">
                                <i class="fas fa-money-bill me-1"></i>Enregistrer un paiement
                            </button>
                        @endif
                        <a href="{{ route('esbtp.inscriptions.edit', $inscription) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <a href="{{ route('esbtp.inscriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('account_info'))
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-user-check me-2"></i>Informations de connexion générées</h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Nom d'utilisateur:</strong> {{ session('account_info')['username'] }}</p>
                                    <p class="mb-1"><strong>Rôle:</strong> {{ session('account_info')['role'] }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Mot de passe temporaire:</strong> <span class="badge bg-light text-dark p-2 font-monospace">{{ session('account_info')['password'] }}</span></p>
                                    <p class="mb-0 text-muted"><small>Veuillez communiquer ces informations à l'étudiant. Le mot de passe devra être changé à la première connexion.</small></p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Informations de l'étudiant -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2">Informations de l'étudiant</h6>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Nom:</strong> {{ $inscription->etudiant->nom }}</p>
                            <p><strong>Prénoms:</strong> {{ $inscription->etudiant->prenoms }}</p>
                            <p><strong>Matricule:</strong> {{ $inscription->etudiant->matricule }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Date de naissance:</strong> {{ $inscription->etudiant->date_naissance }}</p>
                            <p><strong>Lieu de naissance:</strong> {{ $inscription->etudiant->lieu_naissance ?? 'Non renseigné' }}</p>
                            <p><strong>Genre:</strong> {{ $inscription->etudiant->sexe === 'M' ? 'Homme' : 'Femme' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Téléphone:</strong> {{ $inscription->etudiant->telephone }}</p>
                            <p><strong>Email:</strong> {{ $inscription->etudiant->email_personnel }}</p>
                            <p><strong>Adresse:</strong> {{ $inscription->etudiant->adresse }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Ville de résidence:</strong> {{ $inscription->etudiant->ville ?? 'Non renseigné' }}</p>
                            <p><strong>Commune de résidence:</strong> {{ $inscription->etudiant->commune ?? 'Non renseigné' }}</p>
                        </div>
                    </div>

                    <!-- Informations de l'inscription -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2">Informations de l'inscription</h6>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Filière:</strong> {{ $inscription->filiere->name }}</p>
                            <p><strong>Niveau:</strong> {{ $inscription->niveau->name }}</p>
                            <p><strong>Classe:</strong> {{ $inscription->classe->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Année universitaire:</strong> {{ $inscription->anneeUniversitaire->name }}</p>
                            <p><strong>Date d'inscription:</strong> {{ $inscription->date_inscription }}</p>
                            <p><strong>Statut:</strong>
                                <span class="badge bg-{{ $inscription->status === 'active' ? 'success' : ($inscription->status === 'en_attente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($inscription->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Frais d'inscription:</strong> {{ number_format($inscription->frais_inscription, 0, ',', ' ') }} FCFA</p>
                            <p><strong>Montant scolarité:</strong> {{ number_format($inscription->montant_scolarite, 0, ',', ' ') }} FCFA</p>
                            <p><strong>Type d'inscription:</strong> {{ ucfirst(str_replace('_', ' ', $inscription->type_inscription)) }}</p>
                        </div>
                    </div>

                    <!-- Parents -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2">Parents/Tuteurs</h6>
                        </div>
                        @forelse($inscription->etudiant->parents as $parent)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $parent->nom }} {{ $parent->prenoms }}</h6>
                                        <p class="mb-1"><strong>Téléphone:</strong> {{ $parent->telephone }}</p>
                                        <p class="mb-1"><strong>Email:</strong> {{ $parent->email }}</p>
                                        <p class="mb-1"><strong>Profession:</strong> {{ $parent->profession }}</p>
                                        <p class="mb-0"><strong>Relation:</strong> {{ ucfirst($parent->pivot->relation) }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted">Aucun parent enregistré</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paiements -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2">Historique des paiements</h6>
                        </div>
                        <div class="col-12">
                            @if($inscription->paiements->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Montant</th>
                                                <th>Méthode</th>
                                                <th>Référence</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscription->paiements as $paiement)
                                                <tr>
                                                    <td>{{ $paiement->date }}</td>
                                                    <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ $paiement->methode }}</td>
                                                    <td>{{ $paiement->reference }}</td>
                                                    <td>{{ $paiement->commentaire }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th>{{ number_format($inscription->paiements->sum('montant'), 0, ',', ' ') }} FCFA</th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Aucun paiement enregistré</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Validation -->
<div class="modal fade" id="validationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Valider l'inscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('esbtp.inscriptions.valider', $inscription) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir valider cette inscription ?</p>
                    <p>L'étudiant sera automatiquement activé et pourra accéder à son compte.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Valider l'inscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Paiement -->
<div class="modal fade" id="paiementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enregistrer un paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('esbtp.paiements.store') }}" method="POST" id="paiementForm">
                @csrf
                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                <input type="hidden" name="etudiant_id" value="{{ $inscription->etudiant->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="montant">Montant</label>
                        <input type="number" class="form-control" id="montant" name="montant" required>
                    </div>
                    <div class="form-group">
                        <label for="date_paiement">Date de paiement</label>
                        <input type="date" class="form-control" id="date_paiement" name="date_paiement" required>
                    </div>
                    <div class="form-group">
                        <label for="mode_paiement">Mode de paiement</label>
                        <select class="form-control" id="mode_paiement" name="mode_paiement" required>
                            <option value="especes">Espèces</option>
                            <option value="cheque">Chèque</option>
                            <option value="virement">Virement</option>
                            <option value="carte">Carte bancaire</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reference_paiement">Référence du paiement</label>
                        <input type="text" class="form-control" id="reference_paiement" name="reference_paiement">
                    </div>
                    <div class="form-group">
                        <label for="motif">Motif</label>
                        <select class="form-control" id="motif" name="motif" required>
                            <option value="inscription">Frais d'inscription</option>
                            <option value="scolarite">Frais de scolarité</option>
                            <option value="examen">Frais d'examen</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="commentaire">Commentaire</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
