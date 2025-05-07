@extends('layouts.app')

@section('title', 'Nouveau salaire')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Nouveau salaire</h5>
            <a href="{{ route('esbtp.comptabilite.salaires') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
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

            <form action="{{ route('esbtp.comptabilite.salaires.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Informations générales</h5>
                            </div>
                            <div class="card-body">
                                <!-- Employé -->
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Employé <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">Sélectionner un employé</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Période (Mois et Année) -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mois" class="form-label">Mois <span class="text-danger">*</span></label>
                                            <select class="form-select @error('mois') is-invalid @enderror" id="mois" name="mois" required>
                                                <option value="">Sélectionner un mois</option>
                                                <option value="1" {{ old('mois') == 1 ? 'selected' : '' }}>Janvier</option>
                                                <option value="2" {{ old('mois') == 2 ? 'selected' : '' }}>Février</option>
                                                <option value="3" {{ old('mois') == 3 ? 'selected' : '' }}>Mars</option>
                                                <option value="4" {{ old('mois') == 4 ? 'selected' : '' }}>Avril</option>
                                                <option value="5" {{ old('mois') == 5 ? 'selected' : '' }}>Mai</option>
                                                <option value="6" {{ old('mois') == 6 ? 'selected' : '' }}>Juin</option>
                                                <option value="7" {{ old('mois') == 7 ? 'selected' : '' }}>Juillet</option>
                                                <option value="8" {{ old('mois') == 8 ? 'selected' : '' }}>Août</option>
                                                <option value="9" {{ old('mois') == 9 ? 'selected' : '' }}>Septembre</option>
                                                <option value="10" {{ old('mois') == 10 ? 'selected' : '' }}>Octobre</option>
                                                <option value="11" {{ old('mois') == 11 ? 'selected' : '' }}>Novembre</option>
                                                <option value="12" {{ old('mois') == 12 ? 'selected' : '' }}>Décembre</option>
                                            </select>
                                            @error('mois')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="annee" class="form-label">Année <span class="text-danger">*</span></label>
                                            <select class="form-select @error('annee') is-invalid @enderror" id="annee" name="annee" required>
                                                <option value="">Sélectionner une année</option>
                                                @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                                    <option value="{{ $i }}" {{ old('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @error('annee')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Date de paiement -->
                                <div class="mb-3">
                                    <label for="date_paiement" class="form-label">Date de paiement</label>
                                    <input type="date" class="form-control @error('date_paiement') is-invalid @enderror" id="date_paiement" name="date_paiement" value="{{ old('date_paiement') }}">
                                    @error('date_paiement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Laissez vide si le salaire n'a pas encore été payé.</small>
                                </div>

                                <!-- Statut -->
                                <div class="mb-3">
                                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                        <option value="calculé" {{ old('statut') == 'calculé' ? 'selected' : '' }}>Calculé</option>
                                        <option value="validé" {{ old('statut') == 'validé' ? 'selected' : '' }}>Validé</option>
                                        <option value="payé" {{ old('statut') == 'payé' ? 'selected' : '' }}>Payé</option>
                                    </select>
                                    @error('statut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Calcul de la rémunération</h5>
                            </div>
                            <div class="card-body">
                                <!-- Salaire de base -->
                                <div class="mb-3">
                                    <label for="salaire_base" class="form-label">Salaire de base <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('salaire_base') is-invalid @enderror" id="salaire_base" name="salaire_base" value="{{ old('salaire_base', 0) }}" required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('salaire_base')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Heures supplémentaires -->
                                <div class="mb-3">
                                    <label for="heures_supplementaires" class="form-label">Heures supplémentaires</label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('heures_supplementaires') is-invalid @enderror" id="heures_supplementaires" name="heures_supplementaires" value="{{ old('heures_supplementaires', 0) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('heures_supplementaires')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Primes -->
                                <div class="mb-3">
                                    <label for="primes" class="form-label">Primes</label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('primes') is-invalid @enderror" id="primes" name="primes" value="{{ old('primes', 0) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('primes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Indemnités -->
                                <div class="mb-3">
                                    <label for="indemnites" class="form-label">Indemnités</label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('indemnites') is-invalid @enderror" id="indemnites" name="indemnites" value="{{ old('indemnites', 0) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('indemnites')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Retenues -->
                                <div class="mb-3">
                                    <label for="retenues" class="form-label">Retenues</label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('retenues') is-invalid @enderror" id="retenues" name="retenues" value="{{ old('retenues', 0) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('retenues')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Absences, avances, etc.</small>
                                </div>

                                <!-- Charges sociales -->
                                <div class="mb-3">
                                    <label for="charges_sociales" class="form-label">Charges sociales</label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('charges_sociales') is-invalid @enderror" id="charges_sociales" name="charges_sociales" value="{{ old('charges_sociales', 0) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('charges_sociales')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">CNPS, caisse de retraite, etc.</small>
                                </div>

                                <!-- Impôts -->
                                <div class="mb-3">
                                    <label for="impots" class="form-label">Impôts</label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="100" class="form-control @error('impots') is-invalid @enderror" id="impots" name="impots" value="{{ old('impots', 0) }}">
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                    @error('impots')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">IR, IS, etc.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commentaire -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Commentaire</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="3">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('esbtp.comptabilite.salaires') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Calcul automatique du montant net
        const salaire_base = document.getElementById('salaire_base');
        const heures_supplementaires = document.getElementById('heures_supplementaires');
        const primes = document.getElementById('primes');
        const indemnites = document.getElementById('indemnites');
        const retenues = document.getElementById('retenues');
        const charges_sociales = document.getElementById('charges_sociales');
        const impots = document.getElementById('impots');

        const calculerMontantNet = () => {
            const salaireBase = parseFloat(salaire_base.value) || 0;
            const heuresSupp = parseFloat(heures_supplementaires.value) || 0;
            const primesVal = parseFloat(primes.value) || 0;
            const indemnitesVal = parseFloat(indemnites.value) || 0;
            const retenuesVal = parseFloat(retenues.value) || 0;
            const chargesSociales = parseFloat(charges_sociales.value) || 0;
            const impotsVal = parseFloat(impots.value) || 0;

            const montantNet = salaireBase + heuresSupp + primesVal + indemnitesVal - retenuesVal - chargesSociales - impotsVal;
            
            // Si vous souhaitez afficher le montant net calculé en temps réel
            // document.getElementById('montant_net_preview').textContent = montantNet.toLocaleString('fr-FR') + ' FCFA';
        };

        // Ajouter des écouteurs d'événements pour tous les champs de calcul
        [salaire_base, heures_supplementaires, primes, indemnites, retenues, charges_sociales, impots].forEach(element => {
            element.addEventListener('input', calculerMontantNet);
        });

        // Calcul initial
        calculerMontantNet();
    });
</script>
@endpush
@endsection 