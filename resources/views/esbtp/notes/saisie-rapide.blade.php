@extends('layouts.app')

@section('title', 'Saisie des notes : ' . $evaluation->titre . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Saisie des notes : {{ $evaluation->titre }}</h5>
                    <div>
                        <a href="{{ route('esbtp.evaluations.show', $evaluation) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye me-1"></i>Voir l'évaluation
                        </a>
                        <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux évaluations
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Informations sur l'évaluation -->
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
                                                <td>{{ $evaluation->titre }}</td>
                                            </tr>
                                            <tr>
                                                <th>Type :</th>
                                                <td>{{ ucfirst($evaluation->type) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date :</th>
                                                <td>{{ date('d/m/Y', strtotime($evaluation->date_evaluation)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Classe :</th>
                                                <td>{{ $evaluation->classe->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Matière :</th>
                                                <td>{{ $evaluation->matiere->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Barème :</th>
                                                <td>{{ $evaluation->bareme }} points</td>
                                            </tr>
                                            <tr>
                                                <th>Coefficient :</th>
                                                <td>{{ $evaluation->coefficient }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Instructions de saisie</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Les notes doivent être comprises entre 0 et {{ $evaluation->bareme }}.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Utilisez le point (.) comme séparateur décimal.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Cochez la case "Absent" pour les étudiants qui n'ont pas participé à l'évaluation.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Les commentaires sont optionnels et limités à 255 caractères.
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            Cliquez sur "Enregistrer toutes les notes" pour sauvegarder.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de saisie des notes -->
                    <form action="{{ route('esbtp.notes.store-batch') }}" method="POST" id="notesForm">
                        @csrf
                        <input type="hidden" name="evaluation_id" value="{{ $evaluation->id }}">
                        
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Liste des étudiants ({{ $etudiants->count() }})</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="resetForm">
                                        <i class="fas fa-undo me-1"></i>Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-save me-1"></i>Enregistrer toutes les notes
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="10%">Matricule</th>
                                                <th width="25%">Nom et Prénom</th>
                                                <th width="15%">Note /{{ $evaluation->bareme }}</th>
                                                <th width="10%">Absent</th>
                                                <th width="35%">Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($etudiants as $index => $etudiant)
                                                @php
                                                    $note = $notes->where('etudiant_id', $etudiant->id)->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $etudiant->matricule }}</td>
                                                    <td>{{ $etudiant->nom }} {{ $etudiant->prenom }}</td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control note-input @error('notes.' . $etudiant->id . '.valeur') is-invalid @enderror" 
                                                               name="notes[{{ $etudiant->id }}][valeur]" 
                                                               value="{{ old('notes.' . $etudiant->id . '.valeur', $note ? $note->valeur : '') }}"
                                                               min="0" 
                                                               max="{{ $evaluation->bareme }}" 
                                                               step="0.25"
                                                               {{ $note && $note->absent ? 'disabled' : '' }}>
                                                        @error('notes.' . $etudiant->id . '.valeur')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input absent-checkbox" 
                                                                   type="checkbox" 
                                                                   name="notes[{{ $etudiant->id }}][absent]" 
                                                                   value="1" 
                                                                   {{ old('notes.' . $etudiant->id . '.absent', $note && $note->absent ? '1' : '') ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               name="notes[{{ $etudiant->id }}][commentaire]" 
                                                               value="{{ old('notes.' . $etudiant->id . '.commentaire', $note ? $note->commentaire : '') }}"
                                                               maxlength="255">
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Aucun étudiant inscrit dans cette classe.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer toutes les notes
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
        // Gestion des cases à cocher "Absent"
        $('.absent-checkbox').change(function() {
            const noteInput = $(this).closest('tr').find('.note-input');
            
            if ($(this).is(':checked')) {
                // Si "Absent" est coché, désactiver le champ de note et effacer sa valeur
                noteInput.prop('disabled', true).val('');
            } else {
                // Sinon, réactiver le champ de note
                noteInput.prop('disabled', false);
            }
        });
        
        // Réinitialiser le formulaire
        $('#resetForm').click(function() {
            if (confirm('Voulez-vous vraiment réinitialiser le formulaire ? Toutes les modifications non enregistrées seront perdues.')) {
                $('.note-input').val('').prop('disabled', false);
                $('.absent-checkbox').prop('checked', false);
                $('input[name$="[commentaire]"]').val('');
            }
        });
        
        // Validation du formulaire
        $('#notesForm').submit(function() {
            let valid = true;
            
            $('.note-input:not(:disabled)').each(function() {
                const value = $(this).val();
                if (value !== '' && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > {{ $evaluation->bareme }})) {
                    alert('Veuillez saisir des notes valides entre 0 et {{ $evaluation->bareme }}.');
                    $(this).focus();
                    valid = false;
                    return false;
                }
            });
            
            return valid;
        });
    });
</script>
@endsection 