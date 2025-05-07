<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Emploi du Temps - {{ $emploiTemps->classe->name ?? 'Non défini' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 10px;
            background-color: #01632f;
            color: #ffffff;
            position: relative;
        }
        .logo-container {
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .logo {
            max-width: 50px;
        }
        .school-info {
            margin-bottom: 5px;
        }
        .school-info h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #ffffff;
        }
        .school-info h3 {
            font-size: 12px;
            margin: 5px 0;
            color: #ffffff;
        }
        .class-info {
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #f29400;
            padding: 5px 10px;
        }
        .class-info p {
            margin: 2px 0;
        }
        .timetable {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .timetable th, .timetable td {
            border: 1px solid #dee2e6;
            padding: 3px;
            text-align: center;
            height: 28px;
            font-size: 8px;
        }
        .timetable th {
            background-color: #01632f;
            color: #ffffff;
            font-weight: bold;
        }
        .time-column {
            width: 60px;
            font-weight: bold;
            background-color: #f0f0f0;
            border-right: 2px solid #01632f;
        }
        .session-cell {
            padding: 2px;
            border-radius: 3px;
        }
        .session-cours {
            background-color: rgba(1, 99, 47, 0.2);
            border: 1px solid #01632f;
        }
        .session-td {
            background-color: rgba(242, 148, 0, 0.2);
            border: 1px solid #f29400;
        }
        .session-tp {
            background-color: rgba(1, 99, 47, 0.4);
            border: 1px solid #01632f;
        }
        .session-examen {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
        }
        .session-autre {
            background-color: rgba(108, 117, 125, 0.2);
            border: 1px solid #6c757d;
        }
        .session-pause {
            background-color: rgba(173, 181, 189, 0.3);
            border: 1px solid #adb5bd;
        }
        .session-dejeuner {
            background-color: rgba(242, 148, 0, 0.4);
            border: 1px solid #f29400;
        }
        .bottom-container {
            margin-top: 5px;
        }
        .legend {
            margin-top: 5px;
            margin-bottom: 5px;
            padding: 5px;
            border-top: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
        }
        .legend-title {
            font-weight: bold;
            font-size: 8px;
            color: #01632f;
            margin-bottom: 3px;
            display: inline-block;
            margin-right: 5px;
        }
        .legend-container {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 8px;
            margin-bottom: 2px;
            white-space: nowrap;
        }
        .legend-color {
            width: 8px;
            height: 8px;
            border: 1px solid #dee2e6;
            margin-right: 3px;
            border-radius: 2px;
            display: inline-block;
        }
        .legend-item small {
            font-size: 7px;
        }
        .footer {
            margin-top: 5px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            padding-top: 3px;
        }
        .signature {
            float: right;
            text-align: right;
            padding-right: 20px;
            margin-top: 5px;
        }
        .signature-line {
            width: 120px;
            border-top: 1px solid #000;
            margin-left: auto;
            margin-bottom: 3px;
        }
        .signature small {
            font-size: 8px;
        }
        .page-number {
            text-align: right;
            font-size: 8px;
            color: #6c757d;
            margin-top: 5px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .header-left {
            position: absolute;
            top: 10px;
            left: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/LOGO-KLASSCI-PNG.png') }}" alt="Logo" class="logo">
        </div>
        <div class="school-info">
            <h2>Ecole Speciale du Bâtiment et des Travaux Publics</h2>
            <h3>EMPLOI DU TEMPS - {{ strtoupper($emploiTemps->semestre) }}</h3>
        </div>
    </div>

    <div class="class-info">
        <p><strong>Classe:</strong> {{ $emploiTemps->classe->name ?? 'Non définie' }} | <strong>Filière:</strong> {{ $emploiTemps->classe->filiere->name ?? 'Non définie' }} | <strong>Année:</strong> {{ $emploiTemps->annee->name ?? 'Non définie' }} | <strong>Période:</strong> Du {{ $emploiTemps->date_debut ? $emploiTemps->date_debut->format('d/m/Y') : 'Non définie' }} au {{ $emploiTemps->date_fin ? $emploiTemps->date_fin->format('d/m/Y') : 'Non définie' }}</p>
    </div>

    <table class="timetable">
        <thead>
            <tr>
                <th class="time-column">Heure</th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
            </tr>
        </thead>
        <tbody>
            @php
            // Créer une grille pour suivre les cellules occupées par des rowspans
            $occupiedCells = [];
            foreach ($days as $day) {
                foreach ($timeSlots as $slotIndex => $slot) {
                    $occupiedCells[$day][$slotIndex] = false;
                }
            }

            // Pré-traiter les séances pour déterminer les rowspans
            $seancesWithRowspans = [];
            if (isset($seances)) {
                foreach ($seances as $seance) {
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
                <td class="time-column">{{ $timeSlot }}</td>
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
                    <td rowspan="{{ $rowspan }}" class="session-cell session-{{ $seanceToDisplay->type_seance }}">
                        @if(in_array($seanceToDisplay->type_seance, ['pause', 'dejeuner']))
                            {{-- Affichage simplifié pour les pauses --}}
                            <strong>
                                {{ $seanceToDisplay->type_seance === 'pause' ? 'Récréation' : 'Pause déjeuner' }}
                            </strong><br>
                            {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                        @else
                            {{-- Affichage normal pour cours, TD, etc. --}}
                            <strong>
                                {{ $seanceToDisplay->matiere?->name ?? 'Non défini' }}
                            </strong><br>
                            {{ $seanceToDisplay->enseignantName ?? '' }}<br>
                            {{ $seanceToDisplay->salle ?? 'Non définie' }} | 
                            {{ $seanceToDisplay->heure_debut->format('H:i') }} - {{ $seanceToDisplay->heure_fin->format('H:i') }}
                        @endif
                    </td>

                        <!-- <td rowspan="{{ $rowspan }}" class="session-cell session-{{ $seanceToDisplay->type_seance }}">
                            <strong>{{ $seanceToDisplay->matiere ? $seanceToDisplay->matiere->name : 'Non défini' }}</strong><br>
                            {{ $seanceToDisplay->enseignantName }}<br>
                            {{ $seanceToDisplay->salle ?? 'Non défini' }} | {{ $seanceToDisplay->heure_debut->format('H:i') }}-{{ $seanceToDisplay->heure_fin->format('H:i') }}
                        </td> -->
                    @elseif(!$cellOccupied)
                        <td></td>
                    @endif
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="bottom-container clearfix">
        <div class="signature">
            <div class="signature-line"></div>
            <small>Direction des Études</small>
        </div>

        <div class="legend">
            <div class="legend-title">Légende :</div>
            <div class="legend-container">
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

        <div class="footer">
            <p>Ecole Speciale du Bâtiment et des Travaux Publics - {{ $date_edition }}</p>
            <p>Ce document est généré automatiquement et ne nécessite pas de signature.</p>
        </div>

        <div class="page-number">
            Page 1
        </div>
    </div>
</body>
</html>
