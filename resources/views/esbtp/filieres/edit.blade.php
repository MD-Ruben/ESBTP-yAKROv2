@extends('layouts.app')

@section('title', 'Modifier une filière - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier la filière : {{ $filiere->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                        <a href="{{ route('esbtp.filieres.show', $filiere) }}" class="btn btn-info">
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

                    <form action="{{ route('esbtp.filieres.update', $filiere) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom de la filière *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $filiere->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code de la filière *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $filiere->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id" class="form-label">Filière parente (pour une option)</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                        <option value="">Aucune (filière principale)</option>
                                        @foreach($filieres as $parentFiliere)
                                            @if($parentFiliere->id != $filiere->id && !$parentFiliere->isDescendantOf($filiere))
                                                <option value="{{ $parentFiliere->id }}" {{ old('parent_id', $filiere->parent_id) == $parentFiliere->id ? 'selected' : '' }}>
                                                    {{ $parentFiliere->name }} ({{ $parentFiliere->code }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez une filière parente si celle-ci est une option</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Type de formation *</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="technique" {{ old('type', $filiere->type) == 'technique' ? 'selected' : '' }}>Technique</option>
                                        <option value="professionnelle" {{ old('type', $filiere->type) == 'professionnelle' ? 'selected' : '' }}>Professionnelle</option>
                                        <option value="universitaire" {{ old('type', $filiere->type) == 'universitaire' ? 'selected' : '' }}>Universitaire</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $filiere->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="formation_ids" class="form-label">Formations associées</label>
                                    <select class="form-select @error('formation_ids') is-invalid @enderror" id="formation_ids" name="formation_ids[]" multiple>
                                        @foreach($formations as $formation)
                                            <option value="{{ $formation->id }}" {{ in_array($formation->id, old('formation_ids', $filiere->formations->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $formation->name }} ({{ $formation->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('formation_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les formations associées à cette filière</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau_etude_ids" class="form-label">Niveaux d'études associés</label>
                                    <select class="form-select @error('niveau_etude_ids') is-invalid @enderror" id="niveau_etude_ids" name="niveau_etude_ids[]" multiple>
                                        @foreach($niveauxEtudes as $niveau)
                                            <option value="{{ $niveau->id }}" {{ in_array($niveau->id, old('niveau_etude_ids', $filiere->niveauxEtudes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $niveau->name }} ({{ $niveau->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_etude_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les niveaux d'études associés à cette filière</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $filiere->is_active) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Filière active
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Une filière inactive ne sera pas disponible lors de la création de classes.
                            </div>
                        </div>
                        
                        @if($filiere->options->count() > 0 || $filiere->classes->count() > 0)
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informations associées</h6>
                                @if($filiere->options->count() > 0)
                                    <p class="mb-1">Cette filière possède <strong>{{ $filiere->options->count() }} option(s)</strong> :</p>
                                    <ul>
                                        @foreach($filiere->options as $option)
                                            <li>{{ $option->name }} ({{ $option->code }})</li>
                                        @endforeach
                                    </ul>
                                @endif
                                
                                @if($filiere->classes->count() > 0)
                                    <p class="mb-1">Cette filière est utilisée dans <strong>{{ $filiere->classes->count() }} classe(s)</strong>.</p>
                                @endif
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i>Supprimer la filière
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
                <p>Êtes-vous sûr de vouloir supprimer cette filière ?</p>
                <p><strong>Nom :</strong> {{ $filiere->name }}</p>
                
                @if($filiere->options->count() > 0 || $filiere->classes->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette filière est liée à :
                        <ul class="mb-0 mt-1">
                            @if($filiere->options->count() > 0)
                                <li>{{ $filiere->options->count() }} option(s)</li>
                            @endif
                            @if($filiere->classes->count() > 0)
                                <li>{{ $filiere->classes->count() }} classe(s)</li>
                            @endif
                        </ul>
                        La suppression de cette filière pourrait causer des erreurs dans le système. Assurez-vous de supprimer ou de réaffecter ces éléments avant de continuer.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.filieres.destroy', $filiere) }}" method="POST">
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
        $('#parent_id, #type, #formation_ids, #niveau_etude_ids').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endsection 