@extends('layouts.app')

@section('title', 'Ajouter une séance de cours - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une séance de cours</h5>
                    @if(isset($emploiTemps))
                        <a href="{{ route('esbtp.emploi-temps.show', $emploiTemps->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à l'emploi du temps
                        </a>
                    @else
                        <a href="{{ route('esbtp.emploi-temps.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux emplois du temps
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

                    @if(isset($classe))
                        <div class="mb-4 border-start border-primary ps-3">
                            <h6 class="text-primary">Informations sur l'emploi du temps</h6>
                            <p class="mb-1"><strong>Classe :</strong> {{ $classe->name ?? 'Non définie' }}</p>
                            <p class="mb-1"><strong>Filière :</strong> {{ $classe->filiere->name ?? 'Non définie' }}</p>
                            <p class="mb-1"><strong>Niveau :</strong> {{ $classe->niveau->name ?? 'Non défini' }}</p>
                            <p class="mb-1"><strong>Année universitaire :</strong> {{ $classe->annee->name ?? 'Non définie' }}</p>
                        </div>
                    @endif

                    <form action="{{ route('esbtp.seances-cours.store') }}" method="POST">
                        @csrf

                        @if(isset($emploiTemps) && $emploiTemps)
                            <input type="hidden" name="emploi_temps_id" value="{{ $emploiTemps->id }}">
                        @else
                            <div class="form-group mb-3">
                                <label for="emploi_temps_id" class="form-label">Emploi du temps *</label>
                                <select class="form-select @error('emploi_temps_id') is-invalid @enderror" id="emploi_temps_id" name="emploi_temps_id" required>
                                    <option value="">Sélectionner un emploi du temps</option>
                                    @foreach($emploisTemps as $et)
                                        <option value="{{ $et->id }}" {{ (old('emploi_temps_id') == $et->id || (isset($request_params) && $request_params->emploi_temps_id == $et->id)) ? 'selected' : '' }}>
                                            {{ $et->titre ?? 'Sans titre' }} - {{ $et->classe->name ?? 'Classe non définie' }} ({{ $et->classe->filiere->name ?? 'Filière non définie' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('emploi_temps_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_seance" class="form-label fw-bold">Type de séance *</label>
                                    <select class="form-select form-select-lg @error('type_seance') is-invalid @enderror" id="type_seance" name="type_seance" required>
                                        <option value="">Sélectionner un type de séance</option>
                                        <option value="cours" {{ old('type_seance') == 'cours' ? 'selected' : '' }}>Cours magistral</option>
                                        <option value="td" {{ old('type_seance') == 'td' ? 'selected' : '' }}>Travaux dirigés</option>
                                        <option value="tp" {{ old('type_seance') == 'tp' ? 'selected' : '' }}>Travaux pratiques</option>
                                        <option value="examen" {{ old('type_seance') == 'examen' ? 'selected' : '' }}>Examen</option>
                                        <option value="pause" {{ old('type_seance') == 'pause' ? 'selected' : '' }}>Récréation</option>
                                        <option value="dejeuner" {{ old('type_seance') == 'dejeuner' ? 'selected' : '' }}>Pause déjeuner</option>
                                        <option value="autre" {{ old('type_seance') == 'autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('type_seance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="session-type-info" class="mb-3" style="display: none;"></div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jour" class="form-label">Jour *</label>
                                    <select class="form-select @error('jour') is-invalid @enderror" id="jour" name="jour" required>
                                        <option value="">Sélectionner un jour</option>
                                        @foreach($joursSemaine as $index => $jourName)
                                            <option value="{{ $index }}" {{ (old('jour', $jour) == $index) ? 'selected' : '' }}>
                                                {{ $jourName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jour')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="heure_debut" class="form-label">Heure de début *</label>
                                    <input type="time" class="form-control @error('heure_debut') is-invalid @enderror" id="heure_debut" name="heure_debut" value="{{ old('heure_debut', $heure_debut) }}" required>
                                    @error('heure_debut')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="heure_fin" class="form-label">Heure de fin *</label>
                                    <input type="time" class="form-control @error('heure_fin') is-invalid @enderror" id="heure_fin" name="heure_fin" value="{{ old('heure_fin') ?: ($heure_debut ? sprintf('%02d:00', (int)substr($heure_debut, 0, 2) + 1) : '') }}" required>
                                    @error('heure_fin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3" id="subject-teacher-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="matiere_id" class="form-label">Matière *</label>
                                    <select class="form-select @error('matiere_id') is-invalid @enderror" id="matiere_id" name="matiere_id" required>
                                        <option value="">Sélectionner une matière</option>
                                        @foreach($matieres as $matiere)
                                            <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
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
                                    <label for="enseignant" class="form-label">Enseignant</label>
                                    <input type="text" class="form-control @error('enseignant') is-invalid @enderror" id="enseignant" name="enseignant" value="{{ old('enseignant') }}" placeholder="Nom de l'enseignant">
                                    @error('enseignant')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6" id="classroom-col">
                                <div class="form-group">
                                    <label for="salle" class="form-label">Salle *</label>
                                    <input type="text" class="form-control @error('salle') is-invalid @enderror" id="salle" name="salle" value="{{ old('salle') }}" required>
                                    @error('salle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Séance active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="details" class="form-label">Détails</label>
                            <textarea class="form-control @error('details') is-invalid @enderror" id="details" name="details" rows="3">{{ old('details') }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Information</h6>
                            <p class="mb-0">Le système vérifiera automatiquement les conflits d'horaires pour cette séance (même enseignant, même salle ou même classe au même moment). Si des conflits sont détectés, vous devrez les résoudre avant de pouvoir enregistrer cette séance.</p>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
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
        console.log('Document ready - create form');

        // Amélioration des listes déroulantes avec Select2
        $('#matiere_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez une matière'
        });

        $('#emploi_temps_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez un emploi du temps'
        });

        // Validation des horaires
        $('#heure_fin').on('change', function() {
            const heureDebut = $('#heure_debut').val();
            const heureFin = $(this).val();

            if (heureDebut && heureFin && heureDebut >= heureFin) {
                alert("L'heure de fin doit être postérieure à l'heure de début.");
                $(this).val('');
            }
        });

        $('#heure_debut').on('change', function() {
            const heureDebut = $(this).val();
            const heureFin = $('#heure_fin').val();

            if (heureDebut && heureFin && heureDebut >= heureFin) {
                alert("L'heure de début doit être antérieure à l'heure de fin.");
                $(this).val('');
            }
        });

        // Function to toggle fields based on session type
        function toggleFieldsBySessionType() {
            console.log('Toggle function called');
            const sessionType = $('#type_seance').val();
            console.log('Session type:', sessionType);

            // If no session type is selected, show all fields
            if (!sessionType) {
                $('#subject-teacher-row').show();
                $('#classroom-col').show();
                $('#session-type-info').hide();
                $('#matiere_id').prop('required', true);
                $('#salle').prop('required', true);
                return;
            }

            const isBreak = sessionType === 'pause' || sessionType === 'dejeuner';
            console.log('Is break:', isBreak);

            // Info message area
            const $infoMessage = $('#session-type-info');

            // Toggle visibility with animation
            if (isBreak) {
                console.log('Hiding fields for break');
                $('#subject-teacher-row').slideUp(300);

                // Make hidden fields not required
                $('#matiere_id').prop('required', false);
                $('#enseignant').prop('required', false);
                $('#salle').prop('required', false);
                $('#classroom-col').hide();

                // For recreation, hide classroom too, but keep it for lunch break
                if (sessionType === 'pause') {
                    console.log('Hiding classroom for recreation');
                    $('#classroom-col').slideUp(300);
                    //$('#salle').prop('required', false);

                    // Update info message
                    // $infoMessage.html('<div class="alert alert-info mt-2"><i class="fas fa-info-circle me-2"></i><strong>Récréation :</strong> Seuls le jour et les horaires sont requis.</div>').slideDown(300);
                } else {
                    console.log('Showing classroom for lunch break');
                    //$('#classroom-col').slideDown(300);
                    //$('#salle').prop('required', true);

                    // Update info message
                    // $infoMessage.html('<div class="alert alert-info mt-2"><i class="fas fa-info-circle me-2"></i><strong>Pause déjeuner :</strong> La salle est requise mais pas la matière ni l\'enseignant.</div>').slideDown(300);
                }
            } else {
                console.log('Showing all fields');
                // Show all fields and make them required again
                $('#subject-teacher-row').slideDown(300);
                $('#classroom-col').slideDown(300);

                $('#matiere_id').prop('required', true);
                $('#enseignant').prop('required', false); // Keep enseignant optional
                $('#salle').prop('required', true);

                // Update info message based on session type
                let infoText = '';
                switch(sessionType) {
                    case 'cours':
                        infoText = '<strong>Cours magistral :</strong> Tous les champs sont disponibles.';
                        break;
                    case 'td':
                        infoText = '<strong>Travaux dirigés :</strong> Tous les champs sont disponibles.';
                        break;
                    case 'tp':
                        infoText = '<strong>Travaux pratiques :</strong> Tous les champs sont disponibles.';
                        break;
                    case 'examen':
                        infoText = '<strong>Examen :</strong> Tous les champs sont disponibles.';
                        break;
                    case 'autre':
                        infoText = '<strong>Autre type de séance :</strong> Tous les champs sont disponibles.';
                        break;
                }

                if (infoText) {
                    $infoMessage.html('<div class="alert alert-info mt-2"><i class="fas fa-info-circle me-2"></i>' + infoText + '</div>').slideDown(300);
                } else {
                    $infoMessage.slideUp(300);
                }
            }
        }

        // Call the function on page load
        console.log('Page loaded, calling toggle function');
        toggleFieldsBySessionType();

        // Bind the change event directly
        $('#type_seance').on('change', function() {
            console.log('Dropdown changed to: ' + $(this).val());
            toggleFieldsBySessionType();
        });
    });
</script>
@endsection