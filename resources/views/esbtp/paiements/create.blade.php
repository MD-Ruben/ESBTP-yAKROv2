@extends('layouts.app')

@section('title', 'Nouveau Paiement')

@section('styles')
<link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Enregistrer un Nouveau Paiement</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.paiements.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('esbtp.paiements.store') }}" method="POST">
                        @csrf
                        
                        <!-- Informations de l'étudiant -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary">
                                <h3 class="card-title">Informations de l'Étudiant</h3>
                            </div>
                            <div class="card-body">
                                @if($etudiant)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Étudiant</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ $etudiant->matricule }} - {{ $etudiant->user->name }}" readonly>
                                                    <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ $etudiant->user->email }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="etudiant_id">Sélectionner un étudiant <span class="text-danger">*</span></label>
                                        <select name="etudiant_id" id="etudiant_id" class="form-control select2" required>
                                            <option value="">-- Sélectionner un étudiant --</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Informations de l'inscription -->
                        <div class="card mb-4">
                            <div class="card-header bg-info">
                                <h3 class="card-title">Informations de l'Inscription</h3>
                            </div>
                            <div class="card-body">
                                @if($inscription)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Filière</label>
                                                <input type="text" class="form-control" value="{{ $inscription->filiere->name }}" readonly>
                                                <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Niveau</label>
                                                <input type="text" class="form-control" value="{{ $inscription->niveauEtude->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Année Universitaire</label>
                                                <input type="text" class="form-control" value="{{ $inscription->anneeUniversitaire->libelle }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="inscription_id">Sélectionner une inscription <span class="text-danger">*</span></label>
                                        <select name="inscription_id" id="inscription_id" class="form-control" required>
                                            <option value="">-- Sélectionner d'abord un étudiant --</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Informations du paiement -->
                        <div class="card mb-4">
                            <div class="card-header bg-success">
                                <h3 class="card-title">Informations du Paiement</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="montant">Montant <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                                </div>
                                                <input type="number" name="montant" id="montant" class="form-control" min="0" step="1" value="{{ old('montant') }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">FCFA</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date_paiement">Date de paiement <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                </div>
                                                <input type="date" name="date_paiement" id="date_paiement" class="form-control" value="{{ old('date_paiement', date('Y-m-d')) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mode_paiement">Mode de paiement <span class="text-danger">*</span></label>
                                            <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Espèces" {{ old('mode_paiement') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                                <option value="Chèque" {{ old('mode_paiement') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                                <option value="Virement" {{ old('mode_paiement') == 'Virement' ? 'selected' : '' }}>Virement bancaire</option>
                                                <option value="Mobile Money" {{ old('mode_paiement') == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                                                <option value="Carte bancaire" {{ old('mode_paiement') == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reference_paiement">Référence du paiement</label>
                                            <input type="text" name="reference_paiement" id="reference_paiement" class="form-control" value="{{ old('reference_paiement') }}" placeholder="N° de chèque, transaction, etc.">
                                            <small class="form-text text-muted">Numéro de chèque, référence de transaction, etc.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="motif">Motif du paiement <span class="text-danger">*</span></label>
                                            <select name="motif" id="motif" class="form-control" required>
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Frais d'inscription" {{ old('motif') == "Frais d'inscription" ? 'selected' : '' }}>Frais d'inscription</option>
                                                <option value="Scolarité" {{ old('motif') == 'Scolarité' ? 'selected' : '' }}>Scolarité</option>
                                                <option value="Frais d'examen" {{ old('motif') == "Frais d'examen" ? 'selected' : '' }}>Frais d'examen</option>
                                                <option value="Frais de diplôme" {{ old('motif') == 'Frais de diplôme' ? 'selected' : '' }}>Frais de diplôme</option>
                                                <option value="Frais divers" {{ old('motif') == 'Frais divers' ? 'selected' : '' }}>Frais divers</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tranche">Tranche</label>
                                            <select name="tranche" id="tranche" class="form-control">
                                                <option value="">-- Sélectionner --</option>
                                                <option value="Première tranche" {{ old('tranche') == 'Première tranche' ? 'selected' : '' }}>Première tranche</option>
                                                <option value="Deuxième tranche" {{ old('tranche') == 'Deuxième tranche' ? 'selected' : '' }}>Deuxième tranche</option>
                                                <option value="Troisième tranche" {{ old('tranche') == 'Troisième tranche' ? 'selected' : '' }}>Troisième tranche</option>
                                                <option value="Paiement intégral" {{ old('tranche') == 'Paiement intégral' ? 'selected' : '' }}>Paiement intégral</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="commentaire">Commentaire</label>
                                    <textarea name="commentaire" id="commentaire" class="form-control" rows="3">{{ old('commentaire') }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Enregistrer le paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialiser Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Rechercher un étudiant...",
            minimumInputLength: 3,
            ajax: {
                url: "{{ route('esbtp.api.etudiants.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            }
        });
        
        // Charger les inscriptions lorsqu'un étudiant est sélectionné
        $('#etudiant_id').on('change', function() {
            var etudiantId = $(this).val();
            if (etudiantId) {
                $.ajax({
                    url: "{{ route('esbtp.api.etudiants.inscriptions') }}",
                    data: { etudiant_id: etudiantId },
                    dataType: 'json',
                    success: function(data) {
                        var options = '<option value="">-- Sélectionner une inscription --</option>';
                        $.each(data, function(index, inscription) {
                            options += '<option value="' + inscription.id + '">' + 
                                       inscription.filiere + ' - ' + inscription.niveau + 
                                       ' (' + inscription.annee + ')</option>';
                        });
                        $('#inscription_id').html(options);
                    }
                });
            } else {
                $('#inscription_id').html('<option value="">-- Sélectionner d\'abord un étudiant --</option>');
            }
        });
    });
</script>
@endsection 