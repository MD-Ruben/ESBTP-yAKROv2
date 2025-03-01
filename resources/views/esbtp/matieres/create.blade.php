@extends('layouts.app')

@section('title', 'Ajouter une matière - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une nouvelle matière</h5>
                    <a href="{{ route('esbtp.matieres.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i>Liste des matières
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('esbtp.matieres.store') }}" method="POST">
                        @csrf
                        
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
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Code de la matière -->
                                        <div class="mb-3">
                                            <label for="code" class="form-label">Code de la matière <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
                                            <small class="form-text text-muted">Si laissé vide, le code sera généré automatiquement.</small>
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
                                                    <option value="{{ $ue->id }}" {{ old('unite_enseignement_id') == $ue->id ? 'selected' : '' }}>
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
                                            <input type="number" class="form-control @error('coefficient_default') is-invalid @enderror" id="coefficient_default" name="coefficient_default" value="{{ old('coefficient_default', 1) }}" min="1" step="0.5" required>
                                            @error('coefficient_default')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Volume horaire -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="heures_cm_default" class="form-label">Heures de cours magistraux</label>
                                                <input type="number" class="form-control @error('heures_cm_default') is-invalid @enderror" id="heures_cm_default" name="heures_cm_default" value="{{ old('heures_cm_default', 0) }}" min="0" step="0.5">
                                                @error('heures_cm_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="heures_td_default" class="form-label">Heures de travaux dirigés</label>
                                                <input type="number" class="form-control @error('heures_td_default') is-invalid @enderror" id="heures_td_default" name="heures_td_default" value="{{ old('heures_td_default', 0) }}" min="0" step="0.5">
                                                @error('heures_td_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="heures_tp_default" class="form-label">Heures de travaux pratiques</label>
                                                <input type="number" class="form-control @error('heures_tp_default') is-invalid @enderror" id="heures_tp_default" name="heures_tp_default" value="{{ old('heures_tp_default', 0) }}" min="0" step="0.5">
                                                @error('heures_tp_default')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="total_heures_default" class="form-label">Total des heures <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('total_heures_default') is-invalid @enderror" id="total_heures_default" name="total_heures_default" value="{{ old('total_heures_default', 0) }}" min="0" step="0.5" required readonly>
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
                                                    <option value="{{ $formation->id }}" {{ (old('formations') && in_array($formation->id, old('formations'))) || (isset($formationId) && $formationId == $formation->id) ? 'selected' : '' }}>
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
                                                    <option value="{{ $niveau->id }}" {{ old('niveaux_etudes') && in_array($niveau->id, old('niveaux_etudes')) ? 'selected' : '' }}>
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
                                                    <option value="{{ $enseignant->id }}" {{ old('enseignants') && in_array($enseignant->id, old('enseignants')) ? 'selected' : '' }}>
                                                        {{ $enseignant->user->name }} ({{ $enseignant->matricule }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Vous pourrez ajouter ou modifier les enseignants plus tard.</small>
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
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Statut -->
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Matière active</label>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Une matière inactive ne pourra pas être utilisée dans les emplois du temps ou les évaluations.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 text-end">
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fas fa-undo me-1"></i>Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer la matière
                                </button>
                            </div>
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
        // Initialisation de Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Génération automatique du code
        $('#name').on('blur', function() {
            if ($('#code').val() === '') {
                let name = $(this).val().trim().toUpperCase();
                if (name) {
                    // Créer le code à partir des 3 premières lettres de chaque mot
                    let code = name.split(/\s+/).map(word => word.substring(0, 3)).join('');
                    $('#code').val(code);
                }
            }
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