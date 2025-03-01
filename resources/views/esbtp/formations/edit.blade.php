@extends('layouts.app')

@section('title', 'Modifier une formation - ESBTP-yAKRO')

@section('page_title', 'Modifier une formation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier la formation : {{ $formation->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.formations.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                        <a href="{{ route('esbtp.formations.show', $formation) }}" class="btn btn-info">
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

                    <form action="{{ route('esbtp.formations.update', $formation) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom de la formation *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $formation->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code de la formation *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $formation->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $formation->description) }}</textarea>
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
                                    <select class="form-select select2 @error('filiere_ids') is-invalid @enderror" id="filiere_ids" name="filiere_ids[]" multiple>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" {{ in_array($filiere->id, old('filiere_ids', $formation->filieres->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $filiere->name }} ({{ $filiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filiere_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les filières auxquelles cette formation est rattachée</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau_ids" class="form-label">Niveaux d'études associés</label>
                                    <select class="form-select select2 @error('niveau_ids') is-invalid @enderror" id="niveau_ids" name="niveau_ids[]" multiple>
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}" {{ in_array($niveau->id, old('niveau_ids', $formation->niveauxEtudes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $niveau->name }} ({{ $niveau->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les niveaux d'études auxquels cette formation est rattachée</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $formation->is_active) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Formation active
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Une formation inactive ne sera pas disponible lors de la création de classes.
                            </div>
                        </div>
                        
                        @if($formation->classes->count() > 0 || $formation->matieres->count() > 0)
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informations associées</h6>
                                @if($formation->classes->count() > 0)
                                    <p class="mb-1">Cette formation est utilisée dans <strong>{{ $formation->classes->count() }} classe(s)</strong>.</p>
                                @endif
                                
                                @if($formation->matieres->count() > 0)
                                    <p class="mb-1">Cette formation est associée à <strong>{{ $formation->matieres->count() }} matière(s)</strong>.</p>
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
                <p>Êtes-vous sûr de vouloir supprimer cette formation ?</p>
                <p><strong>Nom :</strong> {{ $formation->name }}</p>
                
                @if($formation->classes->count() > 0 || $formation->matieres->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette formation est liée à :
                        <ul class="mb-0 mt-1">
                            @if($formation->classes->count() > 0)
                                <li>{{ $formation->classes->count() }} classe(s)</li>
                            @endif
                            @if($formation->matieres->count() > 0)
                                <li>{{ $formation->matieres->count() }} matière(s)</li>
                            @endif
                        </ul>
                        La suppression de cette formation pourrait causer des erreurs dans le système. Assurez-vous de supprimer ou de réaffecter ces éléments avant de continuer.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.formations.destroy', $formation) }}" method="POST">
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
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endsection 