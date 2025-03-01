@extends('layouts.app')

@section('title', 'Modifier un niveau d\'étude - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier le niveau d'étude : {{ $niveauEtude->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                        <a href="{{ route('esbtp.niveaux-etudes.show', $niveauEtude) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i>Voir les détails
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

                    <form action="{{ route('esbtp.niveaux-etudes.update', $niveauEtude) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom du niveau d'étude *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $niveauEtude->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code du niveau d'étude *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $niveauEtude->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau" class="form-label">Numéro d'année *</label>
                                    <select class="form-select @error('niveau') is-invalid @enderror" id="niveau" name="niveau" required>
                                        <option value="">Sélectionner un numéro d'année</option>
                                        @for($i = 1; $i <= 7; $i++)
                                            <option value="{{ $i }}" {{ old('niveau', $niveauEtude->niveau) == $i ? 'selected' : '' }}>
                                                {{ $i }}{{ $i == 1 ? 'ère' : 'ème' }} année
                                            </option>
                                        @endfor
                                    </select>
                                    @error('niveau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diplome" class="form-label">Diplôme associé</label>
                                    <input type="text" class="form-control @error('diplome') is-invalid @enderror" id="diplome" name="diplome" value="{{ old('diplome', $niveauEtude->diplome) }}">
                                    @error('diplome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Si ce niveau mène à un diplôme, précisez lequel (ex: BTS, Licence, etc.)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $niveauEtude->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filiere_ids" class="form-label">Filières associées</label>
                                    <select class="form-select @error('filiere_ids') is-invalid @enderror" id="filiere_ids" name="filiere_ids[]" multiple>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" {{ in_array($filiere->id, old('filiere_ids', $niveauEtude->filieres->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $filiere->name }} ({{ $filiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filiere_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les filières auxquelles ce niveau d'étude est rattaché</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="formation_ids" class="form-label">Formations associées</label>
                                    <select class="form-select @error('formation_ids') is-invalid @enderror" id="formation_ids" name="formation_ids[]" multiple>
                                        @foreach($formations as $formation)
                                            <option value="{{ $formation->id }}" {{ in_array($formation->id, old('formation_ids', $niveauEtude->formations->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $formation->name }} ({{ $formation->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('formation_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les formations associées à ce niveau d'étude</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $niveauEtude->is_active) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Niveau d'étude actif
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Un niveau d'étude inactif ne sera pas disponible lors de la création de classes.
                            </div>
                        </div>
                        
                        @if($niveauEtude->classes->count() > 0 || $niveauEtude->matieres->count() > 0)
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informations associées</h6>
                                @if($niveauEtude->classes->count() > 0)
                                    <p class="mb-1">Ce niveau d'étude est utilisé dans <strong>{{ $niveauEtude->classes->count() }} classe(s)</strong>.</p>
                                @endif
                                
                                @if($niveauEtude->matieres->count() > 0)
                                    <p class="mb-1">Ce niveau d'étude est associé à <strong>{{ $niveauEtude->matieres->count() }} matière(s)</strong>.</p>
                                @endif
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                            <div>
                                <button type="reset" class="btn btn-secondary me-2">Annuler les modifications</button>
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce niveau d'étude ?</p>
                <p><strong>Nom :</strong> {{ $niveauEtude->name }}</p>
                
                @if($niveauEtude->classes->count() > 0 || $niveauEtude->matieres->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Ce niveau d'étude est lié à :
                        <ul class="mb-0 mt-1">
                            @if($niveauEtude->classes->count() > 0)
                                <li>{{ $niveauEtude->classes->count() }} classe(s)</li>
                            @endif
                            @if($niveauEtude->matieres->count() > 0)
                                <li>{{ $niveauEtude->matieres->count() }} matière(s)</li>
                            @endif
                        </ul>
                        La suppression de ce niveau d'étude pourrait causer des erreurs dans le système. Assurez-vous de supprimer ou de réaffecter ces éléments avant de continuer.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.niveaux-etudes.destroy', $niveauEtude) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Amélioration des listes déroulantes avec Select2
        $('#niveau, #filiere_ids, #formation_ids').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endsection 