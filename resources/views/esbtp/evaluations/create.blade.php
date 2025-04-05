@extends('esbtp.evaluations.form')

@section('title', 'Ajouter une évaluation - ESBTP-yAKRO')

@section('content_form')
<div class="container-fluid">
    <!-- Matières statiques (fallback) -->
    <div id="matiere-data" data-matieres="{{ json_encode($matieres) }}" style="display: none;"></div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Ajouter une nouvelle évaluation
                    </h5>
                    <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start border-danger border-4" role="alert">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-circle fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">Erreur de validation</h5>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Informations générales de l'évaluation -->
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-bottom border-primary border-opacity-25">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-info-circle me-2"></i>Informations générales
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label text-muted mb-1">Titre de l'évaluation <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control shadow-sm @error('titre') is-invalid @enderror"
                                               id="titre" name="titre" value="{{ old('titre') }}"
                                               placeholder="Ex: Examen final de mathématiques" required>
                                        @error('titre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Champ Type d'évaluation -->
                                    <div class="mb-3">
                                        <label for="type" class="form-label text-muted mb-1">Type d'évaluation <span class="text-danger">*</span></label>
                                        <select class="form-select shadow-sm @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="">-- Sélectionner un type --</option>
                                            @foreach($types as $typeKey => $typeValue)
                                                <option value="{{ $typeKey }}" {{ old('type') == $typeKey ? 'selected' : '' }}>
                                                    {{ $typeValue }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text"><i class="fas fa-lightbulb text-warning me-1"></i> Le type détermine comment l'évaluation sera affichée</div>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Champ Période -->
                                    <div class="mb-3">
                                        <label for="periode" class="form-label text-muted mb-1">Période <span class="text-danger">*</span></label>
                                        <select class="form-select shadow-sm @error('periode') is-invalid @enderror" id="periode" name="periode" required>
                                            <option value="">-- Sélectionner une période --</option>
                                            <option value="semestre1" {{ old('periode') == 'semestre1' ? 'selected' : '' }}>Semestre 1</option>
                                            <option value="semestre2" {{ old('periode') == 'semestre2' ? 'selected' : '' }}>Semestre 2</option>
                                        </select>
                                        @error('periode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="date_evaluation" class="form-label text-muted mb-1">Date <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="far fa-calendar-alt text-muted"></i>
                                                </span>
                                                <input type="date" class="form-control border-start-0 ps-0 @error('date_evaluation') is-invalid @enderror"
                                                       id="date_evaluation" name="date_evaluation" value="{{ old('date_evaluation') }}" required>
                                            </div>
                                            @error('date_evaluation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="duree_minutes" class="form-label text-muted mb-1">Durée (minutes)</label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="far fa-clock text-muted"></i>
                                                </span>
                                                <input type="number" class="form-control border-start-0 ps-0 @error('duree_minutes') is-invalid @enderror"
                                                       id="duree_minutes" name="duree_minutes" value="{{ old('duree_minutes') }}"
                                                       min="1" placeholder="Ex: 120">
                                            </div>
                                            @error('duree_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paramètres de notation -->
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-bottom border-primary border-opacity-25">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-calculator me-2"></i>Paramètres de notation
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="classe_id" class="form-label text-muted mb-1">Classe <span class="text-danger">*</span></label>
                                        <select class="form-select shadow-sm select2 @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id" required>
                                            <option value="">-- Sélectionner une classe --</option>
                                            @foreach($classes as $classe)
                                                <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                                    {{ $classe->name }} ({{ $classe->filiere->name }} - {{ $classe->niveau->name }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('classe_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="matiere_id" class="form-label text-muted mb-1">Matière <span class="text-danger">*</span></label>
                                        <select id="matiere_id" name="matiere_id" class="form-select shadow-sm select2 @error('matiere_id') is-invalid @enderror" required>
                                            <option value="">-- Sélectionner une matière --</option>
                                            @foreach($matieres as $matiere)
                                                <option value="{{ $matiere->id }}" {{ (old('matiere_id', $matiere_id) == $matiere->id) ? 'selected' : '' }}>
                                                    {{ $matiere->nom ?? $matiere->name ?? 'Matière ' . $matiere->id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text"><i class="fas fa-lightbulb text-warning me-1"></i> Les matières disponibles dépendent de la classe sélectionnée</div>
                                        @error('matiere_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="coefficient" class="form-label text-muted mb-1">Coefficient <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-balance-scale text-muted"></i>
                                                </span>
                                                <input type="number" class="form-control border-start-0 ps-0 @error('coefficient') is-invalid @enderror"
                                                       id="coefficient" name="coefficient" value="{{ old('coefficient', 1) }}"
                                                       step="0.1" min="0.1" required>
                                            </div>
                                            @error('coefficient')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="bareme" class="form-label text-muted mb-1">Barème <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-award text-muted"></i>
                                                </span>
                                                <input type="number" class="form-control border-start-0 ps-0 @error('bareme') is-invalid @enderror"
                                                       id="bareme" name="bareme" value="{{ old('bareme', 20) }}"
                                                       step="0.1" min="1" required>
                                                <span class="input-group-text">points</span>
                                            </div>
                                            <div class="form-text"><i class="fas fa-info-circle text-info me-1"></i> Nombre de points total (généralement 20)</div>
                                            @error('bareme')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description et options supplémentaires -->
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white border-bottom border-primary border-opacity-25">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-list-alt me-2"></i>Description et options
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="description" class="form-label text-muted mb-1">Description (optionnelle)</label>
                                        <textarea class="form-control shadow-sm @error('description') is-invalid @enderror"
                                                  id="description" name="description" rows="4"
                                                  placeholder="Décrivez le contenu de l'évaluation, les chapitres couverts, etc...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="card bg-light border-0 shadow-sm p-3 mt-3">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox"
                                                   id="is_published" name="is_published" value="1"
                                                   role="switch" {{ old('is_published') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_published">
                                                <span class="fw-medium">Publier immédiatement</span>
                                            </label>
                                            <div class="form-text mt-1">
                                                <i class="fas fa-info-circle text-info me-1"></i>
                                                Une évaluation publiée est visible par les enseignants et permet la saisie des notes.
                                                Vous pourrez la publier ultérieurement si nécessaire.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons de soumission -->
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4" role="alert">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fas fa-lightbulb fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="alert-heading fw-bold">Conseil pour la création d'évaluations</h6>
                                                <p class="mb-0">
                                                    Une fois l'évaluation créée, vous pourrez accéder à la page de saisie rapide des notes
                                                    où vous pourrez entrer les notes de tous les étudiants de la classe simultanément.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="reset" class="btn btn-outline-secondary me-md-2 shadow-sm">
                                            <i class="fas fa-undo me-1"></i>Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-primary shadow-sm">
                                            <i class="fas fa-save me-1"></i>Enregistrer l'évaluation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-primary {
        color: #01632f !important;
    }

    .btn-primary {
        background-color: #01632f;
        border-color: #01632f;
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: #014a23;
        border-color: #014a23;
    }

    .form-control:focus, .form-select:focus {
        border-color: #01632f;
        box-shadow: 0 0 0 0.25rem rgba(1, 99, 47, 0.25);
    }

    .form-check-input:checked {
        background-color: #01632f;
        border-color: #01632f;
    }
</style>
@endpush

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionner une option'
        });

        // Store the initial matiere_id
        const initialMatiereId = '{{ old('matiere_id', $matiere_id ?? '') }}';
        console.log('Initial matiere_id:', initialMatiereId);

        // Handle classe_id change
        $('#classe_id').on('change', function() {
            const classeId = $(this).val();
            if (!classeId) {
                resetMatiereSelect();
                return;
            }
            loadMatieres(classeId);
        });

        function resetMatiereSelect() {
            const $matiereSelect = $('#matiere_id');
            $matiereSelect.empty().append('<option value="">-- Sélectionner une matière --</option>');

            // Load static matieres as fallback
            const staticMatieres = JSON.parse($('#matiere-data').attr('data-matieres'));
            if (staticMatieres && staticMatieres.length > 0) {
                staticMatieres.forEach(function(matiere) {
                    const id = matiere.id;
                    const name = matiere.nom || matiere.name || ('Matière ' + id);
                    const selected = (id == initialMatiereId) ? 'selected' : '';
                    $matiereSelect.append(`<option value="${id}" ${selected}>${name}</option>`);
                });
            }

            $matiereSelect.prop('disabled', false).trigger('change');
        }

        function loadMatieres(classeId) {
            const $matiereSelect = $('#matiere_id');
            $matiereSelect.prop('disabled', true);

            // Afficher un indicateur de chargement
            $matiereSelect.empty().append('<option value="">Chargement des matières...</option>');

            // First try the API endpoint
            $.ajax({
                url: `/esbtp/api/classes/${classeId}/matieres`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('API response:', data);
                    updateMatiereSelect(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading subjects:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);

                    // Fallback to static matieres
                    resetMatiereSelect();
                }
            });
        }

        function updateMatiereSelect(matieres) {
            const $matiereSelect = $('#matiere_id');
            $matiereSelect.empty().append('<option value="">-- Sélectionner une matière --</option>');

            if (matieres && matieres.length > 0) {
                matieres.forEach(function(matiere) {
                    const id = matiere.id;
                    const name = matiere.nom || matiere.name || ('Matière ' + id);
                    const selected = (id == initialMatiereId) ? 'selected' : '';
                    $matiereSelect.append(`<option value="${id}" ${selected}>${name}</option>`);
                });
            } else {
                // No matieres found for this class, show a message
                $matiereSelect.append('<option value="" disabled>Aucune matière disponible pour cette classe</option>');
            }

            $matiereSelect.prop('disabled', false).trigger('change');
        }

        // Trigger change on classe_id if it has a value
        if ($('#classe_id').val()) {
            $('#classe_id').trigger('change');
        }
    });
</script>
@endsection
