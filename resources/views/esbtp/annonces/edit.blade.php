@extends('layouts.app')

@section('title', 'Modifier l\'annonce : ' . $annonce->titre . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier l'annonce</h5>
                    <div>
                        <a href="{{ route('esbtp.annonces.show', $annonce) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye me-1"></i>Voir l'annonce
                        </a>
                        <a href="{{ route('esbtp.annonces.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('esbtp.annonces.update', $annonce) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informations générales -->
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informations générales</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="titre" class="form-label">Titre de l'annonce <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $annonce->titre) }}" required>
                                            @error('titre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('contenu') is-invalid @enderror" id="contenu" name="contenu" rows="6" required>{{ old('contenu', $annonce->contenu) }}</textarea>
                                            @error('contenu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="piece_jointe" class="form-label">Pièce jointe</label>
                                            @if($annonce->piece_jointe)
                                                <div class="mb-2">
                                                    <strong>Pièce jointe actuelle:</strong>
                                                    <a href="{{ asset('storage/' . $annonce->piece_jointe) }}" target="_blank">
                                                        {{ basename($annonce->piece_jointe) }}
                                                    </a>
                                                    <div class="form-check mt-1">
                                                        <input class="form-check-input" type="checkbox" name="supprimer_piece_jointe" id="supprimer_piece_jointe" value="1">
                                                        <label class="form-check-label" for="supprimer_piece_jointe">
                                                            Supprimer la pièce jointe actuelle
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                            <input type="file" class="form-control @error('piece_jointe') is-invalid @enderror" id="piece_jointe" name="piece_jointe">
                                            <small class="text-muted">Laissez vide pour conserver la pièce jointe existante.</small>
                                            @error('piece_jointe')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Options de publication -->
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Options de publication</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="status">Statut de publication <span class="text-danger">*</span></label>
                                                <select name="is_published" id="status" class="form-select @error('is_published') is-invalid @enderror" required>
                                                    <option value="0" {{ old('is_published', $annonce->is_published ? '1' : '0') == '0' ? 'selected' : '' }}>Brouillon</option>
                                                    <option value="1" {{ old('is_published', $annonce->is_published ? '1' : '0') == '1' ? 'selected' : '' }}>Publiée</option>
                                                </select>
                                                @error('is_published')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3" id="date-publication-container" style="display: none;">
                                            <label for="date_publication" class="form-label">Date de publication</label>
                                            <input type="datetime-local" class="form-control @error('date_publication') is-invalid @enderror" id="date_publication" name="date_publication" value="{{ old('date_publication', $annonce->date_publication ? $annonce->date_publication->format('Y-m-d\TH:i') : '') }}">
                                            @error('date_publication')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_expiration" class="form-label">Date d'expiration (optionnel)</label>
                                            <input type="datetime-local" class="form-control @error('date_expiration') is-invalid @enderror" id="date_expiration" name="date_expiration" value="{{ old('date_expiration', $annonce->date_expiration ? $annonce->date_expiration->format('Y-m-d\TH:i') : '') }}">
                                            <small class="text-muted">Laissez vide pour ne pas définir de date d'expiration.</small>
                                            @error('date_expiration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="priorite" class="form-label">Urgence</label>
                                            <select class="form-select @error('priorite') is-invalid @enderror" id="priorite" name="priorite">
                                                <option value="0" {{ old('priorite', $annonce->priorite) == 0 ? 'selected' : '' }}>Normale</option>
                                                <option value="1" {{ old('priorite', $annonce->priorite) == 1 ? 'selected' : '' }}>Importante</option>
                                                <option value="2" {{ old('priorite', $annonce->priorite) == 2 ? 'selected' : '' }}>Urgente</option>
                                            </select>
                                            @error('priorite')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Type de destinataires -->
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Destinataires</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Type de destinataires <span class="text-danger">*</span></label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="type_globale" value="general" {{ old('type', $annonce->type) == 'general' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="type_globale">Tous les étudiants</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="type_classe" value="classe" {{ old('type', $annonce->type) == 'classe' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="type_classe">Classes spécifiques</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" id="type_etudiant" value="etudiant" {{ old('type', $annonce->type) == 'etudiant' ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="type_etudiant">Étudiants spécifiques</label>
                                                </div>
                                            </div>
                                            @error('type')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Sélection par classe -->
                                        <div id="classes_container" class="mb-3" style="display: none;">
                                            <label for="classes" class="form-label">Sélectionner des classes</label>
                                            <div class="row">
                                                <div class="col-md-3 mb-2">
                                                    <select class="form-select" id="filiere_filter">
                                                        <option value="">Toutes les filières</option>
                                                        @foreach($filieres as $filiere)
                                                            <option value="{{ $filiere->id }}">{{ $filiere->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <select class="form-select" id="niveau_filter">
                                                        <option value="">Tous les niveaux</option>
                                                        @foreach($niveaux as $niveau)
                                                            <option value="{{ $niveau->id }}">{{ $niveau->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <button type="button" class="btn btn-outline-primary" id="select_all_classes">Sélectionner tout</button>
                                                </div>
                                            </div>
                                            <select class="form-select select2-multiple @error('classes') is-invalid @enderror" id="classes" name="classes[]" multiple style="width: 100%;">
                                                @foreach($classes as $classe)
                                                    <option value="{{ $classe->id }}"
                                                        data-filiere="{{ $classe->filiere_id }}"
                                                        data-niveau="{{ $classe->niveau_id }}"
                                                        {{ (old('classes', $classesSelectionnees->pluck('id')->toArray()) && in_array($classe->id, old('classes', $classesSelectionnees->pluck('id')->toArray()))) ? 'selected' : '' }}>
                                                        {{ $classe->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('classes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Sélection par étudiant -->
                                        <div id="etudiants_container" class="mb-3" style="display: none;">
                                            <label for="etudiants" class="form-label">Sélectionner des étudiants</label>
                                            <div class="row">
                                                <div class="col-md-3 mb-2">
                                                    <select class="form-select" id="classe_etudiant_filter">
                                                        <option value="">Toutes les classes</option>
                                                        @foreach($classes as $classe)
                                                            <option value="{{ $classe->id }}">{{ $classe->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <button type="button" class="btn btn-outline-primary" id="select_all_etudiants">Sélectionner tout</button>
                                                </div>
                                            </div>
                                            <select class="form-select select2-multiple @error('etudiants') is-invalid @enderror" id="etudiants" name="etudiants[]" multiple style="width: 100%;">
                                                @foreach($etudiants as $etudiant)
                                                    <option value="{{ $etudiant->id }}"
                                                        data-classe="{{ $etudiant->classe_id }}"
                                                        {{ (old('etudiants', $etudiantsSelectionnes->pluck('id')->toArray()) && in_array($etudiant->id, old('etudiants', $etudiantsSelectionnes->pluck('id')->toArray()))) ? 'selected' : '' }}>
                                                        {{ $etudiant->matricule }} - {{ $etudiant->nom }} {{ $etudiant->prenoms }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('etudiants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete">
                                <i class="fas fa-trash me-1"></i>Supprimer cette annonce
                            </button>
                            <div>
                                <a href="{{ route('esbtp.annonces.show', $annonce) }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Formulaire caché pour la suppression -->
                    <form action="{{ route('esbtp.annonces.destroy', $annonce) }}" method="POST" id="delete-form" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmDelete" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette annonce ?</p>
                <p><strong>Titre:</strong> {{ $annonce->titre }}</p>
                <p class="text-danger"><strong>Attention:</strong> Cette action est irréversible et supprimera également tous les liens avec les destinataires.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-form').submit();">Supprimer définitivement</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialiser Select2 pour les listes déroulantes multiples
        $('.select2-multiple').select2({
            placeholder: 'Sélectionner des options',
            allowClear: true
        });

        // Gestion de l'affichage du champ de date de publication
        $('#status').change(function() {
            if ($(this).val() === 'scheduled') {
                $('#date-publication-container').show();
            } else {
                $('#date-publication-container').hide();
            }
        }).trigger('change');

        // Gestion de l'affichage des conteneurs de destinataires
        $('input[name="type"]').change(function() {
            const selectedType = $('input[name="type"]:checked').val();

            $('#classes_container, #etudiants_container').hide();

            if (selectedType === 'classe') {
                $('#classes_container').show();
            } else if (selectedType === 'etudiant') {
                $('#etudiants_container').show();
            }
        }).trigger('change');

        // Filtrage des classes
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

        // Filtrage des étudiants
        $('#classe_etudiant_filter').change(function() {
            const classeId = $(this).val();

            $('#etudiants option').each(function() {
                const etudiantClasseId = $(this).data('classe');

                let show = true;

                if (classeId && etudiantClasseId != classeId) {
                    show = false;
                }

                $(this).prop('disabled', !show);

                if (!show && $(this).is(':selected')) {
                    $(this).prop('selected', false);
                }
            });

            $('#etudiants').trigger('change');
        });

        // Sélection de toutes les classes
        $('#select_all_classes').click(function() {
            $('#classes option:not(:disabled)').prop('selected', true);
            $('#classes').trigger('change');
        });

        // Sélection de tous les étudiants
        $('#select_all_etudiants').click(function() {
            $('#etudiants option:not(:disabled)').prop('selected', true);
            $('#etudiants').trigger('change');
        });
    });
</script>
@endpush
