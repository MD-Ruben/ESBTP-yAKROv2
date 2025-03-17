@extends('layouts.app')

@section('title', 'Mon Emploi du Temps')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mon Emploi du Temps</h1>

    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if(isset($inscription) && $inscription)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-graduation-cap me-1"></i>
                Informations sur ma classe
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Classe:</strong> {{ $inscription->classe->name ?? 'Non définie' }}</p>
                        <p><strong>Filière:</strong> {{ $inscription->classe->filiere->name ?? 'Non définie' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Niveau d'études:</strong> {{ $inscription->classe->niveauEtude->name ?? 'Non défini' }}</p>
                        <p><strong>Année universitaire:</strong> {{ $inscription->anneeUniversitaire->libelle ?? 'Non définie' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($emploiTemps)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar me-1"></i>
                Emploi du temps - {{ $inscription->classe->name ?? 'Classe non définie' }}
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
                            @php
                                // Define time slots
                                $timeSlots = [
                                    '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00',
                                    '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00', '17:00-18:00'
                                ];

                                // Create a grid to track which cells are already occupied by rowspans
                                $occupiedCells = [];
                                foreach (range(1, 6) as $jour) {
                                    foreach ($timeSlots as $slotIndex => $slot) {
                                        $occupiedCells[$jour][$slotIndex] = false;
                                    }
                                }

                                // Pre-process seances to determine rowspans
                                $seancesWithRowspans = [];
                                foreach (range(1, 6) as $jour) {
                                    if (isset($seancesGroupees[$jour])) {
                                        foreach ($seancesGroupees[$jour] as $seance) {
                                            $heureDebut = $seance->heure_debut;
                                            $heureFin = $seance->heure_fin;

                                            if ($heureDebut instanceof \DateTime || $heureDebut instanceof \Carbon\Carbon) {
                                                $heureDebut = $heureDebut->format('H:i');
                                            } else {
                                                $heureDebut = substr($heureDebut, 0, 5);
                                            }

                                            if ($heureFin instanceof \DateTime || $heureFin instanceof \Carbon\Carbon) {
                                                $heureFin = $heureFin->format('H:i');
                                            } else {
                                                $heureFin = substr($heureFin, 0, 5);
                                            }

                                            // Find the starting and ending slot indices
                                            $startSlotIndex = null;
                                            $endSlotIndex = null;

                                            foreach ($timeSlots as $slotIndex => $slot) {
                                                list($slotStart, $slotEnd) = explode('-', $slot);

                                                // Find the first slot that overlaps with the seance
                                                if ($startSlotIndex === null &&
                                                    strtotime($heureDebut) < strtotime($slotEnd) &&
                                                    strtotime($heureFin) > strtotime($slotStart)) {
                                                    $startSlotIndex = $slotIndex;
                                                }

                                                // Find the last slot that overlaps with the seance
                                                if (strtotime($heureDebut) < strtotime($slotEnd) &&
                                                    strtotime($heureFin) > strtotime($slotStart)) {
                                                    $endSlotIndex = $slotIndex;
                                                }
                                            }

                                            if ($startSlotIndex !== null && $endSlotIndex !== null) {
                                                $rowspan = $endSlotIndex - $startSlotIndex + 1;
                                                $seancesWithRowspans[] = [
                                                    'seance' => $seance,
                                                    'jour' => $jour,
                                                    'startSlotIndex' => $startSlotIndex,
                                                    'endSlotIndex' => $endSlotIndex,
                                                    'rowspan' => $rowspan,
                                                    'heureDebut' => $heureDebut,
                                                    'heureFin' => $heureFin
                                                ];

                                                // Mark cells as occupied
                                                for ($i = $startSlotIndex; $i <= $endSlotIndex; $i++) {
                                                    $occupiedCells[$jour][$i] = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp

                            @foreach($timeSlots as $slotIndex => $horaire)
                                <tr>
                                    <td class="align-middle">{{ $horaire }}</td>
                                    @foreach(range(1, 6) as $jour)
                                        @php
                                            $cellOccupied = false;
                                            $seanceToDisplay = null;
                                            $rowspan = 1;

                                            // Check if this cell is the starting point of a multi-row seance
                                            foreach ($seancesWithRowspans as $seanceData) {
                                                if ($seanceData['jour'] == $jour && $seanceData['startSlotIndex'] == $slotIndex) {
                                                    $cellOccupied = true;
                                                    $seanceToDisplay = $seanceData;
                                                    $rowspan = $seanceData['rowspan'];
                                                    break;
                                                }
                                            }

                                            // Check if this cell is covered by a rowspan from a previous row
                                            if (!$cellOccupied && $occupiedCells[$jour][$slotIndex] && $slotIndex > 0) {
                                                foreach ($seancesWithRowspans as $seanceData) {
                                                    if ($seanceData['jour'] == $jour &&
                                                        $slotIndex > $seanceData['startSlotIndex'] &&
                                                        $slotIndex <= $seanceData['endSlotIndex']) {
                                                        $cellOccupied = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp

                                        @if($cellOccupied && $seanceToDisplay)
                                            <td class="align-middle" rowspan="{{ $rowspan }}">
                                                <div class="p-2 bg-light border rounded">
                                                    <strong>{{ $seanceToDisplay['seance']->matiere->name ?? 'Matière non définie' }}</strong><br>
                                                    <small class="text-muted">{{ $seanceToDisplay['heureDebut'] }} - {{ $seanceToDisplay['heureFin'] }}</small><br>
                                                    @if($seanceToDisplay['seance']->salle)
                                                        Salle: {{ $seanceToDisplay['seance']->salle }}<br>
                                                    @endif
                                                    @if($seanceToDisplay['seance']->enseignant)
                                                        Prof: {{ $seanceToDisplay['seance']->enseignantName }}
                                                    @endif
                                                </div>
                                            </td>
                                        @elseif(!$cellOccupied)
                                            <td></td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <small class="text-muted">
                    Période: {{ \Carbon\Carbon::parse($emploiTemps->date_debut)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($emploiTemps->date_fin)->format('d/m/Y') }}
                </small>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucun emploi du temps n'est actuellement disponible pour votre classe.
            @if(isset($inscription) && $inscription)
                <p class="mt-2 mb-0">
                    Vous êtes inscrit dans la classe: <strong>{{ $inscription->classe->name ?? 'Non définie' }}</strong>
                </p>
            @endif
        </div>
    @endif
</div>
@endsection
