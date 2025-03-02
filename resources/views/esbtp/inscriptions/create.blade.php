@extends('layouts.app')

@section('title', 'Nouvelle Inscription')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouvelle Inscription</h1>
        <a href="{{ route('esbtp.inscriptions.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <!-- Formulaire d'inscription -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Formulaire d'inscription</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('esbtp.inscriptions.store') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations générales -->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <h5 class="font-weight-bold">Informations générales</h5>
                        <hr>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="annee_universitaire_id">Année universitaire</label>
                        <select class="form-control @error('annee_universitaire_id') is-invalid @enderror" 
                                id="annee_universitaire_id" name="annee_universitaire_id" required>
                            <option value="">Sélectionner une année</option>
                            @foreach($annees as $annee)
                                <option value="{{ $annee->id }}" 
                                    {{ (old('annee_universitaire_id') == $annee->id || (isset($anneeEnCours) && $anneeEnCours->id == $annee->id)) ? 'selected' : '' }}>
                                    {{ $annee->annee_scolaire }}
                                </option>
                            @endforeach
                        </select>
                        @error('annee_universitaire_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="filiere_id">Filière</label>
                        <select class="form-control @error('filiere_id') is-invalid @enderror" 
                                id="filiere_id" name="filiere_id" required>
                            <option value="">Sélectionner une filière</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('filiere_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="niveau_etude_id">Niveau d'études</label>
                        <select class="form-control @error('niveau_etude_id') is-invalid @enderror" 
                                id="niveau_etude_id" name="niveau_etude_id" required>
                            <option value="">Sélectionner un niveau</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}" {{ old('niveau_etude_id') == $niveau->id ? 'selected' : '' }}>
                                    {{ $niveau->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('niveau_etude_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="classe_id">Classe</label>
                        <select class="form-control @error('classe_id') is-invalid @enderror" 
                                id="classe_id" name="classe_id" required>
                            <option value="">Sélectionner d'abord une filière et un niveau</option>
                        </select>
                        <small class="form-text text-muted">La liste des classes sera mise à jour en fonction de la filière et du niveau sélectionnés.</small>
                        @error('classe_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Informations de l'étudiant -->
                <div class="row mt-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="font-weight-bold">Informations de l'étudiant</h5>
                        <hr>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom">Nom</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom') }}" required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="prenom">Prénom(s)</label>
                        <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                               id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date_naissance">Date de naissance</label>
                        <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" 
                               id="date_naissance" name="date_naissance" value="{{ old('date_naissance') }}" required>
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="genre">Genre</label>
                        <select class="form-control @error('genre') is-invalid @enderror" 
                                id="genre" name="genre" required>
                            <option value="">Sélectionner</option>
                            <option value="homme" {{ old('genre') == 'homme' ? 'selected' : '' }}>Homme</option>
                            <option value="femme" {{ old('genre') == 'femme' ? 'selected' : '' }}>Femme</option>
                        </select>
                        @error('genre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="telephone">Téléphone</label>
                        <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                               id="telephone" name="telephone" value="{{ old('telephone') }}" 
                               placeholder="+225 XX XX XXX XXX">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="ville">Ville</label>
                        <input type="text" class="form-control @error('ville') is-invalid @enderror" 
                               id="ville" name="ville" value="{{ old('ville') }}">
                        @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="commune">Commune</label>
                        <input type="text" class="form-control @error('commune') is-invalid @enderror" 
                               id="commune" name="commune" value="{{ old('commune') }}">
                        @error('commune')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="photo">Photo de profil</label>
                        <input type="file" class="form-control-file @error('photo') is-invalid @enderror" 
                               id="photo" name="photo">
                        <small class="form-text text-muted">Formats acceptés: jpeg, png, jpg. Taille max: 2Mo</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Informations du parent -->
                <div class="row mt-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="font-weight-bold">Informations du parent/tuteur</h5>
                        <hr>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="parent_nom">Nom</label>
                        <input type="text" class="form-control @error('parent_nom') is-invalid @enderror" 
                               id="parent_nom" name="parent_nom" value="{{ old('parent_nom') }}">
                        @error('parent_nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="parent_prenom">Prénom(s)</label>
                        <input type="text" class="form-control @error('parent_prenom') is-invalid @enderror" 
                               id="parent_prenom" name="parent_prenom" value="{{ old('parent_prenom') }}">
                        @error('parent_prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="parent_telephone">Téléphone</label>
                        <input type="text" class="form-control @error('parent_telephone') is-invalid @enderror" 
                               id="parent_telephone" name="parent_telephone" value="{{ old('parent_telephone') }}" 
                               placeholder="+225 XX XX XXX XXX">
                        @error('parent_telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="parent_email">Email</label>
                        <input type="email" class="form-control @error('parent_email') is-invalid @enderror" 
                               id="parent_email" name="parent_email" value="{{ old('parent_email') }}">
                        @error('parent_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="parent_adresse">Adresse</label>
                        <textarea class="form-control @error('parent_adresse') is-invalid @enderror" 
                                  id="parent_adresse" name="parent_adresse" rows="2">{{ old('parent_adresse') }}</textarea>
                        @error('parent_adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Boutons de soumission -->
                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer l'inscription
                        </button>
                        <a href="{{ route('esbtp.inscriptions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Mise à jour dynamique de la liste des classes en fonction de la filière et du niveau
        $('#filiere_id, #niveau_etude_id').change(function() {
            const filiereId = $('#filiere_id').val();
            const niveauId = $('#niveau_etude_id').val();
            const anneeId = $('#annee_universitaire_id').val();
            
            if (filiereId && niveauId) {
                $.ajax({
                    url: '{{ route("inscriptions.getClasses") }}',
                    type: 'GET',
                    data: {
                        filiere_id: filiereId,
                        niveau_id: niveauId,
                        annee_id: anneeId
                    },
                    success: function(data) {
                        let options = '<option value="">Sélectionner une classe</option>';
                        data.forEach(function(classe) {
                            options += `<option value="${classe.id}">${classe.nom}</option>`;
                        });
                        $('#classe_id').html(options);
                    },
                    error: function(xhr) {
                        console.error('Erreur lors du chargement des classes:', xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endsection 