@extends('layouts.app')

@section('title', 'Créer une annonce - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <!-- En-tête de page avec style moderne -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="font-weight-bold text-primary mb-1">
                                <i class="fas fa-plus-circle me-2"></i>Créer une nouvelle annonce
                            </h4>
                            <p class="text-muted mb-0 small">Remplissez le formulaire ci-dessous pour créer une nouvelle annonce.</p>
                        </div>
                        <a href="{{ route('esbtp.annonces.index') }}" class="btn btn-outline-primary rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-lg shadow-sm mb-4" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-exclamation-triangle fs-3"></i>
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

                    <form action="{{ route('esbtp.annonces.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
            <!-- Informations générales - Colonne principale -->
            <div class="col-lg-8">
                <!-- Carte d'informations générales avec style moderne -->
                <div class="card shadow-sm border-0 rounded-lg mb-4 hover-card">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Informations générales
                        </h5>
                                    </div>
                    <div class="card-body py-4">
                        <div class="mb-4">
                                            <label for="titre" class="form-label">Titre de l'annonce <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-heading text-primary"></i>
                                </span>
                                <input type="text" class="form-control border-start-0 ps-0 @error('titre') is-invalid @enderror"
                                    id="titre" name="titre" value="{{ old('titre') }}" placeholder="Saisissez un titre clair et concis" required>
                            </div>
                                            @error('titre')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                            <div class="form-text text-muted small">Le titre doit être court et descriptif (max 255 caractères).</div>
                                        </div>

                        <div class="mb-4">
                                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-align-left text-primary"></i>
                                </span>
                                <textarea class="form-control @error('contenu') is-invalid @enderror"
                                    id="contenu" name="contenu" rows="8"
                                    placeholder="Détaillez votre annonce ici..." required>{{ old('contenu') }}</textarea>
                            </div>
                                            @error('contenu')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                            <label for="piece_jointe" class="form-label d-flex align-items-center">
                                <i class="fas fa-paperclip me-2 text-primary"></i>
                                Pièce jointe (optionnel)
                            </label>
                            <div class="input-group">
                                            <input type="file" class="form-control @error('piece_jointe') is-invalid @enderror" id="piece_jointe" name="piece_jointe">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-upload"></i>
                                </span>
                            </div>
                                            @error('piece_jointe')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                            <div class="form-text text-muted small">Formats acceptés: PDF, Word, Excel, Images (max 5MB)</div>
                                    </div>
                                </div>
                            </div>

                <!-- Section des destinataires -->
                <div class="card shadow-sm border-0 rounded-lg mb-4 hover-card">
                    <div class="card-header bg-white py-3 border-bottom border-light">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Destinataires
                        </h5>
                                    </div>
                    <div class="card-body py-4">
                        <div class="mb-4">
                            <label class="form-label mb-3">Type de destinataires <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="type" id="type_globale"
                                        value="general" {{ old('type', 'general') == 'general' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_globale">
                                        <i class="fas fa-globe me-1 text-primary"></i>
                                        Tous les étudiants
                                    </label>
                                            </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="type" id="type_classe"
                                        value="classe" {{ old('type') == 'classe' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_classe">
                                        <i class="fas fa-chalkboard me-1 text-success"></i>
                                        Classes spécifiques
                                    </label>
                                        </div>
                                <div class="form-check custom-radio">
                                    <input class="form-check-input" type="radio" name="type" id="type_etudiant"
                                        value="etudiant" {{ old('type') == 'etudiant' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="type_etudiant">
                                        <i class="fas fa-user-graduate me-1 text-info"></i>
                                        Étudiants spécifiques
                                    </label>
                                                </div>
                                            </div>
                                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Sélection par classe -->
                        <div id="classes_container" class="mb-4 bg-light p-3 rounded-3" style="display: none;">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-filter me-2"></i>
                                Filtrer les classes
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4 mb-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-sitemap text-primary"></i>
                                        </span>
                                        <select class="form-select shadow-none" id="filiere_filter">
                                                        <option value="">Toutes les filières</option>
                                                        @foreach($filieres as $filiere)
                                                            <option value="{{ $filiere->id }}">{{ $filiere->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-layer-group text-primary"></i>
                                        </span>
                                        <select class="form-select shadow-none" id="niveau_filter">
                                                        <option value="">Tous les niveaux</option>
                                                        @foreach($niveaux as $niveau)
                                                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100" id="select_all_classes">
                                        <i class="fas fa-check-double me-1"></i>
                                        Sélectionner toutes les classes visibles
                                    </button>
                                                </div>
                                            </div>

                            <label for="classes" class="form-label">Classes destinataires <span class="text-danger">*</span></label>
                            <select class="form-select select2-multiple @error('classes') is-invalid @enderror"
                                id="classes" name="classes[]" multiple style="width: 100%;" data-placeholder="Sélectionnez une ou plusieurs classes">
                                                @foreach($classes as $classe)
                                                    <option value="{{ $classe->id }}"
                                                        data-filiere="{{ $classe->filiere_id }}"
                                                        data-niveau="{{ $classe->niveau_id }}"
                                                        {{ (old('classes') && in_array($classe->id, old('classes'))) ? 'selected' : '' }}>
                                                        {{ $classe->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('classes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Sélection par étudiant -->
                        <div id="etudiants_container" class="mb-4 bg-light p-3 rounded-3" style="display: none;">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-filter me-2"></i>
                                Filtrer les étudiants
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-chalkboard text-primary"></i>
                                        </span>
                                        <select class="form-select shadow-none" id="classe_etudiant_filter">
                                                        <option value="">Toutes les classes</option>
                                                        @foreach($classes as $classe)
                                                            <option value="{{ $classe->id }}">{{ $classe->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100" id="select_all_etudiants">
                                        <i class="fas fa-check-double me-1"></i>
                                        Sélectionner tous les étudiants visibles
                                    </button>
                                                </div>
                                            </div>

                            <label for="etudiants" class="form-label">Étudiants destinataires <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-user-graduate text-primary"></i>
                                </span>
                                <select class="form-select select2-multiple @error('etudiants') is-invalid @enderror"
                                    id="etudiants" name="etudiants[]" multiple style="width: 100%;" data-placeholder="Sélectionnez un ou plusieurs étudiants">
                                                @foreach($etudiants as $etudiant)
                                                    <option value="{{ $etudiant->id }}"
                                            data-classe="{{ $etudiant->classe_active ? $etudiant->classe_active->id : '' }}"
                                                        {{ (old('etudiants') && in_array($etudiant->id, old('etudiants'))) ? 'selected' : '' }}>
                                                        {{ $etudiant->matricule }} - {{ $etudiant->nom }} {{ $etudiant->prenoms }}
                                                    </option>
                                                @endforeach
                                            </select>
                            </div>
                                            @error('etudiants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                            <div class="form-text text-muted small mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Les notifications seront envoyées à tous les étudiants sélectionnés, qu'ils soient connectés ou non.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

            <!-- Colonne latérale pour les options -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 80px; z-index: 1000;">
                    <!-- Carte des options de publication -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4 hover-card">
                        <div class="card-header bg-white py-3 border-bottom border-light">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-cog me-2 text-primary"></i>
                                Options de publication
                            </h5>
                        </div>
                        <div class="card-body py-4">
                            <div class="mb-4">
                                <label for="status" class="form-label">Statut de publication <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-toggle-on text-primary"></i>
                                    </span>
                                    <select name="is_published" id="status" class="form-select @error('is_published') is-invalid @enderror" required>
                                        <option value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>Brouillon</option>
                                        <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>Publiée</option>
                                    </select>
                                </div>
                                @error('is_published')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted small">Les annonces en brouillon ne sont pas visibles par les destinataires.</div>
                            </div>

                            <div class="mb-4" id="date-publication-container" style="display: none;">
                                <label for="date_publication" class="form-label">Date de publication</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <input type="datetime-local" class="form-control @error('date_publication') is-invalid @enderror"
                                        id="date_publication" name="date_publication"
                                        value="{{ old('date_publication', now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                @error('date_publication')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted small">Si non spécifié, la date actuelle sera utilisée.</div>
                            </div>

                            <div class="mb-4">
                                <label for="date_expiration" class="form-label">Date d'expiration <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-hourglass-end text-primary"></i>
                                    </span>
                                    <input type="datetime-local" class="form-control @error('date_expiration') is-invalid @enderror"
                                        id="date_expiration" name="date_expiration"
                                        value="{{ old('date_expiration', now()->addMonths(1)->format('Y-m-d\TH:i')) }}">
                                </div>
                                @error('date_expiration')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted small">Après cette date, l'annonce ne sera plus visible pour les destinataires.</div>
                            </div>

                            <div class="mb-3">
                                <label for="priorite" class="form-label">Niveau d'urgence</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-exclamation-circle text-primary"></i>
                                    </span>
                                    <select class="form-select @error('priorite') is-invalid @enderror" id="priorite" name="priorite">
                                        <option value="0" {{ old('priorite') == '0' ? 'selected' : '' }}>Normale</option>
                                        <option value="1" {{ old('priorite') == '1' ? 'selected' : '' }}>Importante</option>
                                        <option value="2" {{ old('priorite') == '2' ? 'selected' : '' }}>Urgente</option>
                                    </select>
                                </div>
                                @error('priorite')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted small">
                                    Les annonces urgentes seront mises en évidence pour les destinataires.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte d'actions -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4 hover-card">
                        <div class="card-body p-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-2">
                                    <i class="fas fa-save me-2"></i>Enregistrer l'annonce
                            </button>
                                <button type="reset" class="btn btn-light py-2">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser le formulaire
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('styles')
<style>
    /* Styles pour les cartes */
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        transform: translateY(-2px);
    }

    /* Amélioration des form-controls */
    .form-control, .form-select {
        padding: 0.6rem 0.75rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--esbtp-green);
        box-shadow: 0 0 0 0.15rem rgba(1, 99, 47, 0.15);
    }

    /* Style pour Select2 */
    .select2-container--default .select2-selection--multiple {
        border-color: #dee2e6;
        border-radius: 0.375rem;
        min-height: 40px;
        padding: 2px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--esbtp-green);
        box-shadow: 0 0 0 0.15rem rgba(1, 99, 47, 0.15);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: var(--esbtp-light-green);
        border-color: var(--esbtp-green);
        color: var(--esbtp-green);
        font-size: 0.85rem;
        border-radius: 50rem;
        padding: 2px 8px;
        margin: 3px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: var(--esbtp-green);
        margin-right: 5px;
        border-right: none;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: rgba(1, 99, 47, 0.2);
        color: var(--esbtp-green);
    }

    /* Style pour les radios personnalisés */
    .custom-radio {
        padding: 10px 15px;
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        margin-right: 10px;
        transition: all 0.2s ease;
    }
    .custom-radio:hover {
        background-color: rgba(1, 99, 47, 0.05);
        border-color: var(--esbtp-green);
    }
    .custom-radio .form-check-input:checked ~ .form-check-label {
        color: var(--esbtp-green);
        font-weight: 500;
    }

    /* Style pour les étiquettes */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    /* Style pour les zones de formulaire spécifiques */
    #classes_container, #etudiants_container {
        transition: all 0.3s ease;
    }

    /* Style pour l'alerte */
    .alert-danger {
        border-left: 4px solid #842029;
    }

    /* Media queries pour la responsivité */
    @media (max-width: 991.98px) {
        .sticky-top {
            position: relative;
            top: 0 !important;
        }
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Configuration avancée de Select2
        $('.select2-multiple').select2({
            theme: 'bootstrap-5',
            placeholder: "Sélectionner des options",
            allowClear: true,
            closeOnSelect: false,
            width: '100%'
        });

        // Gestion de l'affichage du champ de date de publication
        $('#status').change(function() {
            if ($(this).val() === 'scheduled') {
                $('#date-publication-container').slideDown(300);
            } else {
                $('#date-publication-container').slideUp(300);
            }
        }).trigger('change');

        // Animation pour l'affichage des conteneurs de destinataires
        $('input[name="type"]').change(function() {
            const selectedType = $('input[name="type"]:checked').val();

            $('#classes_container, #etudiants_container').slideUp(300);

            if (selectedType === 'classe') {
                setTimeout(() => {
                    $('#classes_container').slideDown(300);
                }, 300);
            } else if (selectedType === 'etudiant') {
                setTimeout(() => {
                    $('#etudiants_container').slideDown(300);
                }, 300);
            }
        }).trigger('change');

        // Filtrage amélioré des classes
        $('#filiere_filter, #niveau_filter').change(function() {
            const filiereId = $('#filiere_filter').val();
            const niveauId = $('#niveau_filter').val();

            $('#classes option').each(function() {
                const classeFiliereId = $(this).data('filiere');
                const classeNiveauId = $(this).data('niveau');

                let show = true;

                if (filiereId && classeFiliereId != filiereId) {
                    show = false;
                }

                if (niveauId && classeNiveauId != niveauId) {
                    show = false;
                }

                $(this).prop('disabled', !show);

                if (!show && $(this).is(':selected')) {
                    $(this).prop('selected', false);
                }
            });

            $('#classes').trigger('change');
        });

        // Filtrage amélioré des étudiants avec meilleure gestion des valeurs vides
        $('#classe_etudiant_filter').change(function() {
            const classeId = $(this).val();
            let visibleCount = 0;

            $('#etudiants option').each(function() {
                const etudiantClasseId = $(this).data('classe');

                let show = true;

                // Si un filtre de classe est sélectionné, n'afficher que les étudiants de cette classe
                if (classeId && etudiantClasseId !== classeId) {
                    show = false;
                }

                $(this).prop('disabled', !show);

                if (!show && $(this).is(':selected')) {
                    $(this).prop('selected', false);
                }

                if (show) {
                    visibleCount++;
                }
            });

            // Afficher un message informatif sur le nombre d'étudiants disponibles après filtrage
            const infoMessage = visibleCount > 0
                ? `${visibleCount} étudiant(s) disponible(s)`
                : "Aucun étudiant disponible avec ce filtre";

            // Mettre à jour ou créer un élément d'information
            if ($('#etudiants-info').length) {
                $('#etudiants-info').text(infoMessage);
            } else {
                $('<div id="etudiants-info" class="text-muted small mt-2 mb-2">' + infoMessage + '</div>').insertBefore('#etudiants');
            }

            $('#etudiants').trigger('change');
        });

        // Sélection améliorée de toutes les classes
        $('#select_all_classes').click(function() {
            $('#classes option:not(:disabled)').prop('selected', true);
            $('#classes').trigger('change');

            // Ajouter un effet visuel
            $(this).addClass('btn-success').removeClass('btn-outline-primary');
            setTimeout(() => {
                $(this).addClass('btn-outline-primary').removeClass('btn-success');
            }, 1000);
        });

        // Sélection améliorée de tous les étudiants
        $('#select_all_etudiants').click(function() {
            $('#etudiants option:not(:disabled)').prop('selected', true);
            $('#etudiants').trigger('change');

            // Ajouter un effet visuel
            $(this).addClass('btn-success').removeClass('btn-outline-primary');
            setTimeout(() => {
                $(this).addClass('btn-outline-primary').removeClass('btn-success');
            }, 1000);
        });

        // Prévisualisation de la notification de niveau d'urgence
        $('#priorite').change(function() {
            const priorite = $(this).val();
            let bgColor = 'var(--esbtp-light-green)';
            let textColor = 'var(--esbtp-green)';

            if (priorite == 1) {
                bgColor = 'var(--esbtp-light-orange)';
                textColor = 'var(--esbtp-orange)';
            } else if (priorite == 2) {
                bgColor = '#f8d7da';
                textColor = '#842029';
            }

            $(this).css({
                'background-color': bgColor,
                'color': textColor,
                'border-color': textColor
            });

            // Revenir à la normale après 1.5 secondes
            setTimeout(() => {
                $(this).css({
                    'background-color': '',
                    'color': '',
                    'border-color': ''
                });
            }, 1500);
        });
    });
</script>
@endpush
