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
                                                <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+225 XX XX XXX XXX" required>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                                @error('email')
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
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="parent_existant_0" name="parent_existant[0]" value="1">
                                                        <label class="form-check-label" for="parent_existant_0">
                                                            Choisir un parent existant
                                                        </label>
                                                    </div>
                                                </div>
                                                
                                                <div class="parent-nouveau">
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
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_email_0" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="parent_email_0" name="parents[0][email]" value="{{ old('parents.0.email') }}">
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
                                                
                                                <div class="parent-existant d-none">
                                                    <div class="row">
                                                        <div class="col-12 mb-3">
                                                            <label for="parent_id_0" class="form-label">Sélectionner un parent existant</label>
                                                            <select class="form-control select-parent" id="parent_id_0" name="parents[0][parent_id]">
                                                                <option value="">Rechercher un parent...</option>
                                                            </select>
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
        console.log('Document ready - Initializing form...');
        
        // Initialisation de Select2 pour les sélecteurs si disponible
        if (typeof $.fn.select2 !== 'undefined') {
            console.log('Select2 is available - Initializing selects...');
            $('#filiere_id, #niveau_etude_id, #annee_universitaire_id, #classe_id, #genre, #statut').select2({
                theme: 'bootstrap4',
                placeholder: 'Sélectionner une option',
                allowClear: true
            });
            
            // Select2 pour les parents existants
            $('.select-parent').select2({
                theme: 'bootstrap4',
                placeholder: 'Rechercher un parent...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route("esbtp.api.search-parents") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.nom + ' ' + item.prenoms + ' (' + item.telephone + ')'
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        } else {
            console.warn('Warning: Select2 library is not available!');
        }
        
        // Toggle parent existant/nouveau
        $(document).on('change', '[id^="parent_existant_"]', function() {
            console.log('Parent toggle changed');
            const parentItem = $(this).closest('.parent-item');
            const isExistant = $(this).is(':checked');
            
            if (isExistant) {
                parentItem.find('.parent-nouveau').addClass('d-none');
                parentItem.find('.parent-existant').removeClass('d-none');
            } else {
                parentItem.find('.parent-nouveau').removeClass('d-none');
                parentItem.find('.parent-existant').addClass('d-none');
            }
        });
        
        // Gestion des classes en fonction de la filière et du niveau
        $('#filiere_id, #niveau_etude_id, #annee_universitaire_id').on('change', function() {
            const filiereId = $('#filiere_id').val();
            const niveauId = $('#niveau_etude_id').val();
            const anneeId = $('#annee_universitaire_id').val();
            
            if (filiereId && niveauId && anneeId) {
                $.ajax({
                    url: '{{ route("esbtp.api.get-classes") }}',
                    data: {
                        filiere_id: filiereId,
                        niveau_id: niveauId,
                        annee_id: anneeId
                    },
                    success: function(data) {
                        let options = '<option value="">Sélectionner une classe</option>';
                        
                        data.forEach(function(classe) {
                            options += `<option value="${classe.id}">${classe.name} (${classe.places_disponibles} places disponibles)</option>`;
                        });
                        
                        $('#classe_id').html(options);
                    }
                });
            } else {
                $('#classe_id').html('<option value="">Sélectionner une classe</option>');
            }
        });
        
        // Ajout de parents
        let parentCount = 1; // Commence à 1 car nous avons déjà un parent par défaut
        
        // Gestionnaire d'événement pour le bouton "Ajouter un parent"
        $('#add-parent').on('click', function(e) {
            e.preventDefault();
            console.log('Add parent button clicked');
            
            if (parentCount >= 2) {
                alert('Vous ne pouvez ajouter que 2 parents maximum.');
                return;
            }
            
            const parentHtml = `
                <div class="parent-item mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Parent / Tuteur #${parentCount + 1}</h6>
                        <div class="d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="parent_existant_${parentCount}" name="parent_existant[${parentCount}]" value="1">
                                <label class="form-check-label" for="parent_existant_${parentCount}">
                                    Choisir un parent existant
                                </label>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-parent">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="parent-nouveau">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="parent_nom_${parentCount}" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_nom_${parentCount}" name="parents[${parentCount}][nom]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_prenoms_${parentCount}" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_prenoms_${parentCount}" name="parents[${parentCount}][prenoms]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_relation_${parentCount}" class="form-label">Relation <span class="text-danger">*</span></label>
                                <select class="form-control" id="parent_relation_${parentCount}" name="parents[${parentCount}][relation]">
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
                                <label for="parent_telephone_${parentCount}" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="parent_telephone_${parentCount}" name="parents[${parentCount}][telephone]" placeholder="+225 XX XX XXX XXX">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_email_${parentCount}" class="form-label">Email</label>
                                <input type="email" class="form-control" id="parent_email_${parentCount}" name="parents[${parentCount}][email]">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="parent_profession_${parentCount}" class="form-label">Profession</label>
                                <input type="text" class="form-control" id="parent_profession_${parentCount}" name="parents[${parentCount}][profession]">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="parent_adresse_${parentCount}" class="form-label">Adresse</label>
                                <textarea class="form-control" id="parent_adresse_${parentCount}" name="parents[${parentCount}][adresse]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="parent-existant d-none">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="parent_id_${parentCount}" class="form-label">Sélectionner un parent existant</label>
                                <select class="form-control select-parent" id="parent_id_${parentCount}" name="parents[${parentCount}][parent_id]">
                                    <option value="">Rechercher un parent...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#parents-container').append(parentHtml);
            
            // Réinitialiser Select2 sur le nouveau parent
            if (typeof $.fn.select2 !== 'undefined') {
                $(`#parent_id_${parentCount}`).select2({
                    theme: 'bootstrap4',
                    placeholder: 'Rechercher un parent...',
                    allowClear: true,
                    minimumInputLength: 2,
                    ajax: {
                        url: '{{ route("esbtp.api.search-parents") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.nom + ' ' + item.prenoms + ' (' + item.telephone + ')'
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }
            
            parentCount++;
        });
        
        // Suppression de parents
        $(document).on('click', '.remove-parent', function(e) {
            e.preventDefault();
            console.log('Remove parent button clicked');
            $(this).closest('.parent-item').remove();
            parentCount--;
            
            // Mise à jour des index des parents restants
            $('.parent-item').each(function(index) {
                $(this).find('h6').text(`Parent / Tuteur #${index + 1}`);
            });
        });
        
        // Débogage général
        console.log('Total parents container:', $('#parents-container').length);
        console.log('Add parent button exists:', $('#add-parent').length);
    });
</script>
@endsection 