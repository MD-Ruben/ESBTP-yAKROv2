@extends('esbtp.evaluations.form')

@section('title', 'Modifier l\'évaluation : ' . $evaluation->titre . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'évaluation : {{ $evaluation->titre }}</h5>
                    <div>
                        <a href="{{ route('esbtp.evaluations.show', $evaluation) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye me-1"></i>Voir les détails
                        </a>
                        <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('esbtp.evaluations.update', $evaluation) }}" method="POST">
                        @csrf
                        @method('PUT')

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
                                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $evaluation->titre) }}" required>
                                            @error('titre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="type" class="form-label">Type d'évaluation <span class="text-danger">*</span></label>
                                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                                <option value="">Sélectionner un type</option>
                                                <option value="examen" {{ old('type', $evaluation->type) == 'examen' ? 'selected' : '' }}>Examen</option>
                                                <option value="devoir" {{ old('type', $evaluation->type) == 'devoir' ? 'selected' : '' }}>Devoir</option>
                                                <option value="quiz" {{ old('type', $evaluation->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                                <option value="tp" {{ old('type', $evaluation->type) == 'tp' ? 'selected' : '' }}>TP</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_evaluation" class="form-label">Date de l'évaluation <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('date_evaluation') is-invalid @enderror" id="date_evaluation" name="date_evaluation" value="{{ old('date_evaluation', $evaluation->date_evaluation ? date('Y-m-d', strtotime($evaluation->date_evaluation)) : '') }}" required>
                                            @error('date_evaluation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="duree_minutes" class="form-label">Durée (en minutes)</label>
                                            <input type="number" class="form-control @error('duree_minutes') is-invalid @enderror" id="duree_minutes" name="duree_minutes" value="{{ old('duree_minutes', $evaluation->duree_minutes) }}" min="1">
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
                                                    <option value="{{ $classe->id }}" {{ old('classe_id', $evaluation->classe_id) == $classe->id ? 'selected' : '' }}>
                                                        {{ $classe->name }} ({{ $classe->filiere->name }} - {{ $classe->niveau->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('classe_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="matiere_id" class="form-label">Matière <span class="text-danger">*</span></label>
                                            <select class="form-select select2 @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                                                <option value="">Sélectionner une matière</option>
                                                <!-- Les matières seront chargées dynamiquement en fonction de la classe sélectionnée -->
                                            </select>
                                            @error('matiere_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="coefficient" class="form-label">Coefficient <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('coefficient') is-invalid @enderror" id="coefficient" name="coefficient" value="{{ old('coefficient', $evaluation->coefficient) }}" step="0.1" min="0.1" required>
                                            @error('coefficient')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="bareme" class="form-label">Barème <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('bareme') is-invalid @enderror" id="bareme" name="bareme" value="{{ old('bareme', $evaluation->bareme) }}" step="0.1" min="1" required>
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
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $evaluation->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $evaluation->is_published) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_published">Publier l'évaluation</label>
                                            <small class="form-text text-muted d-block">Une évaluation publiée est visible par les enseignants et permet la saisie des notes.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes existantes -->
                            @if($evaluation->notes->count() > 0)
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Notes existantes</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Cette évaluation a déjà <strong>{{ $evaluation->notes->count() }}</strong> notes enregistrées.
                                            La modification de certains paramètres (barème, coefficient) peut affecter les calculs des moyennes et des bulletins.
                                        </div>
                                        <a href="{{ route('esbtp.notes.saisie-rapide', $evaluation) }}" class="btn btn-primary">
                                            <i class="fas fa-pen me-1"></i>Accéder à la saisie des notes
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Boutons de soumission -->
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-body d-flex justify-content-between">
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                            <i class="fas fa-trash me-1"></i>Supprimer l'évaluation
                                        </button>
                                        <div>
                                            <button type="reset" class="btn btn-secondary me-2">
                                                <i class="fas fa-undo me-1"></i>Annuler les modifications
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette évaluation ?</p>

                @if($evaluation->notes->count() > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette évaluation a <strong>{{ $evaluation->notes->count() }}</strong> notes associées qui seront également supprimées.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.evaluations.destroy', $evaluation) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
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
            theme: 'bootstrap-5'
        });

        // Chargement des matières en fonction de la classe sélectionnée
        function loadMatieres(classeId) {
            if (classeId) {
                $.ajax({
                    url: '/api/classes/' + classeId + '/matieres',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#matiere_id').empty();
                        $('#matiere_id').append('<option value="">Sélectionner une matière</option>');
                        $.each(data, function(key, matiere) {
                            $('#matiere_id').append('<option value="' + matiere.id + '">' + matiere.name + '</option>');
                        });

                        // Présélectionner la matière si elle était déjà choisie
                        const selectedMatiere = "{{ old('matiere_id', $evaluation->matiere_id) }}";
                        if (selectedMatiere) {
                            $('#matiere_id').val(selectedMatiere).trigger('change');
                        }
                    },
                    error: function() {
                        alert('Erreur lors du chargement des matières.');
                    }
                });
            } else {
                $('#matiere_id').empty();
                $('#matiere_id').append('<option value="">Sélectionner une matière</option>');
            }
        }

        // Chargement initial des matières
        loadMatieres($('#classe_id').val());

        // Chargement des matières lors du changement de classe
        $('#classe_id').change(function() {
            loadMatieres($(this).val());
        });
    });
</script>
@endsection
