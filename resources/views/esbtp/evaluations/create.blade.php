@extends('layouts.app')

@section('title', 'Ajouter une évaluation - ESBTP-yAKRO')

@section('content')
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

                    <form action="{{ route('esbtp.evaluations.store') }}" method="POST">
                        @csrf
                        
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
                                                    <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                                        {{ $matiere->nom ?? $matiere->name ?? 'Matière ' . $matiere->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('matiere_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted mt-2">
                                                <button type="button" id="debug-load-all" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-bug"></i> Charger toutes les matières (debug)
                                                </button>
                                            </small>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Variable globale pour les matières
    let globalMatieres = [];
    
    // S'assurer que jQuery est disponible avant d'exécuter le code
    document.addEventListener('DOMContentLoaded', function() {
        // Attendre que jQuery soit complètement chargé
        function checkJquery() {
            if (window.jQuery) {
                // Charger les matières depuis le contrôleur
                globalMatieres = @json($matieresJson ?? []);
                console.log('DEBUG - Matières chargées depuis le contrôleur:', globalMatieres.length);
                
                // Puis initialiser Select2
                initializeSelect2();
            } else {
                console.log('Attente de jQuery...');
                setTimeout(checkJquery, 100);
            }
        }
        
        checkJquery();
        
        function initializeSelect2() {
            console.log('jQuery est disponible, initialisation de Select2...');
            
            // Initialisation de Select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Bouton de débogage pour charger toutes les matières
            $('#debug-load-all').on('click', function() {
                console.log('DEBUG - Bouton debug cliqué');
                loadAllMatieres();
            });
            
            // Chargement des matières en fonction de la classe sélectionnée
            $('#classe_id').on('change', function() {
                const classeId = $(this).val();
                console.log('DEBUG - Classe sélectionnée ID:', classeId);
                
                // Vider le dropdown des matières quand aucune classe n'est sélectionnée
                if (!classeId) {
                    $('#matiere_id').empty();
                    $('#matiere_id').append('<option value="">Sélectionner une matière</option>');
                    console.log('DEBUG - Aucune classe sélectionnée, dropdown des matières vidé');
                    return;
                }
                
                // Afficher un indicateur de chargement
                $('#matiere_id').empty();
                $('#matiere_id').append('<option value="">Chargement des matières...</option>');
                $('#matiere_id').prop('disabled', true);
                
                // Tenter de charger les matières avec les trois méthodes
                loadMatieres(classeId);
            });
            
            // Fonction pour charger les matières avec plusieurs tentatives
            function loadMatieres(classeId) {
                console.log('DEBUG - Tentative de chargement des matières pour la classe ID:', classeId);
                
                // Vider et désactiver le select des matières pendant le chargement
                $('#matiere_id').empty();
                $('#matiere_id').append('<option value="">Chargement des matières...</option>');
                $('#matiere_id').prop('disabled', true);
                
                // Première tentative - API standard
                $.ajax({
                    url: '/esbtp/api/classes/' + classeId + '/matieres',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        console.log('DEBUG - Succès API standard:', data);
                        handleMatieresData(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('DEBUG - Échec API standard:', status, error);
                        
                        // Deuxième tentative - Route fallback
                        $.ajax({
                            url: '/esbtp/api/classes/' + classeId + '/matieres',
                            type: 'GET',
                            dataType: 'json',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(data) {
                                console.log('DEBUG - Succès route fallback:', data);
                                handleMatieresData(data);
                            },
                            error: function(xhr, status, error) {
                                console.error('DEBUG - Échec route fallback:', status, error);
                                
                                // En cas d'échec des deux API, utiliser les matières globales
                                if (globalMatieres && globalMatieres.length > 0) {
                                    console.log('DEBUG - Utilisation des matières globales');
                                    handleMatieresData(globalMatieres);
                                } else {
                                    $('#matiere_id').empty();
                                    $('#matiere_id').append('<option value="">Sélectionner une matière</option>');
                                    $('#matiere_id').append('<option value="1">Matière Test</option>');
                                    $('#matiere_id').prop('disabled', false);
                                }
                            }
                        });
                    }
                });
            }
            
            // Fonction pour traiter les données de matières récupérées
            function handleMatieresData(data) {
                $('#matiere_id').empty();
                $('#matiere_id').prop('disabled', false);
                
                if (data && data.length > 0) {
                    $('#matiere_id').append('<option value="">Sélectionner une matière</option>');
                    
                    data.forEach(function(item) {
                        let id = item.id;
                        let name = item.name || item.nom || ('Matière ' + id);
                        if (item.code) {
                            name = item.code + ' - ' + name;
                        }
                        $('#matiere_id').append('<option value="' + id + '">' + name + '</option>');
                    });
                } else {
                    $('#matiere_id').append('<option value="" disabled>Aucune matière disponible</option>');
                }
                
                // Check if there's an old value to restore
                const selectedMatiere = "{{ old('matiere_id') }}";
                if (selectedMatiere) {
                    try {
                        // Try to set the value, but handle if it doesn't exist
                        const matExists = $('#matiere_id option[value="' + selectedMatiere + '"]').length > 0;
                        if (matExists) {
                            $('#matiere_id').val(selectedMatiere);
                        } else {
                            console.log('DEBUG - La matière sélectionnée ' + selectedMatiere + ' n\'existe pas dans la liste actuelle');
                        }
                    } catch (err) {
                        console.error('DEBUG - Erreur lors de la présélection de la matière:', err);
                    }
                }
                
                $('#matiere_id').trigger('change');
            }

            // Déclencher le changement si une classe est déjà sélectionnée (cas de validation avec erreurs)
            if ($('#classe_id').val()) {
                console.log('DEBUG - Classe déjà sélectionnée, déclenchement du changement');
                $('#classe_id').trigger('change');
            } else {
                // Si aucune classe n'est sélectionnée, charger toutes les matières disponibles
                console.log('DEBUG - Aucune classe sélectionnée, chargement de toutes les matières');
                loadAllMatieres();
            }

            // Fonction pour charger toutes les matières indépendamment de la classe
            function loadAllMatieres() {
                console.log('DEBUG - Chargement de toutes les matières');
                
                // Si des matières globales sont disponibles, les utiliser directement
                if (globalMatieres && globalMatieres.length > 0) {
                    console.log('DEBUG - Utilisation des matières globales dans loadAllMatieres');
                    handleMatieresData(globalMatieres);
                    return;
                }
                
                $('#matiere_id').empty();
                $('#matiere_id').append('<option value="">Chargement des matières...</option>');
                $('#matiere_id').prop('disabled', true);
                
                $.ajax({
                    url: '/esbtp/matieres/json',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        console.log('DEBUG - Réponse directe matières:', data);
                        handleMatieresData(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('DEBUG - Erreur chargement direct:', status, error);
                        
                        $('#matiere_id').empty();
                        $('#matiere_id').prop('disabled', false);
                        $('#matiere_id').append('<option value="">Sélectionner une matière</option>');
                        
                        // En dernier recours, ajouter une matière de test
                        $('#matiere_id').append('<option value="1">Matière Test</option>');
                        $('#matiere_id').trigger('change');
                    }
                });
            }
        }
    });
</script>
@endsection 