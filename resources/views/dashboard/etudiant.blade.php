@extends('layouts.app')

@section('title', 'Tableau de bord Étudiant')

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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $etudiant->matricule }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.etudiants.show', ['etudiant' => $etudiant->id]) }}" class="btn btn-sm btn-primary mt-3">Voir mon profil</a>
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
                    <a href="{{ route('esbtp.classes.show', ['classe' => $classe->id]) }}" class="btn btn-sm btn-success mt-3">Détails de la classe</a>
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Taux de présence</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendancePercentage }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.mes-absences.index') }}" class="btn btn-sm btn-danger mt-3">Voir mes présences</a>
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
                        <a href="{{ route('notes.student', $etudiant->id) }}" class="btn btn-primary">Voir toutes mes notes</a>
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
</div>
@endsection
