@extends('layouts.app')

@section('title', 'Ajouter un étudiant - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un nouvel étudiant</h5>
                    <a href="{{ route('esbtp.etudiants.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
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

                    <form action="{{ route('esbtp.etudiants.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informations personnelles</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                                                @error('nom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="prenoms" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('prenoms') is-invalid @enderror" id="prenoms" name="prenoms" value="{{ old('prenoms') }}" required>
                                                @error('prenoms')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="genre" class="form-label">Genre <span class="text-danger">*</span></label>
                                                <select class="form-select @error('genre') is-invalid @enderror" id="genre" name="genre" required>
                                                    <option value="">Sélectionner le genre</option>
                                                    <option value="M" {{ old('genre') == 'M' ? 'selected' : '' }}>Masculin</option>
                                                    <option value="F" {{ old('genre') == 'F' ? 'selected' : '' }}>Féminin</option>
                                                </select>
                                                @error('genre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                                <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}">
                                                @error('date_naissance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="lieu_naissance" class="form-label">Lieu de naissance</label>
                                                <input type="text" class="form-control @error('lieu_naissance') is-invalid @enderror" id="lieu_naissance" name="lieu_naissance" value="{{ old('lieu_naissance') }}">
                                                @error('lieu_naissance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="ville_naissance" class="form-label">Ville de naissance</label>
                                                <input type="text" class="form-control @error('ville_naissance') is-invalid @enderror" id="ville_naissance" name="ville_naissance" value="{{ old('ville_naissance') }}">
                                                @error('ville_naissance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="commune_naissance" class="form-label">Commune de naissance</label>
                                                <input type="text" class="form-control @error('commune_naissance') is-invalid @enderror" id="commune_naissance" name="commune_naissance" value="{{ old('commune_naissance') }}">
                                                @error('commune_naissance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+225 XX XX XXX XXX" required>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="email_personnel" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email_personnel') is-invalid @enderror" id="email_personnel" name="email_personnel" value="{{ old('email_personnel') }}" required>
                                                @error('email_personnel')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="ville" class="form-label">Ville</label>
                                                <input type="text" class="form-control @error('ville') is-invalid @enderror" id="ville" name="ville" value="{{ old('ville') }}">
                                                @error('ville')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="commune" class="form-label">Commune</label>
                                                <input type="text" class="form-control @error('commune') is-invalid @enderror" id="commune" name="commune" value="{{ old('commune') }}">
                                                @error('commune')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="photo" class="form-label">Photo de profil</label>
                                                <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                                @error('photo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Format recommandé : JPEG ou PNG, max 2Mo</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informations d'inscription</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="date_admission" class="form-label">Date d'admission <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control @error('date_admission') is-invalid @enderror" id="date_admission" name="date_admission" value="{{ old('date_admission', date('Y-m-d')) }}" required>
                                                @error('date_admission')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                                    <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                                                    <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                                </select>
                                                @error('statut')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="filiere_id" class="form-label">Filière <span class="text-danger">*</span></label>
                                                <select class="form-select @error('filiere_id') is-invalid @enderror" id="filiere_id" name="filiere_id" required>
                                                    <option value="">Sélectionner une filière</option>
                                                    @foreach($filieres as $filiere)
                                                        <option value="{{ $filiere->id }}" {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                                            {{ $filiere->name }} {{ $filiere->parent ? '(Option de '.$filiere->parent->name.')' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('filiere_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="niveau_etude_id" class="form-label">Niveau d'études <span class="text-danger">*</span></label>
                                                <select class="form-select @error('niveau_etude_id') is-invalid @enderror" id="niveau_etude_id" name="niveau_etude_id" required>
                                                    <option value="">Sélectionner un niveau</option>
                                                    @foreach($niveaux as $niveau)
                                                        <option value="{{ $niveau->id }}" {{ old('niveau_etude_id') == $niveau->id ? 'selected' : '' }}>
                                                            {{ $niveau->name }} ({{ $niveau->type }} - Année {{ $niveau->year }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('niveau_etude_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="annee_universitaire_id" class="form-label">Année universitaire <span class="text-danger">*</span></label>
                                                <select class="form-select @error('annee_universitaire_id') is-invalid @enderror" id="annee_universitaire_id" name="annee_universitaire_id" required>
                                                    <option value="">Sélectionner une année</option>
                                                    @foreach($annees as $annee)
                                                        <option value="{{ $annee->id }}" {{ old('annee_universitaire_id') == $annee->id ? 'selected' : '' }}>
                                                            {{ $annee->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('annee_universitaire_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="classe_id" class="form-label">Classe</label>
                                                <select class="form-select @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id">
                                                    <option value="">Sélectionner une classe</option>
                                                    <!-- Les classes seront chargées dynamiquement via JavaScript -->
                                                </select>
                                                @error('classe_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">La classe sera automatiquement assignée en fonction de la filière et du niveau d'études</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Information(s) sur le(s) parent(s) / tuteur(s)</h6>
                                        <button type="button" class="btn btn-sm btn-primary" id="add-parent">
                                            <i class="fas fa-plus me-1"></i>Ajouter un parent
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="parents-container">
                                            <!-- Premier parent (toujours présent) -->
                                            <div class="parent-item mb-4 p-3 border rounded">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <h6>Parent / Tuteur #1</h6>
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="parent_existant_0" name="parent_existant[0]" value="1" {{ old('parent_existant.0') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="parent_existant_0">
                                                                Choisir un parent existant
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="parent-nouveau {{ old('parent_existant.0') ? 'd-none' : '' }}">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_nom_0" class="form-label">Nom <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="parent_nom_0" name="parents[0][nom]" value="{{ old('parents.0.nom') }}">
                                                            @error('parents.0.nom')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_prenoms_0" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="parent_prenoms_0" name="parents[0][prenoms]" value="{{ old('parents.0.prenoms') }}">
                                                            @error('parents.0.prenoms')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_relation_0" class="form-label">Relation <span class="text-danger">*</span></label>
                                                            <select class="form-control" id="parent_relation_0" name="parents[0][relation]">
                                                                <option value="">Sélectionner une relation</option>
                                                                <option value="Père" {{ old('parents.0.relation') == 'Père' ? 'selected' : '' }}>Père</option>
                                                                <option value="Mère" {{ old('parents.0.relation') == 'Mère' ? 'selected' : '' }}>Mère</option>
                                                                <option value="Tuteur" {{ old('parents.0.relation') == 'Tuteur' ? 'selected' : '' }}>Tuteur</option>
                                                                <option value="Autre" {{ old('parents.0.relation') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                                            </select>
                                                            @error('parents.0.relation')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_telephone_0" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="parent_telephone_0" name="parents[0][telephone]" value="{{ old('parents.0.telephone') }}" placeholder="+225 XX XX XXX XXX">
                                                            @error('parents.0.telephone')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_email_0" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="parent_email_0" name="parents[0][email]" value="{{ old('parents.0.email') }}">
                                                            @error('parents.0.email')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_profession_0" class="form-label">Profession</label>
                                                            <input type="text" class="form-control" id="parent_profession_0" name="parents[0][profession]" value="{{ old('parents.0.profession') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="parent_adresse_0" class="form-label">Adresse</label>
                                                            <textarea class="form-control" id="parent_adresse_0" name="parents[0][adresse]" rows="2">{{ old('parents.0.adresse') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="parent-existant {{ old('parent_existant.0') ? '' : 'd-none' }}">
                                                    <div class="row">
                                                        <div class="col-12 mb-3">
                                                            <label for="parent_id_0" class="form-label">Sélectionner un parent existant</label>
                                                            <select class="form-control select-parent" id="parent_id_0" name="parents[0][parent_id]" data-placeholder="Rechercher un parent...">
                                                                <option value="">Rechercher un parent...</option>
                                                                @if(old('parents.0.parent_id'))
                                                                    <option value="{{ old('parents.0.parent_id') }}" selected>Parent ID: {{ old('parents.0.parent_id') }}</option>
                                                                @endif
                                                            </select>
                                                            @error('parents.0.parent_id')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                            <small class="form-text text-muted">Commencez à taper le nom, prénom ou téléphone du parent pour le rechercher</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Compte utilisateur</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="create_account" class="form-label">Créer un compte utilisateur</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="create_account" name="create_account" value="1" {{ old('create_account') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="create_account">
                                                        Oui, créer un compte utilisateur pour cet étudiant
                                                    </label>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Un compte sera créé avec l'email fourni ci-dessus. Le mot de passe sera généré automatiquement et envoyé par email à l'étudiant.
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer l'étudiant
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
<script>
    $(document).ready(function() {
        console.log('Document ready - initializing student form');

        // Afficher les informations de débogage
        console.log('Routes API disponibles:');
        console.log('- Search Parents: {{ route("esbtp.api.search-parents") }}');
        console.log('- Get Classes: {{ route("esbtp.api.get-classes") }}');

        // Initialiser Select2 pour les parents existants
        $('.select-parent').select2({
            ajax: {
                url: '{{ route("esbtp.api.search-parents") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    console.log('Recherche de parents, terme:', params.term);
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    console.log('Résultats de recherche parents:', data);

                    // Formatage des données pour Select2
                    const items = data.items.map(function(parent) {
                        return {
                            id: parent.id,
                            text: parent.nom + ' ' + parent.prenoms + ' (' + parent.telephone + ')'
                        };
                    });

                    return {
                        results: items,
                        pagination: {
                            more: data.pagination.more
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Rechercher un parent...',
            minimumInputLength: 1,
            templateResult: formatParent,
            templateSelection: formatParentSelection
        });

        // Fonctions de formatage pour Select2
        function formatParent(parent) {
            if (parent.loading) {
                return parent.text;
            }
            return $('<span>' + parent.text + '</span>');
        }

        function formatParentSelection(parent) {
            return parent.text || parent.id;
        }

        // Initialiser le compteur de parents (1 parent déjà présent)
        let parentCount = 1;
        console.log('Nombre initial de parents:', parentCount);

        // Toggle between existing and new parent inputs - avec debug
        $(document).on('change', 'input[id^="parent_existant_"]', function() {
            const index = this.id.split('_').pop();
            console.log('Toggle parent existant pour index:', index, 'Checked:', $(this).is(':checked'));

            if ($(this).is(':checked')) {
                // Afficher le sélecteur de parent existant
                $(this).closest('.parent-item').find('.parent-existant').removeClass('d-none');
                $(this).closest('.parent-item').find('.parent-nouveau').addClass('d-none');
                console.log('Mode parent existant activé');
            } else {
                // Afficher le formulaire de nouveau parent
                $(this).closest('.parent-item').find('.parent-existant').addClass('d-none');
                $(this).closest('.parent-item').find('.parent-nouveau').removeClass('d-none');
                console.log('Mode nouveau parent activé');
            }
        });

        // Initialiser Select2 pour les autres champs
        $('.select2').select2();

        // AJAX pour charger les classes en fonction de la filière, du niveau et de l'année
        $('#filiere_id, #niveau_etude_id, #annee_universitaire_id').change(function() {
            var filiereId = $('#filiere_id').val();
            var niveauId = $('#niveau_etude_id').val();
            var anneeId = $('#annee_universitaire_id').val();

            console.log('Changement détecté - Filière:', filiereId, 'Niveau:', niveauId, 'Année:', anneeId);

            if (filiereId && niveauId && anneeId) {
                console.log('Toutes les données sont présentes, appel AJAX pour les classes');

                // Afficher un indicateur de chargement
                $('#classe_id').html('<option value="">Chargement des classes...</option>');

                $.ajax({
                    url: '{{ route("esbtp.api.get-classes") }}',
                    type: 'GET',
                    data: {
                        filiere_id: filiereId,
                        niveau_id: niveauId,
                        annee_id: anneeId
                    },
                    success: function(data) {
                        console.log('Classes reçues:', data);

                        if (data.length === 0) {
                            $('#classe_id').html('<option value="">Aucune classe disponible</option>');
                            return;
                        }

                        var options = '<option value="">Sélectionner une classe</option>';
                        $.each(data, function(index, classe) {
                            options += '<option value="' + classe.id + '">' + classe.name + ' (' + classe.code + ')</option>';
                        });
                        $('#classe_id').html(options);

                        // Si une seule classe est disponible, la sélectionner automatiquement
                        if (data.length === 1) {
                            $('#classe_id').val(data[0].id);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de la récupération des classes:', error);
                        console.error('Réponse du serveur:', xhr.responseText);
                        $('#classe_id').html('<option value="">Erreur lors du chargement des classes</option>');
                    }
                });
            } else {
                $('#classe_id').html('<option value="">Sélectionner une classe</option>');
                console.log('Données manquantes pour récupérer les classes');
            }
        });

        // Ajouter un parent (limité à 2 parents maximum)
        $('#add-parent').on('click', function(e) {
            console.log('Bouton Ajouter parent cliqué');

            if (parentCount >= 2) {
                alert('Un maximum de 2 parents est autorisé.');
                return;
            }

            const index = parentCount;
            parentCount++;

            console.log('Ajout d\'un nouveau parent avec index:', index);

            const parentHtml = `
                <div class="parent-item mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Parent / Tuteur #${parentCount}</h6>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="parent_existant_${index}" name="parent_existant[${index}]" value="1">
                                <label class="form-check-label" for="parent_existant_${index}">
                                    Choisir un parent existant
                                </label>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-parent">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="parent-nouveau">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="parent_nom_${index}" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_nom_${index}" name="parents[${index}][nom]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_prenoms_${index}" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_prenoms_${index}" name="parents[${index}][prenoms]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_relation_${index}" class="form-label">Relation <span class="text-danger">*</span></label>
                                <select class="form-control" id="parent_relation_${index}" name="parents[${index}][relation]">
                                    <option value="">Sélectionner une relation</option>
                                    <option value="Père">Père</option>
                                    <option value="Mère">Mère</option>
                                    <option value="Tuteur">Tuteur</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="parent_telephone_${index}" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_telephone_${index}" name="parents[${index}][telephone]" placeholder="+225 XX XX XXX XXX">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_email_${index}" class="form-label">Email</label>
                                <input type="email" class="form-control" id="parent_email_${index}" name="parents[${index}][email]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_profession_${index}" class="form-label">Profession</label>
                                <input type="text" class="form-control" id="parent_profession_${index}" name="parents[${index}][profession]">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="parent_adresse_${index}" class="form-label">Adresse</label>
                                <textarea class="form-control" id="parent_adresse_${index}" name="parents[${index}][adresse]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="parent-existant d-none">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="parent_id_${index}" class="form-label">Sélectionner un parent existant</label>
                                <select class="form-control select-parent" id="parent_id_${index}" name="parents[${index}][parent_id]" data-placeholder="Rechercher un parent...">
                                    <option value="">Rechercher un parent...</option>
                                </select>
                                <small class="form-text text-muted">Commencez à taper le nom, prénom ou téléphone du parent pour le rechercher</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#parents-container').append(parentHtml);

            // Réinitialiser Select2 pour le nouveau parent
            $('#parent_id_' + index).select2({
                ajax: {
                    url: '{{ route("esbtp.api.search-parents") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                        // Formatage des données pour Select2
                        const items = data.items.map(function(parent) {
                            return {
                                id: parent.id,
                                text: parent.nom + ' ' + parent.prenoms + ' (' + parent.telephone + ')'
                            };
                        });

                        return {
                            results: items,
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                placeholder: 'Rechercher un parent...',
                minimumInputLength: 1,
                templateResult: formatParent,
                templateSelection: formatParentSelection
            });
        });

        // Supprimer un parent
        $(document).on('click', '.remove-parent', function() {
            console.log('Bouton Supprimer parent cliqué');
            $(this).closest('.parent-item').remove();
            parentCount--;
        });

        // Déclencher le chargement initial des classes si toutes les valeurs sont définies
        if ($('#filiere_id').val() && $('#niveau_etude_id').val() && $('#annee_universitaire_id').val()) {
            $('#annee_universitaire_id').trigger('change');
        }
    });
</script>
@endsection
