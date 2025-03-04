@extends('layouts.app')

@section('title', 'Modifier une séance de cours - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier une séance de cours</h5>
                    <a href="{{ route('esbtp.emplois-temps.show', $seancesCour->emploi_temps_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à l'emploi du temps
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

                    <div class="mb-4 border-start border-primary ps-3">
                        <h6 class="text-primary">Informations sur l'emploi du temps</h6>
                        <p class="mb-1"><strong>Classe :</strong> {{ $seancesCour->emploiTemps->classe->name }}</p>
                        <p class="mb-1"><strong>Filière :</strong> {{ $seancesCour->emploiTemps->classe->filiere->name }}</p>
                        <p class="mb-1"><strong>Niveau :</strong> {{ $seancesCour->emploiTemps->classe->niveau->name }}</p>
                        <p class="mb-1"><strong>Année universitaire :</strong> {{ $seancesCour->emploiTemps->annee->name }}</p>
                    </div>

                    <form action="{{ route('esbtp.seances-cours.update', $seancesCour->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="matiere_id" class="form-label">Matière *</label>
                                    <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                                        <option value="">Sélectionner une matière</option>
                                        @foreach($matieres as $matiere)
                                            <option value="{{ $matiere->id }}" {{ (old('matiere_id', $seancesCour->matiere_id) == $matiere->id) ? 'selected' : '' }}>
                                                {{ $matiere->code }} - {{ $matiere->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('matiere_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="enseignant_id" class="form-label">Enseignant *</label>
                                    <select class="form-select @error('enseignant_id') is-invalid @enderror" id="enseignant_id" name="enseignant_id" required>
                                        <option value="">Sélectionner un enseignant</option>
                                        @foreach($enseignants as $enseignant)
                                            <option value="{{ $enseignant->id }}" {{ (old('enseignant_id', $seancesCour->enseignant_id) == $enseignant->id) ? 'selected' : '' }}>
                                                {{ $enseignant->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('enseignant_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jour_semaine" class="form-label">Jour de la semaine *</label>
                                    <select class="form-select @error('jour_semaine') is-invalid @enderror" id="jour_semaine" name="jour_semaine" required>
                                        <option value="">Sélectionner un jour</option>
                                        <option value="1" {{ (old('jour_semaine', $seancesCour->jour_semaine) == 1) ? 'selected' : '' }}>Lundi</option>
                                        <option value="2" {{ (old('jour_semaine', $seancesCour->jour_semaine) == 2) ? 'selected' : '' }}>Mardi</option>
                                        <option value="3" {{ (old('jour_semaine', $seancesCour->jour_semaine) == 3) ? 'selected' : '' }}>Mercredi</option>
                                        <option value="4" {{ (old('jour_semaine', $seancesCour->jour_semaine) == 4) ? 'selected' : '' }}>Jeudi</option>
                                        <option value="5" {{ (old('jour_semaine', $seancesCour->jour_semaine) == 5) ? 'selected' : '' }}>Vendredi</option>
                                        <option value="6" {{ (old('jour_semaine', $seancesCour->jour_semaine) == 6) ? 'selected' : '' }}>Samedi</option>
                                    </select>
                                    @error('jour_semaine')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="heure_debut" class="form-label">Heure de début *</label>
                                    <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" id="heure_debut" name="heure_debut" value="{{ old('heure_debut', $seancesCour->heure_debut) }}" required>
                                    @error('heure_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="heure_fin" class="form-label">Heure de fin *</label>
                                    <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" id="heure_fin" name="heure_fin" value="{{ old('heure_fin', $seancesCour->heure_fin) }}" required>
                                    @error('heure_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_seance" class="form-label">Type de séance *</label>
                                    <select class="form-select @error('type_seance') is-invalid @enderror" id="type_seance" name="type_seance" required>
                                        <option value="cours" {{ (old('type_seance', $seancesCour->type_seance) == 'cours') ? 'selected' : '' }}>Cours magistral</option>
                                        <option value="td" {{ (old('type_seance', $seancesCour->type_seance) == 'td') ? 'selected' : '' }}>Travaux dirigés</option>
                                        <option value="tp" {{ (old('type_seance', $seancesCour->type_seance) == 'tp') ? 'selected' : '' }}>Travaux pratiques</option>
                                        <option value="examen" {{ (old('type_seance', $seancesCour->type_seance) == 'examen') ? 'selected' : '' }}>Examen</option>
                                        <option value="autre" {{ (old('type_seance', $seancesCour->type_seance) == 'autre') ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type_seance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="salle" class="form-label">Salle *</label>
                                    <input type="text" class="form-control @error('salle') is-invalid @enderror" id="salle" name="salle" value="{{ old('salle', $seancesCour->salle) }}" required>
                                    @error('salle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ (old('is_active', $seancesCour->is_active) == 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Séance active
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="3">{{ old('commentaire', $seancesCour->commentaire) }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Information</h6>
                            <p class="mb-0">Le système vérifiera automatiquement les conflits d'horaires pour cette séance (même enseignant, même salle ou même classe au même moment). Si des conflits sont détectés, vous devrez les résoudre avant de pouvoir enregistrer cette séance.</p>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i>Supprimer la séance
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
                <p>Êtes-vous sûr de vouloir supprimer cette séance de cours ?</p>
                <p class="fw-bold">
                    @php
                        $jours = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                    @endphp
                    {{ $jours[$seancesCour->jour_semaine] ?? 'Jour inconnu' }} de {{ $seancesCour->heure_debut }} à {{ $seancesCour->heure_fin }} - 
                    {{ $seancesCour->matiere->name }}
                </p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Attention :</strong> Cette action est irréversible.
                </p>
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
        // Amélioration des listes déroulantes avec Select2
        $('#matiere_id, #enseignant_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez un élément'
        });
        
        // Validation des horaires
        $('#heure_fin').on('change', function() {
            const heureDebut = $('#heure_debut').val();
            const heureFin = $(this).val();
            
            if (heureDebut && heureFin && heureDebut >= heureFin) {
                alert("L'heure de fin doit être postérieure à l'heure de début.");
                $(this).val('{{ $seancesCour->heure_fin }}');
            }
        });
        
        $('#heure_debut').on('change', function() {
            const heureDebut = $(this).val();
            const heureFin = $('#heure_fin').val();
            
            if (heureDebut && heureFin && heureDebut >= heureFin) {
                alert("L'heure de début doit être antérieure à l'heure de fin.");
                $(this).val('{{ $seancesCour->heure_debut }}');
            }
        });
    });
</script>
@endsection 