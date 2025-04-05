@extends('layouts.app')

@section('title', 'Modifier l\'inscription')

@push('styles')
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-selection {
        height: 38px !important;
        border: 1px solid #ced4da !important;
    }
    .select2-selection__rendered {
        line-height: 36px !important;
    }
    .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier l'inscription</h1>
        <div>
            <a href="{{ route('esbtp.inscriptions.show', $inscription->id) }}" class="btn btn-info btn-sm me-2">
                <i class="fas fa-eye fa-sm text-white-50 me-1"></i> Voir les détails
            </a>
            <a href="{{ route('esbtp.inscriptions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire de modification d'inscription -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulaire de modification</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('esbtp.inscriptions.update', $inscription->id) }}">
                @csrf
                @method('PUT')

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Informations de l'étudiant (lecture seule) -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="font-weight-bold">Informations de l'étudiant</h5>
                        <hr>

                        <div class="alert alert-info">
                            <p class="mb-0"><strong>Étudiant :</strong> {{ $inscription->etudiant->nom }} {{ $inscription->etudiant->prenoms }}</p>
                            <p class="mb-0"><strong>Matricule :</strong> {{ $inscription->etudiant->matricule }}</p>
                            <p class="mb-0"><strong>Date de naissance :</strong> {{ $inscription->etudiant->date_naissance->format('d/m/Y') }}</p>
                            <p class="mb-0"><strong>Pour modifier les informations de l'étudiant, veuillez utiliser la page de profil de l'étudiant.</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Informations de l'inscription -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="font-weight-bold">Informations de l'inscription</h5>
                        <hr>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_inscription">Date d'inscription <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_inscription') is-invalid @enderror" id="date_inscription" name="date_inscription" value="{{ old('date_inscription', $inscription->date_inscription->format('Y-m-d')) }}" required>
                            @error('date_inscription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="type_inscription">Type d'inscription <span class="text-danger">*</span></label>
                            <select class="form-control @error('type_inscription') is-invalid @enderror" id="type_inscription" name="type_inscription" required>
                                <option value="première_inscription" {{ old('type_inscription', $inscription->type_inscription) == 'première_inscription' ? 'selected' : '' }}>Première inscription</option>
                                <option value="réinscription" {{ old('type_inscription', $inscription->type_inscription) == 'réinscription' ? 'selected' : '' }}>Réinscription</option>
                                <option value="transfert" {{ old('type_inscription', $inscription->type_inscription) == 'transfert' ? 'selected' : '' }}>Transfert</option>
                            </select>
                            @error('type_inscription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Statut <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="en_attente" {{ old('status', $inscription->status) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="active" {{ old('status', $inscription->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="annulée" {{ old('status', $inscription->status) == 'annulée' ? 'selected' : '' }}>Annulée</option>
                                <option value="terminée" {{ old('status', $inscription->status) == 'terminée' ? 'selected' : '' }}>Terminée</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filiere_id">Filière <span class="text-danger">*</span></label>
                            <select class="form-control @error('filiere_id') is-invalid @enderror" id="filiere_id" name="filiere_id" required>
                                <option value="">Sélectionner une filière</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id', $inscription->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="niveau_id">Niveau d'études <span class="text-danger">*</span></label>
                            <select class="form-control @error('niveau_id') is-invalid @enderror" id="niveau_id" name="niveau_id" required>
                                <option value="">Sélectionner un niveau</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ old('niveau_id', $inscription->niveau_id) == $niveau->id ? 'selected' : '' }}>
                                        {{ $niveau->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('niveau_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="classe_id">Classe <span class="text-danger">*</span></label>
                            <select class="form-control @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id" required>
                                <option value="">Sélectionner une classe</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ old('classe_id', $inscription->classe_id) == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="frais_inscription">Frais d'inscription <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('frais_inscription') is-invalid @enderror" id="frais_inscription" name="frais_inscription" value="{{ old('frais_inscription', $inscription->frais_inscription) }}" min="0" required>
                            @error('frais_inscription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="montant_scolarite">Montant scolarité <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('montant_scolarite') is-invalid @enderror" id="montant_scolarite" name="montant_scolarite" value="{{ old('montant_scolarite', $inscription->montant_scolarite) }}" min="0" required>
                            @error('montant_scolarite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observations">Observations</label>
                            <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations" rows="3">{{ old('observations', $inscription->observations) }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif des paiements -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="font-weight-bold">Récapitulatif des paiements</h5>
                        <hr>

                        @if($inscription->paiements->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Montant</th>
                                            <th>Méthode</th>
                                            <th>Référence</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inscription->paiements as $paiement)
                                            <tr>
                                                <td>{{ $paiement->date->format('d/m/Y') }}</td>
                                                <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                <td>{{ $paiement->methode }}</td>
                                                <td>{{ $paiement->reference }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="1"><strong>Total payé :</strong></td>
                                            <td colspan="3"><strong>{{ number_format($inscription->paiements->sum('montant'), 0, ',', ' ') }} FCFA</strong></td>
                                        </tr>
                                        <tr>
                                            <td colspan="1"><strong>Reste à payer :</strong></td>
                                            <td colspan="3"><strong>{{ number_format($inscription->montant_scolarite - $inscription->paiements->sum('montant'), 0, ',', ' ') }} FCFA</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <p class="text-muted">Pour ajouter un nouveau paiement, veuillez utiliser la page de détails de l'inscription.</p>
                        @else
                            <div class="alert alert-warning">
                                Aucun paiement enregistré pour cette inscription.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="history.back()">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Charger les classes en fonction de la filière et du niveau
        function loadClasses() {
            var filiereId = $('#filiere_id').val();
            var niveauId = $('#niveau_id').val();

            if (filiereId && niveauId) {
                $.ajax({
                    url: "{{ route('esbtp.inscriptions.getClasses') }}",
                    type: "GET",
                    data: {
                        filiere_id: filiereId,
                        niveau_id: niveauId,
                        annee_id: "{{ $inscription->annee_universitaire_id }}"
                    },
                    success: function(data) {
                        $('#classe_id').empty();
                        $('#classe_id').append('<option value="">Sélectionner une classe</option>');

                        $.each(data, function(key, value) {
                            $('#classe_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });

                        // Réselectionner la classe actuelle si elle existe dans la liste
                        var currentClasseId = "{{ old('classe_id', $inscription->classe_id) }}";
                        if (currentClasseId) {
                            $('#classe_id').val(currentClasseId);
                        }
                    }
                });
            } else {
                $('#classe_id').empty();
                $('#classe_id').append('<option value="">Sélectionner une classe</option>');
            }
        }

        // Événements de changement de filière et niveau
        $('#filiere_id, #niveau_id').change(function() {
            loadClasses();
        });

        // Avertissement si le statut est modifié à "terminée"
        $('#status').change(function() {
            if ($(this).val() === 'terminée') {
                alert("Attention : Changer le statut à 'terminée' modifiera également le statut de l'étudiant à 'diplômé' s'il n'a pas d'autres inscriptions actives.");
            } else if ($(this).val() === 'annulée') {
                alert("Attention : Changer le statut à 'annulée' peut modifier le statut de l'étudiant à 'inactif' s'il n'a pas d'autres inscriptions actives.");
            }
        });
    });
</script>
@endpush
