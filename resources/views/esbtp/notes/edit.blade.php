@extends('layouts.app')

@section('title', 'Modifier une note - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier la note</h5>
                    <div>
                        <a href="{{ route('esbtp.evaluations.show', $note->evaluation) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux détails de l'évaluation
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations sur l'évaluation</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Titre :</th>
                                                <td>{{ $note->evaluation->titre }}</td>
                                            </tr>
                                            <tr>
                                                <th>Type :</th>
                                                <td>{{ ucfirst($note->evaluation->type) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date :</th>
                                                <td>{{ date('d/m/Y', strtotime($note->evaluation->date_evaluation)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Classe :</th>
                                                <td>{{ $note->evaluation->classe->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Matière :</th>
                                                <td>{{ $note->evaluation->matiere->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Barème :</th>
                                                <td>{{ $note->evaluation->bareme }} points</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations sur l'étudiant</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Matricule :</th>
                                                <td>{{ $note->etudiant->matricule }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nom :</th>
                                                <td>{{ $note->etudiant->nom }}</td>
                                            </tr>
                                            <tr>
                                                <th>Prénom :</th>
                                                <td>{{ $note->etudiant->prenom }}</td>
                                            </tr>
                                            <tr>
                                                <th>Classe :</th>
                                                <td>{{ $note->etudiant->classe->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Statut :</th>
                                                <td>
                                                    @if($note->etudiant->active)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('esbtp.notes.update', $note) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Informations de la note</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="valeur" class="form-label">Note <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number"
                                                       class="form-control @error('valeur') is-invalid @enderror"
                                                       id="valeur"
                                                       name="valeur"
                                                       value="{{ old('valeur', $note->valeur) }}"
                                                       min="0"
                                                       max="{{ $note->evaluation->bareme }}"
                                                       step="0.25"
                                                       {{ old('absent', $note->absent) ? 'disabled' : '' }}>
                                                <span class="input-group-text">/ {{ $note->evaluation->bareme }}</span>
                                                @error('valeur')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">Note équivalente sur 20 : <span id="note_sur_20">{{ number_format(($note->valeur * 20) / $note->evaluation->bareme, 2) }}</span>/20</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="date_saisie" class="form-label">Date de saisie</label>
                                            <input type="date"
                                                   class="form-control @error('date_saisie') is-invalid @enderror"
                                                   id="date_saisie"
                                                   name="date_saisie"
                                                   value="{{ old('date_saisie', $note->created_at ? date('Y-m-d', strtotime($note->created_at)) : date('Y-m-d')) }}">
                                            @error('date_saisie')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               id="absent"
                                               name="absent"
                                               value="1"
                                               {{ old('absent', $note->absent) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="absent">L'étudiant était absent lors de l'évaluation</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="commentaire" class="form-label">Commentaire</label>
                                    <textarea class="form-control @error('commentaire') is-invalid @enderror"
                                              id="commentaire"
                                              name="commentaire"
                                              rows="3">{{ old('commentaire', $note->commentaire) }}</textarea>
                                    @error('commentaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i>Supprimer la note
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
                <p>Êtes-vous sûr de vouloir supprimer cette note ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et pourrait affecter les calculs de moyennes et les bulletins.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.notes.destroy', $note) }}" method="POST">
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
        // Initialize required state on page load
        const valeurInput = $('#valeur');
        const absentCheckbox = $('#absent');

        function updateRequiredState() {
            const isAbsent = absentCheckbox.is(':checked');
            console.log('Updating required state - Absent:', isAbsent);

            valeurInput.prop('required', !isAbsent);
            valeurInput.prop('disabled', isAbsent);

            console.log('Required state after update:', valeurInput.prop('required'));
            console.log('Disabled state after update:', valeurInput.prop('disabled'));

            if (isAbsent) {
                valeurInput.val('');
                $('#note_sur_20').text('--');
                // Remove validation error if present
                valeurInput.removeClass('is-invalid');
                valeurInput.next('.invalid-feedback').remove();
            }
        }

        // Initial state
        console.log('Setting initial state');
        updateRequiredState();

        // Calcul automatique de la note sur 20
        function updateNoteSur20() {
            const valeur = parseFloat($('#valeur').val()) || 0;
            const bareme = {{ $note->evaluation->bareme }};
            const noteSur20 = (valeur * 20) / bareme;
            $('#note_sur_20').text(noteSur20.toFixed(2));
        }

        $('#valeur').on('input', updateNoteSur20);

        // Gestion de la case à cocher "Absent"
        $('#absent').change(function() {
            console.log('Absent checkbox changed');
            updateRequiredState();
            if ($(this).is(':checked')) {
                $('#note_sur_20').text('--');
            } else {
                updateNoteSur20();
            }
        });

        // Reset button handler
        $('button[type="reset"]').click(function() {
            console.log('Form reset triggered');
            setTimeout(updateRequiredState, 0);
        });
    });
</script>
@endsection
