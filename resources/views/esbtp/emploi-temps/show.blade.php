@extends('layouts.app')

@section('title', 'Emploi du temps - ' . $emploiTemps->classe->name . ' - ESBTP-yAKRO')

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
    
    .session-info {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
                    <h5 class="mb-0">Emploi du temps : {{ $emploiTemps->titre }}</h5>
                    <div>
                        <a href="{{ route('esbtp.emplois-temps.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('esbtp.emplois-temps.edit', $emploiTemps->id) }}" class="btn btn-warning">
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

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="border-start border-primary ps-3">
                                <h6 class="text-primary">Informations sur l'emploi du temps</h6>
                                <p class="mb-1"><strong>Classe :</strong> {{ $emploiTemps->classe->name }}</p>
                                <p class="mb-1"><strong>Filière :</strong> {{ $emploiTemps->classe->filiere->name }}</p>
                                <p class="mb-1"><strong>Niveau :</strong> {{ $emploiTemps->classe->niveau->name }}</p>
                                <p class="mb-1"><strong>Année universitaire :</strong> {{ $emploiTemps->annee->name }}</p>
                                <p class="mb-1">
                                    <strong>Période :</strong> 
                                    @if($emploiTemps->periode == 'semestre1')
                                        Semestre 1
                                    @elseif($emploiTemps->periode == 'semestre2')
                                        Semestre 2
                                    @else
                                        Année complète
                                    @endif
                                </p>
                                <p class="mb-1">
                                    <strong>Statut :</strong>
                                    @if($emploiTemps->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="border-start border-info ps-3">
                                <h6 class="text-info">Statistiques des séances</h6>
                                <p class="mb-1"><strong>Nombre total de séances :</strong> {{ $emploiTemps->seances->count() }}</p>
                                <p class="mb-1">
                                    <strong>Types de séances :</strong>
                                    <span class="badge bg-secondary">{{ $emploiTemps->seances->where('type_seance', 'cours')->count() }} cours</span>
                                    <span class="badge bg-secondary">{{ $emploiTemps->seances->where('type_seance', 'td')->count() }} TD</span>
                                    <span class="badge bg-secondary">{{ $emploiTemps->seances->where('type_seance', 'tp')->count() }} TP</span>
                                    <span class="badge bg-secondary">{{ $emploiTemps->seances->where('type_seance', 'examen')->count() }} examens</span>
                                </p>
                                <p class="mb-1"><strong>Séances actives :</strong> {{ $emploiTemps->seances->where('is_active', 1)->count() }}</p>
                                <p class="mb-1"><strong>Séances par matière :</strong></p>
                                <div style="max-height: 100px; overflow-y: auto;">
                                    @foreach($matiereStats as $matiere => $count)
                                        <small>{{ $matiere }}: {{ $count }} séance(s)</small><br>
                                    @endforeach
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
                                @for($hour = 8; $hour <= 18; $hour++)
                                    <tr>
                                        <td class="text-center time-column">{{ sprintf('%02d:00', $hour) }}</td>
                                        @for($day = 1; $day <= 6; $day++)
                                            <td>
                                                @php
                                                    $sessionsAtTime = $seances->filter(function($seance) use ($day, $hour) {
                                                        $startHour = (int)substr($seance->heure_debut, 0, 2);
                                                        $endHour = (int)substr($seance->heure_fin, 0, 2);
                                                        return $seance->jour_semaine == $day && $startHour <= $hour && $endHour > $hour;
                                                    });
                                                @endphp
                                                
                                                @if($sessionsAtTime->count() > 0)
                                                    @foreach($sessionsAtTime as $seance)
                                                        <div class="session-cell session-{{ $seance->type_seance }} {{ $seance->is_active ? '' : 'session-inactive' }}">
                                                            <div class="session-info">
                                                                <strong>{{ $seance->matiere->name }}</strong>
                                                            </div>
                                                            <div class="session-info">
                                                                {{ $seance->heure_debut }} - {{ $seance->heure_fin }}
                                                            </div>
                                                            <div class="session-info">
                                                                {{ $seance->enseignant->name }}
                                                            </div>
                                                            <div class="session-info">
                                                                Salle: {{ $seance->salle }}
                                                            </div>
                                                            <div class="session-actions">
                                                                <a href="{{ route('esbtp.seances-cours.edit', $seance->id) }}" class="btn btn-sm btn-light">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <a href="{{ route('esbtp.seances-cours.create', ['emploi_temps_id' => $emploiTemps->id, 'jour' => $day, 'heure' => $hour]) }}" class="btn-add-session">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <h6 class="mt-4">Liste des séances de cours</h6>
                    <div class="row">
                        @forelse($seances as $seance)
                            <div class="col-md-6 col-lg-4 mb-3">
                                @php
                                    $jours = ['', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                                @endphp
                                <div class="card seance-list-item {{ $seance->type_seance }}">
                                    <div class="card-body">
                                        <h6 class="card-title d-flex justify-content-between align-items-center">
                                            {{ $seance->matiere->name }}
                                            @if(!$seance->is_active)
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </h6>
                                        <p class="card-text mb-1">
                                            <i class="fas fa-calendar-day me-1"></i> {{ $jours[$seance->jour_semaine] }}
                                        </p>
                                        <p class="card-text mb-1">
                                            <i class="fas fa-clock me-1"></i> {{ $seance->heure_debut }} - {{ $seance->heure_fin }}
                                        </p>
                                        <p class="card-text mb-1">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> {{ $seance->enseignant->name }}
                                        </p>
                                        <p class="card-text mb-1">
                                            <i class="fas fa-door-open me-1"></i> Salle: {{ $seance->salle }}
                                        </p>
                                        <p class="card-text mb-1">
                                            <i class="fas fa-tag me-1"></i> 
                                            @if($seance->type_seance == 'cours')
                                                Cours magistral
                                            @elseif($seance->type_seance == 'td')
                                                Travaux dirigés
                                            @elseif($seance->type_seance == 'tp')
                                                Travaux pratiques
                                            @elseif($seance->type_seance == 'examen')
                                                Examen
                                            @else
                                                Autre
                                            @endif
                                        </p>
                                        <div class="d-flex justify-content-end mt-2">
                                            <a href="{{ route('esbtp.seances-cours.edit', $seance->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aucune séance de cours n'a été ajoutée à cet emploi du temps.
                                    <a href="{{ route('esbtp.seances-cours.create', ['emploi_temps_id' => $emploiTemps->id]) }}" class="alert-link">
                                        Ajouter une séance de cours
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 