@extends('layouts.esbtp')

@section('title', 'Modifier une séance - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier une séance de cours</h5>
                    <a href="{{ route('esbtp.emploi-temps.show', $emploiTemps->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à l'emploi du temps
                    </a>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="border-start border-primary ps-3">
                            <h6 class="text-primary">Classe</h6>
                            <p class="mb-1">{{ $classe->name }}</p>
                            <p class="mb-1 small text-muted">{{ $classe->filiere->name }} - {{ $classe->niveau->name }}</p>
                        </div>
                    </div>

                    <form action="{{ route('esbtp.seances-cours.update', $seancesCour->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="matiere_id" class="form-label">Matière <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="matiere_id" name="matiere_id" required>
                                        <option value="">Sélectionner une matière</option>
                                        @foreach($matieres as $matiere)
                                            <option value="{{ $matiere->id }}" {{ old('matiere_id', $seancesCour->matiere_id) == $matiere->id ? 'selected' : '' }}>
                                                {{ $matiere->name }} ({{ $matiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('matiere_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="enseignant_id" class="form-label">Enseignant <span class="text-danger">*</span></label>
                                    <select class="form-select select2" id="enseignant_id" name="enseignant_id" required>
                                        <option value="">Sélectionner un enseignant</option>
                                        @foreach($enseignants as $enseignant)
                                            <option value="{{ $enseignant->id }}" {{ old('enseignant_id', $seancesCour->enseignant_id) == $enseignant->id ? 'selected' : '' }}>
                                                {{ $enseignant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('enseignant_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jour_semaine" class="form-label">Jour <span class="text-danger">*</span></label>
                                    <select class="form-select" id="jour_semaine" name="jour_semaine" required>
                                        <option value="">Sélectionner un jour</option>
                                        @foreach($joursSemaine as $key => $jour)
                                            <option value="{{ $key }}" {{ old('jour_semaine', $seancesCour->jour_semaine) == $key ? 'selected' : '' }}>
                                                {{ $jour }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jour_semaine')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="heure_debut" class="form-label">Heure de début <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="heure_debut" name="heure_debut" value="{{ old('heure_debut', $seancesCour->heure_debut) }}" required>
                                    @error('heure_debut')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="heure_fin" class="form-label">Heure de fin <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="heure_fin" name="heure_fin" value="{{ old('heure_fin', $seancesCour->heure_fin) }}" required>
                                    @error('heure_fin')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="salle" class="form-label">Salle <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="salle" name="salle" value="{{ old('salle', $seancesCour->salle) }}" required>
                                    @error('salle')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_seance" class="form-label">Type de séance <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type_seance" name="type_seance" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="cours" {{ old('type_seance', $seancesCour->type_seance) == 'cours' ? 'selected' : '' }}>Cours magistral</option>
                                        <option value="td" {{ old('type_seance', $seancesCour->type_seance) == 'td' ? 'selected' : '' }}>Travaux dirigés</option>
                                        <option value="tp" {{ old('type_seance', $seancesCour->type_seance) == 'tp' ? 'selected' : '' }}>Travaux pratiques</option>
                                        <option value="examen" {{ old('type_seance', $seancesCour->type_seance) == 'examen' ? 'selected' : '' }}>Examen</option>
                                        <option value="autre" {{ old('type_seance', $seancesCour->type_seance) == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type_seance')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $seancesCour->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Activer cette séance
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3">{{ old('commentaire', $seancesCour->commentaire) }}</textarea>
                            @error('commentaire')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-eraser me-1"></i>Réinitialiser
                            </button>
                            <div>
                                <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-1"></i>Supprimer
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Enregistrer les modifications
                                </button>
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
                <p>Êtes-vous sûr de vouloir supprimer cette séance de cours ?</p>
                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.seances-cours.destroy', $seancesCour->id) }}" method="POST">
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
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
        
        // Vérification de l'heure de fin > heure de début
        $('#heure_fin, #heure_debut').on('change', function() {
            const heureDebut = $('#heure_debut').val();
            const heureFin = $('#heure_fin').val();
            
            if (heureDebut && heureFin && heureDebut >= heureFin) {
                alert("L'heure de fin doit être postérieure à l'heure de début");
                $('#heure_fin').val('{{ $seancesCour->heure_fin }}');
            }
        });
    });
</script>
@endsection 