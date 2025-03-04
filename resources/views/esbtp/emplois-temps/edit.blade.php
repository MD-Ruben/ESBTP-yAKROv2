@extends('layouts.app')

@section('title', 'Modifier un emploi du temps - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier un emploi du temps</h5>
                    <div>
                        <a href="{{ route('esbtp.emplois-temps.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                        <a href="{{ route('esbtp.emplois-temps.show', $emploiTemps->id) }}" class="btn btn-info">
                            <i class="fas fa-eye me-1"></i>Voir l'emploi du temps
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

                    <form action="{{ route('esbtp.emplois-temps.update', $emploiTemps->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titre" class="form-label">Titre de l'emploi du temps *</label>
                                    <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $emploiTemps->titre) }}" required>
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="classe_id" class="form-label">Classe *</label>
                                    <select class="form-select @error('classe_id') is-invalid @enderror" id="classe_id" name="classe_id" required>
                                        <option value="">Sélectionner une classe</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" {{ (old('classe_id', $emploiTemps->classe_id) == $classe->id) ? 'selected' : '' }}>
                                                {{ $classe->name }} ({{ $classe->filiere->name }} - {{ $classe->niveau->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('classe_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="annee_universitaire_id" class="form-label">Année universitaire *</label>
                                    <select class="form-select @error('annee_universitaire_id') is-invalid @enderror" id="annee_universitaire_id" name="annee_universitaire_id" required>
                                        <option value="">Sélectionner une année universitaire</option>
                                        @foreach($annees as $annee)
                                            <option value="{{ $annee->id }}" {{ (old('annee_universitaire_id', $emploiTemps->annee_universitaire_id) == $annee->id) ? 'selected' : '' }}>
                                                {{ $annee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('annee_universitaire_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periode" class="form-label">Période *</label>
                                    <select class="form-select @error('periode') is-invalid @enderror" id="periode" name="periode" required>
                                        <option value="">Sélectionner une période</option>
                                        <option value="semestre1" {{ (old('periode', $emploiTemps->periode) == 'semestre1') ? 'selected' : '' }}>Semestre 1</option>
                                        <option value="semestre2" {{ (old('periode', $emploiTemps->periode) == 'semestre2') ? 'selected' : '' }}>Semestre 2</option>
                                        <option value="annee" {{ (old('periode', $emploiTemps->periode) == 'annee') ? 'selected' : '' }}>Année complète</option>
                                    </select>
                                    @error('periode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', $emploiTemps->date_debut ? $emploiTemps->date_debut->format('Y-m-d') : '') }}">
                                    @error('date_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', $emploiTemps->date_fin ? $emploiTemps->date_fin->format('Y-m-d') : '') }}">
                                    @error('date_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ (old('is_active', $emploiTemps->is_active) == 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Emploi du temps actif
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Un seul emploi du temps peut être actif par classe et par période. Si vous activez cet emploi du temps, les autres emplois du temps pour la même classe et la même période seront automatiquement désactivés.
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Information</h6>
                            <p class="mb-0">Cet emploi du temps contient actuellement <strong>{{ $emploiTemps->seances->count() }} séances</strong> de cours.</p>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i>Supprimer l'emploi du temps
                            </button>
                            <div>
                                <button type="reset" class="btn btn-secondary me-2">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet emploi du temps ?</p>
                <p><strong>Titre :</strong> {{ $emploiTemps->titre }}</p>
                <p><strong>Classe :</strong> {{ $emploiTemps->classe->name }}</p>
                <p><strong>Année universitaire :</strong> {{ $emploiTemps->annee->name }}</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Attention :</strong> Cette action supprimera également toutes les séances de cours associées à cet emploi du temps. Cette action est irréversible.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.emplois-temps.destroy', $emploiTemps->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Amélioration des listes déroulantes avec Select2
        $('#classe_id, #annee_universitaire_id, #periode').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez un élément'
        });
        
        // Validation des dates
        $('#date_fin').on('change', function() {
            var dateDebut = $('#date_debut').val();
            var dateFin = $(this).val();
            
            if (dateDebut && dateFin && dateDebut > dateFin) {
                alert("La date de fin doit être postérieure à la date de début.");
                $(this).val('{{ $emploiTemps->date_fin ? $emploiTemps->date_fin->format("Y-m-d") : "" }}');
            }
        });
        
        $('#date_debut').on('change', function() {
            var dateDebut = $(this).val();
            var dateFin = $('#date_fin').val();
            
            if (dateDebut && dateFin && dateDebut > dateFin) {
                alert("La date de début doit être antérieure à la date de fin.");
                $(this).val('{{ $emploiTemps->date_debut ? $emploiTemps->date_debut->format("Y-m-d") : "" }}');
            }
        });
    });
</script>
@endsection 