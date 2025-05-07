@extends('layouts.app')

@section('title', 'Tableau de bord Étudiant')

@push('styles')
<style>
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .progress {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="my-4">Bienvenue, {{ $user->name }}</h1>
    <p class="text-muted">Votre espace étudiant ESBTP-yAKRO</p>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Numéro matricule</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $student->matricule }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.etudiants.show', ['etudiant' => $student->id]) }}" class="btn btn-sm btn-primary mt-3">Voir mon profil</a>
                </div>
            </div>
        </div>

        @if(isset($classe))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Classe</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classe->nom }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.student.classes.show', ['classe' => $classe->id]) }}" class="btn btn-sm btn-success mt-3">Détails de la classe</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($filiere))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Filière</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $filiere->nom }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        @if(isset($unreadNotifications))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Notifications non lues</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unreadNotifications }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-warning mt-3">Voir toutes les notifications</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($attendancePercentage))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase fw-semibold text-muted mb-0">Présences</h6>
                        <div class="stat-icon bg-primary-light rounded-circle p-2">
                            <i class="fas fa-clipboard-check text-primary"></i>
                        </div>
                    </div>

                    <div class="attendance-stats">
                        <div class="d-flex align-items-center mb-2">
                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                <div class="progress-bar {{ $attendancePercentage >= 75 ? 'bg-success' : ($attendancePercentage >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                     role="progressbar"
                                     style="width: {{ $attendancePercentage }}%"
                                     aria-valuenow="{{ $attendancePercentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <span class="fw-bold">{{ $attendancePercentage }}%</span>
                        </div>

                        <div class="d-flex justify-content-between text-muted small">
                            <span>Présences</span>
                            <span>Objectif: 100%</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('esbtp.mes-absences.index') }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye me-1"></i> Voir mes présences
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(isset($niveau))
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Niveau d'étude</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $niveau->nom }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Cours aujourd'hui -->
    @if(isset($todayClasses) && $todayClasses->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cours d'aujourd'hui</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Horaire</th>
                                    <th>Salle</th>
                                    <th>Enseignant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todayClasses as $cours)
                                <tr>
                                    <td>{{ $cours->matiere->nom ?? 'N/A' }}</td>
                                    <td>{{ $cours->heure_debut->format('H:i') }} - {{ $cours->heure_fin->format('H:i') }}</td>
                                    <td>{{ $cours->salle }}</td>
                                    <td>{{ $cours->enseignant }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Examens à venir -->
    @if(isset($upcomingExams) && $upcomingExams->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Examens à venir</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Matière</th>
                                    <th>Date</th>
                                    <th>Heure</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingExams as $examen)
                                <tr>
                                    <td>{{ $examen->type }}</td>
                                    <td>{{ $examen->matiere->nom ?? 'N/A' }}</td>
                                    <td>{{ $examen->date->format('d/m/Y') }}</td>
                                    <td>{{ $examen->heure }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Notes récentes -->
    @if(isset($recentGrades) && $recentGrades->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notes récentes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Évaluation</th>
                                    <th>Note</th>
                                    <th>Coefficient</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentGrades as $note)
                                <tr>
                                    <td>{{ $note->matiere->nom ?? 'N/A' }}</td>
                                    <td>{{ $note->evaluation->type ?? 'N/A' }}</td>
                                    <td>{{ $note->note }}/20</td>
                                    <td>{{ $note->coefficient }}</td>
                                    <td>{{ $note->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('esbtp.mes-notes.index') }}" class="btn btn-primary">Voir toutes mes notes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar me-1"></i>
            Emploi du temps - {{ $classe->name ?? 'Classe non définie' }}
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Horaire</th>
                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $index => $jour)
                                <th>{{ $jour }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['08:00-10:00', '10:00-12:00', '13:00-15:00', '15:00-17:00', '17:00-19:00'] as $horaire)
                            <tr>
                                <td class="align-middle">{{ $horaire }}</td>
                                @foreach(range(0, 5) as $jour)
                                    <td class="align-middle">
                                        @if(isset($seances[$jour]))
                                            @foreach($seances[$jour] as $seance)
                                                @php
                                                    $heureDebut = \Carbon\Carbon::parse($seance->heure_debut)->format('H:i');
                                                    $heureFin = \Carbon\Carbon::parse($seance->heure_fin)->format('H:i');
                                                    $creneauSeance = $heureDebut.'-'.$heureFin;
                                                    $creneauMatch = false;

                                                    // Extraire les heures de début et de fin du créneau actuel
                                                    list($slotStart, $slotEnd) = explode('-', $horaire);

                                                    // Debug logging
                                                    \Log::debug('Comparaison des créneaux:', [
                                                        'jour' => $jour,
                                                        'horaire_attendu' => $horaire,
                                                        'slot_start' => $slotStart,
                                                        'slot_end' => $slotEnd,
                                                        'seance_debut' => $heureDebut,
                                                        'seance_fin' => $heureFin,
                                                        'matiere' => $seance->matiere->name ?? 'Non définie',
                                                        'seance_id' => $seance->id
                                                    ]);

                                                    // Comparer les heures sans les secondes
                                                    if ($heureDebut === $slotStart && $heureFin === $slotEnd) {
                                                        $creneauMatch = true;
                                                        \Log::info('Créneau correspondant trouvé:', [
                                                            'jour' => $jour,
                                                            'horaire' => $horaire,
                                                            'matiere' => $seance->matiere->name ?? 'Non définie',
                                                            'seance_id' => $seance->id
                                                        ]);
                                                    }
                                                @endphp
                                                @if($creneauMatch)
                                                    <div class="p-2 bg-light border rounded">
                                                        <strong>{{ $seance->matiere->name ?? 'Matière non définie' }}</strong><br>
                                                        @if($seance->salle)
                                                            Salle: {{ $seance->salle }}<br>
                                                        @endif
                                                        @if($seance->enseignantName)
                                                            Prof: {{ $seance->enseignantName }}
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            @php
                                                \Log::warning('Aucune séance trouvée pour le jour ' . $jour);
                                            @endphp
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($emploiTemps))
            <div class="card-footer">
                <small class="text-muted">
                    Période: {{ \Carbon\Carbon::parse($emploiTemps->date_debut)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($emploiTemps->date_fin)->format('d/m/Y') }}
                </small>
            </div>
        @endif
    </div>

    <!-- Statistiques de présence -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold">Statistiques de présence</h5>
                    <div class="rounded-circle bg-light p-2">
                        <i class="fas fa-chart-pie text-primary"></i>
                    </div>
                </div>

                @php
                    // Calculer le taux de présence en utilisant les données du contrôleur
                    $totalAttendances = isset($presences) && isset($absences) ?
                        $presences->count() + $absences->count() +
                        (isset($retards) ? $retards->count() : 0) +
                        (isset($excuses) ? $excuses->count() : 0) : 0;

                    $present = isset($presences) ? $presences->count() : 0;
                    $retard = isset($retards) ? $retards->count() : 0;
                    $excuse = isset($excuses) ? $excuses->count() : 0;

                    $presenceRate = $totalAttendances > 0 ?
                        round((($present + $retard + $excuse) / $totalAttendances) * 100) : 100;

                    // Couleur basée sur le taux de présence
                    $progressColor = $presenceRate >= 75 ? 'success' : ($presenceRate >= 50 ? 'warning' : 'danger');
                @endphp

                <div class="attendance-stats mb-3">
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted">Taux de présence</span>
                        <span class="fw-bold">{{ $presenceRate }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-{{ $progressColor }}"
                             role="progressbar"
                             style="width: {{ $presenceRate }}%"
                             aria-valuenow="{{ $presenceRate }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <div class="d-flex mb-3 text-center">
                    <div class="col-4">
                        <div class="p-2 bg-success-light rounded mb-2">
                            <i class="fas fa-check text-success"></i>
                        </div>
                        <h6 class="fw-bold mb-0">{{ $present }}</h6>
                        <small class="text-muted">Présences</small>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-danger-light rounded mb-2">
                            <i class="fas fa-times text-danger"></i>
                        </div>
                        <h6 class="fw-bold mb-0">{{ isset($absences) ? $absences->count() : 0 }}</h6>
                        <small class="text-muted">Absences</small>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-warning-light rounded mb-2">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h6 class="fw-bold mb-0">{{ $retard }}</h6>
                        <small class="text-muted">Retards</small>
                    </div>
                </div>

                <a href="{{ route('esbtp.mes-absences.index') }}" class="btn btn-primary mt-auto">
                    <i class="fas fa-calendar-check me-2"></i>Voir toutes mes absences
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
