@extends('layouts.app')

@section('title', 'Modifier une séance de cours - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier une séance de cours</h5>
                    @if($emploiTemps)
                        <a href="{{ route('esbtp.emploi-temps.show', $seancesCour->emploi_temps_id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à l'emploi du temps
                        </a>
                    @else
                        <a href="{{ route('esbtp.seances-cours.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste des séances
                        </a>
                    @endif
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

                    <!-- Section de débogage -->
                    <div class="mb-4 border-start border-warning ps-3 bg-light p-3" id="debug-section">
                        <h6 class="text-warning">Informations de débogage</h6>
                        <p class="mb-1"><strong>ID de la séance :</strong> {{ $seancesCour->id }}</p>
                        <p class="mb-1"><strong>ID de l'emploi du temps :</strong> {{ $seancesCour->emploi_temps_id }}</p>
                        <p class="mb-1"><strong>Emploi du temps trouvé :</strong> {{ $emploiTemps ? 'Oui' : 'Non' }}</p>
                        @if($emploiTemps)
                            <p class="mb-1"><strong>ID de l'emploi du temps trouvé :</strong> {{ $emploiTemps->id }}</p>
                            <p class="mb-1"><strong>Soft deleted :</strong> {{ $emploiTemps->deleted_at ? 'Oui' : 'Non' }}</p>
                        @endif
                        <button type="button" id="check-emploi-temps" class="btn btn-warning btn-sm mt-2">
                            <i class="fas fa-search me-1"></i>Vérifier l'emploi du temps
                        </button>
                        <div id="check-result" class="mt-2"></div>
                    </div>

                    <div class="mb-4 border-start border-primary ps-3">
                        <h6 class="text-primary">Informations sur l'emploi du temps</h6>
                        @if($emploiTemps)
                            <p class="mb-1"><strong>Classe :</strong> {{ $emploiTemps->classe->name ?? 'Non définie' }}</p>
                            <p class="mb-1"><strong>Filière :</strong> {{ $emploiTemps->classe->filiere->name ?? 'Non définie' }}</p>
                            <p class="mb-1"><strong>Niveau :</strong> {{ $emploiTemps->classe->niveau->name ?? 'Non défini' }}</p>
                            <p class="mb-1"><strong>Année universitaire :</strong> {{ $emploiTemps->annee->name ?? 'Non définie' }}</p>
                        @else
                            <p class="mb-1 text-danger">Emploi du temps non disponible. Les informations seront mises à jour après l'enregistrement.</p>
                        @endif
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
                                    <label for="enseignant" class="form-label">Enseignant *</label>
                                    <input type="text" class="form-control @error('enseignant') is-invalid @enderror" id="enseignant" name="enseignant" value="{{ old('enseignant', $seancesCour->enseignant) }}" required placeholder="Nom de l'enseignant">
                                    @error('enseignant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jour" class="form-label">Jour *</label>
                                    <select class="form-select @error('jour') is-invalid @enderror" id="jour" name="jour" required>
                                        <option value="">Sélectionner un jour</option>
                                        <option value="1" {{ (old('jour', $seancesCour->jour) == 1) ? 'selected' : '' }}>Lundi</option>
                                        <option value="2" {{ (old('jour', $seancesCour->jour) == 2) ? 'selected' : '' }}>Mardi</option>
                                        <option value="3" {{ (old('jour', $seancesCour->jour) == 3) ? 'selected' : '' }}>Mercredi</option>
                                        <option value="4" {{ (old('jour', $seancesCour->jour) == 4) ? 'selected' : '' }}>Jeudi</option>
                                        <option value="5" {{ (old('jour', $seancesCour->jour) == 5) ? 'selected' : '' }}>Vendredi</option>
                                        <option value="6" {{ (old('jour', $seancesCour->jour) == 6) ? 'selected' : '' }}>Samedi</option>
                                    </select>
                                    @error('jour')
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
                                        <option value="pause" {{ (old('type_seance', $seancesCour->type_seance) == 'pause') ? 'selected' : '' }}>Récréation</option>
                                        <option value="dejeuner" {{ (old('type_seance', $seancesCour->type_seance) == 'dejeuner') ? 'selected' : '' }}>Pause déjeuner</option>
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
                            <label for="description" class="form-label">Détails</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $seancesCour->description) }}</textarea>
                            @error('description')
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
                        $jour = isset($jours[$seancesCour->jour]) ? $jours[$seancesCour->jour] : 'Jour inconnu';
                    @endphp
                    {{ $jour }} de {{ $seancesCour->heure_debut }} à {{ $seancesCour->heure_fin }} -
                    {{ $seancesCour->matiere->name ?? 'Matière inconnue' }}
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

@section('scripts')
<script>
    $(document).ready(function() {
        // Amélioration des listes déroulantes avec Select2
        $('#matiere_id').select2({
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

        // Afficher les informations de débogage dans la console
        console.log('Débogage séance de cours:', {
            seance_id: {{ $seancesCour->id }},
            emploi_temps_id: {{ $seancesCour->emploi_temps_id }},
            emploi_temps_trouve: {{ $emploiTemps ? 'true' : 'false' }},
            @if($emploiTemps)
            emploi_temps: {
                id: {{ $emploiTemps->id }},
                classe_id: {{ $emploiTemps->classe_id }},
                deleted_at: '{{ $emploiTemps->deleted_at }}',
            },
            @endif
        });

        // Fonction pour vérifier l'existence de l'emploi du temps
        $('#check-emploi-temps').on('click', function() {
            const emploiTempsId = {{ $seancesCour->emploi_temps_id }};
            const resultDiv = $('#check-result');

            resultDiv.html('<div class="spinner-border spinner-border-sm text-warning" role="status"><span class="visually-hidden">Chargement...</span></div> Vérification en cours...');

            // Vérifier l'existence de l'emploi du temps via une requête AJAX
            $.ajax({
                url: `/api/check-emploi-temps/${emploiTempsId}`,
                method: 'GET',
                success: function(data) {
                    console.log('Résultat de la vérification:', data);

                    if (data.exists) {
                        resultDiv.html(`<div class="alert alert-success mb-0">L'emploi du temps existe (ID: ${data.id})</div>`);
                    } else {
                        resultDiv.html(`<div class="alert alert-danger mb-0">L'emploi du temps n'existe pas</div>`);
                    }

                    // Afficher les détails complets
                    if (data.details) {
                        const detailsHtml = `
                            <div class="mt-2">
                                <strong>Détails:</strong>
                                <pre class="bg-light p-2 mt-1" style="font-size: 0.8rem;">${JSON.stringify(data.details, null, 2)}</pre>
                            </div>
                        `;
                        resultDiv.append(detailsHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la vérification:', error);
                    resultDiv.html(`<div class="alert alert-danger mb-0">Erreur lors de la vérification: ${error}</div>`);
                }
            });
        });
    });
</script>
@endsection
@endsection
