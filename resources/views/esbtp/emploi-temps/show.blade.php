@extends('layouts.app')

@section('title', 'Emploi du temps - ' . (is_object($emploiTemps) && is_object($emploiTemps->classe) ? $emploiTemps->classe->name : 'Non défini') . ' - ESBTP-yAKRO')

@section('styles')
<style>
    .timetable-container {
        overflow-x: auto;
    }

    .timetable {
        min-width: 900px;
    }

    .timetable th, .timetable td {
        min-width: 150px;
        height: 60px;
        position: relative;
    }

    .time-column {
        width: 80px;
        font-weight: bold;
        background-color: #f8f9fa;
    }

    .session-cell {
        padding: 5px;
        border-radius: 4px;
        font-size: 0.85rem;
        color: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Styles pour les séances qui durent plus d'une heure */
    .session-long {
        position: relative;
        z-index: 10;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: scale(1.02);
        transition: transform 0.2s;
    }

    .session-long:hover {
        transform: scale(1.05);
        z-index: 20;
    }

    .session-cours {
        background-color: #3498db;
    }

    .session-td {
        background-color: #2ecc71;
    }

    .session-tp {
        background-color: #9b59b6;
    }

    .session-examen {
        background-color: #e74c3c;
    }

    .session-autre {
        background-color: #f39c12;
    }

    /* Nouveaux styles pour les pauses et déjeuners */
    .session-pause {
        background-color: #95a5a6;
    }

    .session-dejeuner {
        background-color: #e67e22;
    }

    .session-info {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .session-matiere {
        font-weight: bold;
        font-size: 0.9rem;
    }

    .session-enseignant {
        font-size: 0.8rem;
        opacity: 0.9;
    }

    .session-details {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .session-actions {
        position: absolute;
        top: 5px;
        right: 5px;
        display: none;
    }

    .session-cell:hover .session-actions {
        display: block;
    }

    .session-inactive {
        opacity: 0.6;
    }

    .btn-add-session {
        border: 2px dashed #dee2e6;
        background-color: rgba(0,0,0,0.02);
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        transition: all 0.2s;
    }

    .btn-add-session:hover {
        background-color: rgba(0,0,0,0.05);
        color: #343a40;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 15px;
    }

    .legend-color {
        width: 15px;
        height: 15px;
        border-radius: 3px;
        margin-right: 5px;
    }

    .seance-list-item {
        border-left: 4px solid #3498db;
    }

    .seance-list-item.td {
        border-left-color: #2ecc71;
    }

    .seance-list-item.tp {
        border-left-color: #9b59b6;
    }

    .seance-list-item.examen {
        border-left-color: #e74c3c;
    }

    .seance-list-item.autre {
        border-left-color: #f39c12;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Emploi du temps : {{ $emploiTemps->titre ?? 'Non défini' }}</h5>
                    <div>
                        <a href="{{ route('esbtp.emploi-temps.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('esbtp.emploi-temps.export-pdf', ['emploi_temp' => $emploiTemps->id]) }}" class="btn btn-danger me-2" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i>Générer PDF
                            </a>
                            <a href="{{ route('esbtp.emploi-temps.edit', ['emploi_temp' => $emploiTemps->id]) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>Modifier
                            </a>
                            <a href="{{ route('esbtp.seances-cours.create', ['emploi_temps_id' => $emploiTemps->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Ajouter une séance
                            </a>
                        </div>
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

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Attention</h5>
                            <p>{{ session('warning') }}</p>
                            @if (session('show_force_delete'))
                                <hr>
                                <div class="d-flex justify-content-end">
                                    @if(auth()->user()->hasRole('superAdmin') && auth()->user()->can('delete_timetables'))
                                    <form action="{{ route('esbtp.emploi-temps.destroy', ['emploi_temp' => $emploiTemps->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="force_delete" value="1">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-1"></i>Confirmer la suppression forcée
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border-start border-primary ps-3">
                                <h6 class="text-primary">Informations sur l'emploi du temps</h6>
                                <p class="mb-1"><strong>Classe :</strong> {{ is_object($emploiTemps) && is_object($emploiTemps->classe) ? $emploiTemps->classe->name : 'Non définie' }}</p>
                                <p class="mb-1"><strong>Filière :</strong> {{ is_object($emploiTemps) && is_object($emploiTemps->classe) && is_object($emploiTemps->classe->filiere) ? $emploiTemps->classe->filiere->name : 'Non définie' }}</p>
                                <p class="mb-1"><strong>Niveau :</strong> {{ is_object($emploiTemps) && is_object($emploiTemps->classe) && is_object($emploiTemps->classe->niveau) ? $emploiTemps->classe->niveau->name : 'Non défini' }}</p>
                                <p class="mb-1"><strong>Année universitaire :</strong> {{ is_object($emploiTemps) && is_object($emploiTemps->annee) ? $emploiTemps->annee->name : 'Non définie' }}</p>
                                <p class="mb-1">
                                    <strong>Période :</strong>
                                    @if(isset($emploiTemps->semestre) && $emploiTemps->semestre == 'Semestre 1')
                                        Semestre 1
                                    @elseif(isset($emploiTemps->semestre) && $emploiTemps->semestre == 'Semestre 2')
                                        Semestre 2
                                    @else
                                        Année complète
                                    @endif
                                </p>
                                <p class="mb-1">
                                    <strong>Statut :</strong>
                                    @if(isset($emploiTemps->is_active) && $emploiTemps->is_active)
                                        <span class="badge bg-success">Actif</span>
                                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire'))
                                        <form action="{{ route('esbtp.emploi-temps.update', ['emploi_temp' => $emploiTemps->id]) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="0">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet emploi du temps ?')">
                                                <i class="fas fa-toggle-off me-1"></i>Désactiver
                                            </button>
                                        </form>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire'))
                                        <form action="{{ route('esbtp.emploi-temps.update', ['emploi_temp' => $emploiTemps->id]) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Êtes-vous sûr de vouloir activer cet emploi du temps ? Cela désactivera tous les autres emplois du temps pour cette classe.')">
                                                <i class="fas fa-toggle-on me-1"></i>Activer
                                            </button>
                                        </form>
                                        @endif
                                    @endif
                                    @if(isset($emploiTemps->is_current) && $emploiTemps->is_current)
                                        <span class="badge bg-info ms-1">Courant</span>
                                    @else
                                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire'))
                                        <form action="{{ route('esbtp.emploi-temps.set-current', ['id' => $emploiTemps->id]) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info" onclick="return confirm('Êtes-vous sûr de vouloir définir cet emploi du temps comme courant ? Cela désactivera tous les autres emplois du temps pour cette classe.')">
                                                <i class="fas fa-calendar-check me-1"></i>Définir comme courant
                                            </button>
                                        </form>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border-start border-info ps-3">
                                <h6 class="text-info">Statistiques des séances</h6>
                                <p class="mb-1"><strong>Nombre total de séances :</strong> {{ is_object($emploiTemps) && is_object($emploiTemps->seances) ? $emploiTemps->seances->count() : '0' }}</p>
                                <p class="mb-1">
                                    <strong>Types de séances :</strong>
                                    <span class="badge bg-secondary">{{ is_object($emploiTemps) && is_object($emploiTemps->seances) ? $emploiTemps->seances->where('type_seance', 'cours')->count() : '0' }} cours</span>
                                    <span class="badge bg-secondary">{{ is_object($emploiTemps) && is_object($emploiTemps->seances) ? $emploiTemps->seances->where('type_seance', 'td')->count() : '0' }} TD</span>
                                    <span class="badge bg-secondary">{{ is_object($emploiTemps) && is_object($emploiTemps->seances) ? $emploiTemps->seances->where('type_seance', 'tp')->count() : '0' }} TP</span>
                                    <span class="badge bg-secondary">{{ is_object($emploiTemps) && is_object($emploiTemps->seances) ? $emploiTemps->seances->where('type_seance', 'examen')->count() : '0' }} examens</span>
                                </p>
                                <p class="mb-1"><strong>Séances actives :</strong> {{ is_object($emploiTemps) && is_object($emploiTemps->seances) ? $emploiTemps->seances->where('is_active', 1)->count() : '0' }}</p>
                                <p class="mb-1"><strong>Séances par matière :</strong></p>
                                <div style="max-height: 100px; overflow-y: auto;">
                                    @if(isset($matiereStats) && is_array($matiereStats))
                                        @foreach($matiereStats as $matiere => $count)
                                            <small>{{ $matiere }}: {{ $count }} séance(s)</small><br>
                                        @endforeach
                                    @else
                                        <small>Aucune donnée disponible</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6>Légende :</h6>
                        <div class="d-flex flex-wrap">
                            <div class="legend-item">
                                <div class="legend-color session-cours"></div>
                                <small>Cours magistral</small>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color session-td"></div>
                                <small>Travaux dirigés</small>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color session-tp"></div>
                                <small>Travaux pratiques</small>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color session-examen"></div>
                                <small>Examen</small>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color session-autre"></div>
                                <small>Autre</small>
                            </div>
                            <!-- Nouvelles légendes pour les pauses et déjeuners -->
                            <div class="legend-item">
                                <div class="legend-color session-pause"></div>
                                <small>Récréation</small>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color session-dejeuner"></div>
                                <small>Pause déjeuner</small>
                            </div>
                        </div>
                    </div>

                    <div class="timetable-container mb-4">
                        <table class="table table-bordered timetable">
                            <thead>
                                <tr>
                                    <th class="text-center time-column">Heure</th>
                                    <th class="text-center">Lundi</th>
                                    <th class="text-center">Mardi</th>
                                    <th class="text-center">Mercredi</th>
                                    <th class="text-center">Jeudi</th>
                                    <th class="text-center">Vendredi</th>
                                    <th class="text-center">Samedi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                // Définir les créneaux horaires
                                $timeSlots = isset($timeSlots) && is_array($timeSlots) ? $timeSlots : [];

                                // Créer une grille pour suivre les cellules occupées par des rowspans
                                $occupiedCells = [];
                                foreach ($days as $day) {
                                    foreach ($timeSlots as $slotIndex => $slot) {
                                        $occupiedCells[$day][$slotIndex] = false;
                                    }
                                }

                                // Pré-traiter les séances pour déterminer les rowspans
                                $seancesWithRowspans = [];
                                if (isset($emploiTemps) && $emploiTemps->seances) {
                                    foreach ($emploiTemps->seances as $seance) {
                                        $jour = $seance->jour;
                                        $heureDebut = $seance->heure_debut->format('H:i');
                                        $heureFin = $seance->heure_fin->format('H:i');

                                        // Trouver l'index du créneau de début
                                        $startSlotIndex = array_search($heureDebut, $timeSlots);
                                        if ($startSlotIndex === false) continue;

                                        // Calculer combien de créneaux cette séance occupe
                                        $endSlotIndex = null;
                                        foreach ($timeSlots as $index => $slot) {
                                            // Check if this is the last slot
                                            $nextSlotIndex = $index + 1;
                                            $nextSlot = isset($timeSlots[$nextSlotIndex]) ? $timeSlots[$nextSlotIndex] : null;

                                            // If this is the last slot or the end time is before the next slot starts
                                            if ($nextSlot === null || $heureFin <= $nextSlot) {
                                                if ($index >= $startSlotIndex) {
                                                    $endSlotIndex = $index;
                                                    break; // Found the ending slot, no need to continue
                                                }
                                            }
                                        }

                                        if ($endSlotIndex === null) $endSlotIndex = $startSlotIndex;

                                        // Calculer le rowspan
                                        $rowspan = $endSlotIndex - $startSlotIndex + 1;
                                        if ($rowspan < 1) $rowspan = 1;

                                        // Stocker les informations
                                        $seancesWithRowspans[] = [
                                            'seance' => $seance,
                                            'jour' => $jour,
                                            'startSlotIndex' => $startSlotIndex,
                                            'endSlotIndex' => $endSlotIndex,
                                            'rowspan' => $rowspan
                                        ];

                                        // Marquer les cellules comme occupées
                                        for ($i = $startSlotIndex; $i <= $endSlotIndex; $i++) {
                                            $occupiedCells[$jour][$i] = true;
                                        }
                                    }
                                }
                                @endphp

                                @foreach($timeSlots as $slotIndex => $timeSlot)
                                <tr>
                                    <td class="text-center time-column">{{ $timeSlot }}</td>
                                    @foreach($days as $day)
                                        @php
                                        // Vérifier si cette cellule est occupée par un rowspan d'une ligne précédente
                                        $cellOccupied = $occupiedCells[$day][$slotIndex];

                                        // Trouver la séance à afficher dans cette cellule (s'il y en a une)
                                        $seanceToDisplay = null;
                                        $rowspan = 1;

                                        foreach ($seancesWithRowspans as $seanceData) {
                                            if ($seanceData['jour'] == $day && $seanceData['startSlotIndex'] == $slotIndex) {
                                                $seanceToDisplay = $seanceData['seance'];
                                                $rowspan = $seanceData['rowspan'];
                                                break;
                                            }
                                        }
                                        @endphp

                                        @if($seanceToDisplay && $cellOccupied)

                                        <td class="align-middle" rowspan="{{ $rowspan }}">
                                            <div class="session-cell session-{{ $seanceToDisplay->type_seance }} {{ $seanceToDisplay->is_active ? '' : 'session-inactive' }}"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="@if(in_array($seanceToDisplay->type_seance, ['pause', 'dejeuner']))
                                                            {{ $seanceToDisplay->type_seance === 'pause' ? 'Récréation' : 'Pause déjeuner' }} | {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                                                        @else
                                                            {{ is_object($seanceToDisplay->matiere) ? $seanceToDisplay->matiere->name : 'Matière non définie' }} | {{ $seanceToDisplay->enseignantName }} | {{ $seanceToDisplay->salle ?? 'Salle non définie' }} | {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                                                        @endif">
                                                
                                                @if(in_array($seanceToDisplay->type_seance, ['pause', 'dejeuner']))
                                                    <div class="session-info session-matiere">
                                                        {{ $seanceToDisplay->type_seance === 'pause' ? 'Récréation' : 'Pause déjeuner' }}
                                                    </div>
                                                    <div class="session-info session-details">
                                                        {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                                                    </div>
                                                @else
                                                    <div class="session-info session-matiere">
                                                        {{ is_object($seanceToDisplay->matiere) ? $seanceToDisplay->matiere->name : 'Matière non définie' }}
                                                    </div>
                                                    <div class="session-info session-enseignant">
                                                        {{ $seanceToDisplay->enseignantName }}
                                                    </div>
                                                    <div class="session-info session-details">
                                                        {{ $seanceToDisplay->salle ?? 'Salle non définie' }} | {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                                                    </div>
                                                @endif

                                                <!-- actions... -->
                                            </div>
                                        </td>
                                            <!-- <td class="align-middle" rowspan="{{ $rowspan }}">
                                                <div class="session-cell session-{{ $seanceToDisplay->type_seance }} {{ $seanceToDisplay->is_active ? '' : 'session-inactive' }}"
                                                     data-bs-toggle="tooltip"
                                                     data-bs-placement="top"
                                                     title="{{ is_object($seanceToDisplay->matiere) ? $seanceToDisplay->matiere->name : 'Matière non définie' }} | {{ $seanceToDisplay->enseignantName }} | {{ $seanceToDisplay->salle ?? 'Salle non définie' }} | {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}">
                                                    <div class="session-info session-matiere">
                                                        {{ is_object($seanceToDisplay->matiere) ? $seanceToDisplay->matiere->name : 'Matière non définie' }}
                                                    </div>
                                                    <div class="session-info session-enseignant">
                                                        {{ $seanceToDisplay->enseignantName }}
                                                    </div>
                                                    <div class="session-info session-details">
                                                        {{ $seanceToDisplay->salle ?? 'Salle non définie' }} | {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                                                    </div>
                                                    <div class="session-actions">
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('esbtp.seances-cours.edit', $seanceToDisplay->id) }}" class="btn btn-sm btn-light">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <form action="{{ route('esbtp.seances-cours.destroy', $seanceToDisplay->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-light" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td> -->
                                        @elseif(!$cellOccupied)
                                            <td>
                                                <a href="{{ route('esbtp.seances-cours.create', ['emploi_temps_id' => $emploiTemps->id ?? 0, 'jour' => $day, 'heure_debut' => $timeSlot]) }}" class="btn-add-session">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Liste des séances</h6>
                            <div class="list-group">
                                @if(isset($emploiTemps) && is_object($emploiTemps) && is_object($emploiTemps->seances) && $emploiTemps->seances->count() > 0)
                                    @foreach($emploiTemps->seances->sortBy('jour')->sortBy('heure_debut') as $seance)
                                        <div class="list-group-item seance-list-item {{ $seance->type_seance }}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">
                                                    {{ is_object($seance->matiere) ? $seance->matiere->name : 'Matière non définie' }}
                                                    <span class="badge bg-secondary">{{ ucfirst($seance->type_seance) }}</span>
                                                </h6>
                                                <small>{{ $seance->jour }}, {{ $seance->heure_debut }} - {{ $seance->heure_fin }}</small>
                                            </div>
                                            <p class="mb-1">
                                                <strong>Enseignant :</strong> {{ $seance->enseignantName }} |
                                                <strong>Salle :</strong> {{ $seance->salle ?? 'Non définie' }}
                                            </p>
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('esbtp.seances-cours.edit', $seance->id) }}" class="btn btn-sm btn-warning me-2">
                                                    <i class="fas fa-edit me-1"></i>Modifier
                                                </a>
                                                <form action="{{ route('esbtp.seances-cours.destroy', $seance->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')">
                                                        <i class="fas fa-trash me-1"></i>Supprimer
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="list-group-item">
                                        <p class="mb-0 text-muted">Aucune séance n'a été ajoutée à cet emploi du temps.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
