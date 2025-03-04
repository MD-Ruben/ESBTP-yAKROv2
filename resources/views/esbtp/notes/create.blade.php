@extends('layouts.app')

@section('title', 'Ajouter une note - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une nouvelle note</h5>
                    <div>
                        <a href="{{ route('esbtp.notes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste des notes
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

                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                        </div>
                    @endif

                    <form action="{{ route('esbtp.notes.store') }}" method="POST">
                        @csrf
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Informations sur l'évaluation</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="evaluation_id" class="form-label">Évaluation <span class="text-danger">*</span></label>
                                    <select class="form-select @error('evaluation_id') is-invalid @enderror" id="evaluation_id" name="evaluation_id" required>
                                        <option value="">Sélectionnez une évaluation</option>
                                        @foreach($evaluations as $evaluation)
                                            <option value="{{ $evaluation->id }}" data-bareme="{{ $evaluation->bareme }}" {{ old('evaluation_id') == $evaluation->id ? 'selected' : '' }}>
                                                {{ $evaluation->titre }} - {{ $evaluation->matiere->name }} - {{ $evaluation->classe->name }} ({{ date('d/m/Y', strtotime($evaluation->date_evaluation)) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('evaluation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="etudiant_id" class="form-label">Étudiant <span class="text-danger">*</span></label>
                                    <select class="form-select @error('etudiant_id') is-invalid @enderror" id="etudiant_id" name="etudiant_id" required>
                                        <option value="">Sélectionnez un étudiant</option>
                                        @foreach($etudiants as $etudiant)
                                            <option value="{{ $etudiant->id }}" {{ old('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                                {{ $etudiant->matricule }} - {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('etudiant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Informations de la note</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="valeur" class="form-label">Note <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" 
                                               class="form-control @error('valeur') is-invalid @enderror" 
                                               id="valeur" 
                                               name="valeur" 
                                               value="{{ old('valeur') }}" 
                                               min="0" 
                                               step="0.25"
                                               required
                                               {{ old('absent') ? 'disabled' : '' }}>
                                        <span class="input-group-text" id="bareme-display">/ --</span>
                                        @error('valeur')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Note équivalente sur 20 : <span id="note_sur_20">--</span>/20</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="absent" 
                                               name="absent" 
                                               value="1" 
                                               {{ old('absent') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="absent">L'étudiant était absent lors de l'évaluation</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="commentaire" class="form-label">Commentaire</label>
                                    <textarea class="form-control @error('commentaire') is-invalid @enderror" 
                                              id="commentaire" 
                                              name="commentaire" 
                                              rows="3">{{ old('commentaire') }}</textarea>
                                    @error('commentaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="reset" class="btn btn-secondary me-2">
                                    <i class="fas fa-undo me-1"></i>Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer la note
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
        // Mise à jour du barème lorsqu'on sélectionne une évaluation
        $('#evaluation_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            const bareme = selectedOption.data('bareme') || '--';
            $('#bareme-display').text('/ ' + bareme);
            
            // Réinitialiser la note sur 20
            updateNoteSur20();
        });
        
        // Calcul automatique de la note sur 20
        function updateNoteSur20() {
            const valeur = parseFloat($('#valeur').val()) || 0;
            const baremeText = $('#bareme-display').text().replace('/ ', '');
            
            if (baremeText === '--' || isNaN(parseFloat(baremeText))) {
                $('#note_sur_20').text('--');
                return;
            }
            
            const bareme = parseFloat(baremeText);
            const noteSur20 = (valeur * 20) / bareme;
            $('#note_sur_20').text(noteSur20.toFixed(2));
        }
        
        $('#valeur').on('input', updateNoteSur20);
        
        // Gestion de la case à cocher "Absent"
        $('#absent').change(function() {
            if ($(this).is(':checked')) {
                $('#valeur').prop('disabled', true).val('');
                $('#note_sur_20').text('0.00');
            } else {
                $('#valeur').prop('disabled', false);
                updateNoteSur20();
            }
        });
        
        // Filtrer les étudiants par classe lorsqu'on sélectionne une évaluation
        $('#evaluation_id').change(function() {
            // Cette fonctionnalité pourrait être améliorée avec une requête AJAX
            // pour charger uniquement les étudiants de la classe concernée
        });
    });
</script>
@endsection 