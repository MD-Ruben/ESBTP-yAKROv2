@extends('layouts.app')

@section('title', 'Modifier la matière : ' . $matiere->name . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier la matière : {{ $matiere->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.matieres.show', $matiere) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye me-1"></i>Détails
                        </a>
                        <a href="{{ route('esbtp.matieres.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i>Liste des matières
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('esbtp.matieres.update', $matiere) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations générales</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Nom de la matière -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nom de la matière <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $matiere->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Code de la matière -->
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Code de la matière <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $matiere->code) }}" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Unité d'enseignement -->
                                        <div class="mb-3">
                                            <label for="unite_enseignement_id" class="form-label">Unité d'enseignement</label>
                                            <select class="form-select select2 @error('unite_enseignement_id') is-invalid @enderror" id="unite_enseignement_id" name="unite_enseignement_id">
                                                <option value="">Sélectionner une unité d'enseignement</option>
                                                @foreach($unitesEnseignement as $ue)
                                                    <option value="{{ $ue->id }}" {{ old('unite_enseignement_id', $matiere->unite_enseignement_id) == $ue->id ? 'selected' : '' }}>
                                                        {{ $ue->name }} ({{ $ue->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unite_enseignement_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Paramètres d'évaluation</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Coefficient -->
                                        <div class="mb-3">
                                            <label for="coefficient_default" class="form-label">Coefficient par défaut <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('coefficient_default') is-invalid @enderror" id="coefficient_default" name="coefficient_default" value="{{ old('coefficient_default', $matiere->coefficient_default) }}" min="1" step="0.5" required>
                                            @error('coefficient_default')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Volume horaire -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="heures_cm_default" class="form-label">Heures de cours magistraux</label>
                                                <input type="number" class="form-control @error('heures_cm_default') is-invalid @enderror" id="heures_cm_default" name="heures_cm_default" value="{{ old('heures_cm_default', $matiere->heures_cm_default) }}" min="0" step="0.5">
                                                @error('heures_cm_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="heures_td_default" class="form-label">Heures de travaux dirigés</label>
                                                <input type="number" class="form-control @error('heures_td_default') is-invalid @enderror" id="heures_td_default" name="heures_td_default" value="{{ old('heures_td_default', $matiere->heures_td_default) }}" min="0" step="0.5">
                                                @error('heures_td_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="heures_tp_default" class="form-label">Heures de travaux pratiques</label>
                                                <input type="number" class="form-control @error('heures_tp_default') is-invalid @enderror" id="heures_tp_default" name="heures_tp_default" value="{{ old('heures_tp_default', $matiere->heures_tp_default) }}" min="0" step="0.5">
                                                @error('heures_tp_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="total_heures_default" class="form-label">Total des heures <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('total_heures_default') is-invalid @enderror" id="total_heures_default" name="total_heures_default" value="{{ old('total_heures_default', $matiere->total_heures_default) }}" min="0" step="0.5" required readonly>
                                                @error('total_heures_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-link me-2"></i>Associations</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Formations associées -->
                                        <div class="mb-3">
                                            <label for="formations" class="form-label">Formations <span class="text-danger">*</span></label>
                                            <select class="form-select select2 @error('formations') is-invalid @enderror" id="formations" name="formations[]" multiple required>
                                                @foreach($formations as $formation)
                                                    <option value="{{ $formation->id }}" {{ (old('formations') ? in_array($formation->id, old('formations')) : $matiere->formations->contains($formation->id)) ? 'selected' : '' }}>
                                                        {{ $formation->name }} ({{ $formation->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('formations')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Niveaux d'études associés -->
                                        <div class="mb-3">
                                            <label for="niveaux_etudes" class="form-label">Niveaux d'études <span class="text-danger">*</span></label>
                                            <select class="form-select select2 @error('niveaux_etudes') is-invalid @enderror" id="niveaux_etudes" name="niveaux_etudes[]" multiple required>
                                                @foreach($niveauxEtudes as $niveau)
                                                    <option value="{{ $niveau->id }}" {{ (old('niveaux_etudes') ? in_array($niveau->id, old('niveaux_etudes')) : $matiere->niveauxEtudes->contains($niveau->id)) ? 'selected' : '' }}>
                                                        {{ $niveau->name }} ({{ $niveau->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('niveaux_etudes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Enseignants associés -->
                                        <div class="mb-3">
                                            <label for="enseignants" class="form-label">Enseignants</label>
                                            <select class="form-select select2 @error('enseignants') is-invalid @enderror" id="enseignants" name="enseignants[]" multiple>
                                                @foreach($enseignants as $enseignant)
                                                    <option value="{{ $enseignant->id }}" {{ (old('enseignants') ? in_array($enseignant->id, old('enseignants')) : $matiere->enseignants->contains($enseignant->id)) ? 'selected' : '' }}>
                                                        {{ $enseignant->user->name }} ({{ $enseignant->matricule }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('enseignants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-align-left me-2"></i>Description et options</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $matiere->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Statut -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $matiere->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Matière active</label>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Une matière inactive ne pourra pas être utilisée dans les emplois du temps ou les évaluations.
                                        </small>
                                        
                                        <!-- Alertes pour les éléments liés -->
                                        @if($matiere->seancesCours->count() > 0 || $matiere->evaluations->count() > 0)
                                            <div class="alert alert-warning mt-3 mb-0">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Attention :</strong> Cette matière est liée à :
                                                <ul class="mb-0 mt-1">
                                                    @if($matiere->seancesCours->count() > 0)
                                                        <li>{{ $matiere->seancesCours->count() }} séance(s) de cours</li>
                                                    @endif
                                                    @if($matiere->evaluations->count() > 0)
                                                        <li>{{ $matiere->evaluations->count() }} évaluation(s)</li>
                                                    @endif
                                                </ul>
                                                La modification de cette matière peut affecter ces éléments.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 d-flex justify-content-between">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                                <div>
                                    <a href="{{ route('esbtp.matieres.show', $matiere) }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Enregistrer les modifications
                                    </button>
                                </div>
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
                <p>Êtes-vous sûr de vouloir supprimer cette matière ?</p>
                <p><strong>Nom :</strong> {{ $matiere->name }}</p>
                
                @if($matiere->seancesCours->count() > 0 || $matiere->evaluations->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette matière est liée à :
                        <ul class="mb-0 mt-1">
                            @if($matiere->seancesCours->count() > 0)
                                <li>{{ $matiere->seancesCours->count() }} séance(s) de cours</li>
                            @endif
                            @if($matiere->evaluations->count() > 0)
                                <li>{{ $matiere->evaluations->count() }} évaluation(s)</li>
                            @endif
                        </ul>
                        La suppression de cette matière pourrait causer des erreurs dans le système. Assurez-vous de supprimer ces éléments liés avant de continuer.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.matieres.destroy', $matiere) }}" method="POST">
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
        // Initialisation de Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Calcul automatique du total des heures
        function calculateTotalHours() {
            const cm = parseFloat($('#heures_cm_default').val()) || 0;
            const td = parseFloat($('#heures_td_default').val()) || 0;
            const tp = parseFloat($('#heures_tp_default').val()) || 0;
            const total = cm + td + tp;
            $('#total_heures_default').val(total);
        }
        
        $('#heures_cm_default, #heures_td_default, #heures_tp_default').on('input', calculateTotalHours);
        calculateTotalHours(); // Calcul initial
    });
</script>
@endsection 