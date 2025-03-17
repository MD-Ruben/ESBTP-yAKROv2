@extends('esbtp.evaluations.form')

@section('title', 'Ajouter une évaluation - ESBTP-yAKRO')

@section('content_form')
<div class="container-fluid">
    <!-- Matières statiques (fallback) -->
    <div id="matiere-data" data-matieres="{{ json_encode($matieres) }}" style="display: none;"></div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une nouvelle évaluation</h5>
                    <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-secondary">
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

                    <div class="row">
                        <!-- Informations générales de l'évaluation -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations générales</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Titre de l'évaluation <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required>
                                        @error('titre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type d'évaluation <span class="text-danger">*</span></label>
                                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                            <option value="">Sélectionner un type</option>
                                            <option value="examen" {{ old('type') == 'examen' ? 'selected' : '' }}>Examen</option>
                                            <option value="devoir" {{ old('type') == 'devoir' ? 'selected' : '' }}>Devoir</option>
                                            <option value="quiz" {{ old('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                            <option value="tp" {{ old('type') == 'tp' ? 'selected' : '' }}>TP</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="date_evaluation" class="form-label">Date de l'évaluation <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date_evaluation') is-invalid @enderror" id="date_evaluation" name="date_evaluation" value="{{ old('date_evaluation') }}" required>
                                        @error('date_evaluation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="duree_minutes" class="form-label">Durée (en minutes)</label>
                                        <input type="number" class="form-control @error('duree_minutes') is-invalid @enderror" id="duree_minutes" name="duree_minutes" value="{{ old('duree_minutes') }}" min="1">
                                        @error('duree_minutes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paramètres de notation -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Paramètres de notation</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="classe_id" class="form-label">Classe <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id" required>
                                            <option value="">Sélectionner une classe</option>
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

                                    <div class="form-group">
                                        <label for="matiere_id">Matière <span class="text-danger">*</span></label>
                                        <select id="matiere_id" name="matiere_id" class="form-control select2 @error('matiere_id') is-invalid @enderror" required>
                                            <option value="">Sélectionner une matière</option>
                                            @foreach($matieres as $matiere)
                                                <option value="{{ $matiere->id }}" {{ (old('matiere_id', $matiere_id) == $matiere->id) ? 'selected' : '' }}>
                                                    {{ $matiere->nom ?? $matiere->name ?? 'Matière ' . $matiere->id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('matiere_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="coefficient" class="form-label">Coefficient <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('coefficient') is-invalid @enderror" id="coefficient" name="coefficient" value="{{ old('coefficient', 1) }}" step="0.1" min="0.1" required>
                                        @error('coefficient')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="bareme" class="form-label">Barème <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('bareme') is-invalid @enderror" id="bareme" name="bareme" value="{{ old('bareme', 20) }}" step="0.1" min="1" required>
                                        @error('bareme')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Nombre de points total pour cette évaluation (généralement 20).</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description et options supplémentaires -->
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Description et options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_published">Publier immédiatement</label>
                                        <small class="form-text text-muted d-block">Une évaluation publiée est visible par les enseignants et permet la saisie des notes.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons de soumission -->
                        <div class="col-12 text-end">
                            <button type="reset" class="btn btn-secondary me-2">
                                <i class="fas fa-undo me-1"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
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
            $matiereSelect.empty().append('<option value="">Sélectionner une matière</option>');

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

            // Log pour le débogage
            console.log('Chargement des matières pour la classe ID:', classeId);
            console.log('Matière initiale:', initialMatiereId);

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

                    // On error, try the fallback endpoint
                    $.ajax({
                        url: `/esbtp/api/classes/${classeId}/matieres`,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log('Fallback API response:', data);
                            updateMatiereSelect(data);
                        },
                        error: function(xhr) {
                            console.error('Fallback also failed:', xhr);
                            // Load static matieres as last resort
                            resetMatiereSelect();
                            console.log('Using static matieres as fallback');
                        }
                    });
                }
            });
        }

        function updateMatiereSelect(data) {
            const $matiereSelect = $('#matiere_id');
            $matiereSelect.empty().append('<option value="">Sélectionner une matière</option>');

            if (Array.isArray(data) && data.length > 0) {
                console.log('Updating matiere select with', data.length, 'items');
                data.forEach(function(item) {
                    const id = item.id;
                    const name = item.nom || item.name || ('Matière ' + id);
                    const selected = (id == initialMatiereId) ? 'selected' : '';
                    $matiereSelect.append(`<option value="${id}" ${selected}>${name}</option>`);

                    // Log pour le débogage
                    if (id == initialMatiereId) {
                        console.log('Matière correspondante trouvée:', name);
                    }
                });
            } else {
                console.warn('No data received or invalid format');
            }

            $matiereSelect.prop('disabled', false).trigger('change');
        }

        // If we have both classe_id and initial matiere_id, trigger the load
        const initialClasseId = $('#classe_id').val();
        if (initialClasseId && initialMatiereId) {
            loadMatieres(initialClasseId);
        } else if (initialMatiereId) {
            // If we only have matiere_id but no classe_id, use the static data
            resetMatiereSelect();
        }
    });
</script>
@endsection
