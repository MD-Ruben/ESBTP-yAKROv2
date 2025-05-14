@extends('layouts.app')

@section('title', 'Emploi du temps - Enseignant')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mon emploi du temps</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Emploi du temps</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-calendar-alt me-1"></i>
                Emploi du temps de {{ $enseignantNom }}
            </div>
            <div>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Retour au tableau de bord
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="table-primary">
                            <th width="15%">Horaire</th>
                            @foreach($joursSemaine as $jour => $nomJour)
                                <th>{{ $nomJour }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Définir les créneaux horaires standards (peut être adapté)
                            $creneaux = [
                                '08:00-10:00',
                                '10:00-12:00',
                                '12:00-14:00',
                                '14:00-16:00',
                                '16:00-18:00'
                            ];
                        @endphp

                        @foreach($creneaux as $creneau)
                            <tr>
                                <td class="align-middle font-weight-bold">{{ $creneau }}</td>
                                
                                @foreach($joursSemaine as $jour => $nomJour)
                                    <td>
                                        @php
                                            // Pour chaque jour et créneau, trouver les séances correspondantes
                                            list($debut, $fin) = explode('-', $creneau);
                                            $seancesCreneau = $emploiTempsSemaine[$jour]->filter(function($seance) use ($debut, $fin) {
                                                $heureDebut = substr($seance->heure_debut, 0, 5);
                                                $heureFin = substr($seance->heure_fin, 0, 5);
                                                
                                                // Vérifier si la séance est dans ce créneau
                                                return ($heureDebut >= $debut && $heureDebut < $fin) || 
                                                       ($heureFin > $debut && $heureFin <= $fin) || 
                                                       ($heureDebut <= $debut && $heureFin >= $fin);
                                            });
                                        @endphp
                                        
                                        @forelse($seancesCreneau as $seance)
                                            <div class="course-block p-2 mb-2 rounded
                                                @if($seance->type_seance == 'cours') bg-primary-light
                                                @elseif($seance->type_seance == 'td') bg-success-light
                                                @else bg-warning-light
                                                @endif">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="badge 
                                                        @if($seance->type_seance == 'cours') bg-primary
                                                        @elseif($seance->type_seance == 'td') bg-success
                                                        @else bg-warning
                                                        @endif">
                                                        {{ strtoupper($seance->type_seance) }}
                                                    </span>
                                                    <span class="text-muted small">
                                                        {{ substr($seance->heure_debut, 0, 5) }}-{{ substr($seance->heure_fin, 0, 5) }}
                                                    </span>
                                                </div>
                                                <div class="fw-bold mb-1">{{ $seance->matiere->name ?? 'Matière non définie' }}</div>
                                                <div class="small">
                                                    <i class="fas fa-users me-1"></i> 
                                                    {{ $seance->emploiTemps->classe->name ?? 'Classe non définie' }}
                                                </div>
                                                <div class="small">
                                                    <i class="fas fa-map-marker-alt me-1"></i> 
                                                    {{ $seance->salle ?? 'Salle non définie' }}
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-coffee"></i>
                                            </div>
                                        @endforelse
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .bg-primary-light {
        background-color: rgba(99, 102, 241, 0.1);
    }
    .bg-success-light {
        background-color: rgba(34, 197, 94, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(245, 158, 11, 0.1);
    }
    .course-block {
        border-left: 3px solid var(--nextadmin-primary);
    }
    .course-block:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection 