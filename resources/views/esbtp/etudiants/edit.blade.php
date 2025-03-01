@extends('layouts.app')

@section('title', 'Modifier un étudiant - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'étudiant: {{ $etudiant->nom }} {{ $etudiant->prenoms }}</h5>
                    <div>
                        <a href="{{ route('esbtp.etudiants.show', $etudiant) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye me-1"></i>Voir les détails
                        </a>
                        <a href="{{ route('esbtp.etudiants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
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

                    <form action="{{ route('esbtp.etudiants.update', $etudiant) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informations personnelles</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="matricule" class="form-label">Matricule</label>
                                                <input type="text" class="form-control" id="matricule" value="{{ $etudiant->matricule }}" readonly>
                                                <small class="form-text text-muted">Le matricule ne peut pas être modifié.</small>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $etudiant->nom) }}" required>
                                                @error('nom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="prenoms" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('prenoms') is-invalid @enderror" id="prenoms" name="prenoms" value="{{ old('prenoms', $etudiant->prenoms) }}" required>
                                                @error('prenoms')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="genre" class="form-label">Genre <span class="text-danger">*</span></label>
                                                <select class="form-select @error('genre') is-invalid @enderror" id="genre" name="genre" required>
                                                    <option value="M" {{ old('genre', $etudiant->genre) == 'M' ? 'selected' : '' }}>Masculin</option>
                                                    <option value="F" {{ old('genre', $etudiant->genre) == 'F' ? 'selected' : '' }}>Féminin</option>
                                                </select>
                                                @error('genre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="date_naissance" class="form-label">Date de naissance</label>
                                                <input type="date" class="form-control @error('date_naissance') is-invalid @enderror" id="date_naissance" name="date_naissance" value="{{ old('date_naissance', $etudiant->date_naissance ? $etudiant->date_naissance->format('Y-m-d') : '') }}">
                                                @error('date_naissance')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="telephone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $etudiant->telephone) }}" required>
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $etudiant->email) }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="ville" class="form-label">Ville</label>
                                                <input type="text" class="form-control @error('ville') is-invalid @enderror" id="ville" name="ville" value="{{ old('ville', $etudiant->ville) }}">
                                                @error('ville')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="commune" class="form-label">Commune</label>
                                                <input type="text" class="form-control @error('commune') is-invalid @enderror" id="commune" name="commune" value="{{ old('commune', $etudiant->commune) }}">
                                                @error('commune')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="photo" class="form-label">Photo de profil</label>
                                                <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                                @error('photo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Laissez vide pour conserver la photo actuelle.</small>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                @if($etudiant->photo)
                                                    <div class="mt-2">
                                                        <label class="form-label">Photo actuelle</label>
                                                        <div>
                                                            <img src="{{ asset('storage/'.$etudiant->photo) }}" alt="Photo de profil" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                                                <select class="form-select @error('statut') is-invalid @enderror" id="statut" name="statut" required>
                                                    <option value="actif" {{ old('statut', $etudiant->statut) == 'actif' ? 'selected' : '' }}>Actif</option>
                                                    <option value="inactif" {{ old('statut', $etudiant->statut) == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                                </select>
                                                @error('statut')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
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
                                        @if($etudiant->parents->count() < 2)
                                            <button type="button" class="btn btn-sm btn-primary" id="add-parent">
                                                <i class="fas fa-plus me-1"></i>Ajouter un parent
                                            </button>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div id="parents-container">
                                            @forelse($etudiant->parents as $index => $parent)
                                                <div class="parent-item mb-4 p-3 border rounded">
                                                    <div class="d-flex justify-content-between mb-3">
                                                        <h6>Parent / Tuteur #{{ $index + 1 }}</h6>
                                                        @if($index > 0 || $etudiant->parents->count() > 1)
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-parent" data-parent-id="{{ $parent->id }}">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    
                                                    <input type="hidden" name="parents[{{ $index }}][id]" value="{{ $parent->id }}">
                                                    
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_nom_{{ $index }}" class="form-label">Nom <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="parent_nom_{{ $index }}" name="parents[{{ $index }}][nom]" value="{{ old('parents.'.$index.'.nom', $parent->nom) }}" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_prenoms_{{ $index }}" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="parent_prenoms_{{ $index }}" name="parents[{{ $index }}][prenoms]" value="{{ old('parents.'.$index.'.prenoms', $parent->prenoms) }}" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_relation_{{ $index }}" class="form-label">Relation <span class="text-danger">*</span></label>
                                                            <select class="form-control" id="parent_relation_{{ $index }}" name="parents[{{ $index }}][relation]" required>
                                                                <option value="Père" {{ old('parents.'.$index.'.relation', $parent->pivot->relation) == 'Père' ? 'selected' : '' }}>Père</option>
                                                                <option value="Mère" {{ old('parents.'.$index.'.relation', $parent->pivot->relation) == 'Mère' ? 'selected' : '' }}>Mère</option>
                                                                <option value="Tuteur" {{ old('parents.'.$index.'.relation', $parent->pivot->relation) == 'Tuteur' ? 'selected' : '' }}>Tuteur</option>
                                                                <option value="Autre" {{ old('parents.'.$index.'.relation', $parent->pivot->relation) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_telephone_{{ $index }}" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="parent_telephone_{{ $index }}" name="parents[{{ $index }}][telephone]" value="{{ old('parents.'.$index.'.telephone', $parent->telephone) }}" required>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_email_{{ $index }}" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="parent_email_{{ $index }}" name="parents[{{ $index }}][email]" value="{{ old('parents.'.$index.'.email', $parent->email) }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="parent_profession_{{ $index }}" class="form-label">Profession</label>
                                                            <input type="text" class="form-control" id="parent_profession_{{ $index }}" name="parents[{{ $index }}][profession]" value="{{ old('parents.'.$index.'.profession', $parent->profession) }}">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="parent_adresse_{{ $index }}" class="form-label">Adresse</label>
                                                            <textarea class="form-control" id="parent_adresse_{{ $index }}" name="parents[{{ $index }}][adresse]" rows="2">{{ old('parents.'.$index.'.adresse', $parent->adresse) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="alert alert-info">
                                                    Aucun parent ou tuteur enregistré pour cet étudiant.
                                                </div>
                                            @endforelse
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
                                                <label class="form-label">Compte utilisateur</label>
                                                @if($etudiant->user)
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-success me-2">Actif</span>
                                                        <span>{{ $etudiant->user->email }}</span>
                                                        <a href="{{ route('esbtp.etudiants.reset-password', $etudiant) }}" class="btn btn-sm btn-outline-secondary ms-2" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?')">
                                                            <i class="fas fa-key me-1"></i>Réinitialiser le mot de passe
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-warning me-2">Non créé</span>
                                                        <div class="form-check form-switch ms-2">
                                                            <input class="form-check-input" type="checkbox" id="create_account" name="create_account" value="1" {{ old('create_account') ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="create_account">
                                                                Créer un compte utilisateur pour cet étudiant
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        Un compte sera créé avec l'email fourni ci-dessus. Le mot de passe sera généré automatiquement et envoyé par email à l'étudiant.
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Mettre à jour l'étudiant
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
        // Initialisation de Select2 pour les sélecteurs si disponible
        if (typeof $.fn.select2 !== 'undefined') {
            $('#genre, #statut').select2({
                theme: 'bootstrap4',
                minimumResultsForSearch: Infinity
            });
        }
        
        // Ajout de parents
        let parentCount = {{ $etudiant->parents->count() }};
        $('#add-parent').on('click', function() {
            if (parentCount >= 2) {
                alert('Vous ne pouvez ajouter que 2 parents maximum.');
                return;
            }
            
            const parentHtml = `
                <div class="parent-item mb-4 p-3 border rounded">
                    <div class="d-flex justify-content-between mb-3">
                        <h6>Nouveau parent / tuteur</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-parent">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="parent_nom_new" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="parent_nom_new" name="new_parent[nom]" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="parent_prenoms_new" class="form-label">Prénom(s) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="parent_prenoms_new" name="new_parent[prenoms]" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="parent_relation_new" class="form-label">Relation <span class="text-danger">*</span></label>
                            <select class="form-control" id="parent_relation_new" name="new_parent[relation]" required>
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
                            <label for="parent_telephone_new" class="form-label">Téléphone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="parent_telephone_new" name="new_parent[telephone]" placeholder="+225 XX XX XXX XXX" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="parent_email_new" class="form-label">Email</label>
                            <input type="email" class="form-control" id="parent_email_new" name="new_parent[email]">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="parent_profession_new" class="form-label">Profession</label>
                            <input type="text" class="form-control" id="parent_profession_new" name="new_parent[profession]">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="parent_adresse_new" class="form-label">Adresse</label>
                            <textarea class="form-control" id="parent_adresse_new" name="new_parent[adresse]" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            `;
            
            $('#parents-container').append(parentHtml);
            parentCount++;
            
            // Masquer le bouton d'ajout si on a atteint 2 parents
            if (parentCount >= 2) {
                $('#add-parent').hide();
            }
        });
        
        // Suppression de parents
        $(document).on('click', '.remove-parent', function() {
            const parentItem = $(this).closest('.parent-item');
            const parentId = $(this).data('parent-id');
            
            if (parentId) {
                // Si c'est un parent existant, ajouter un champ hidden pour la suppression
                $('form').append(`<input type="hidden" name="delete_parents[]" value="${parentId}">`);
            }
            
            parentItem.remove();
            parentCount--;
            
            // Afficher le bouton d'ajout si on a moins de 2 parents
            if (parentCount < 2) {
                $('#add-parent').show();
            }
        });
    });
</script>
@endsection 