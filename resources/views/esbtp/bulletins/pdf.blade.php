<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin de Notes - {{ $bulletin->etudiant ? $bulletin->etudiant->nom_complet : 'Étudiant non défini' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .school-info {
            font-size: 8pt;
            margin-bottom: 10px;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px;
            vertical-align: top;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .grades-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .section-title {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .signature {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .absence-box {
            margin: 10px 0;
            border: 1px solid #000;
            padding: 5px;
        }
        .results-box {
            margin: 10px 0;
            border: 1px solid #000;
            padding: 5px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table width="100%">
            <tr>
                <td width="20%" style="text-align: center;">
                    <img src="{{ public_path($config['school_logo']) }}" alt="Logo" height="80">
                </td>
                <td width="60%" style="text-align: center;">
                    <div style="font-weight: bold; font-size: 14pt;">{{ $config['school_name'] }}</div>
                    <div>{{ $config['school_type'] }}</div>
                    <div>{{ $config['school_authorization'] }}</div>
                </td>
                <td width="20%" style="text-align: center; vertical-align: middle;">
                    <div style="font-weight: bold;">BULLETIN DE NOTES</div>
                    <div>{{ $bulletin->periode == 'semestre1' ? 'Premier Semestre' : ($bulletin->periode == 'semestre2' ? 'Deuxième Semestre' : 'Annuel') }}</div>
                    <div>------------------</div>
                    <div>Edition du: {{ now()->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin: 10px 0;">
            <div style="font-weight: bold;">Cycle:</div>
            <div>{{ $bulletin->classe && $bulletin->classe->niveauEtude ? $bulletin->classe->niveauEtude->type : 'Brevet de Technicien Supérieur' }}</div>
            <div>{{ $bulletin->classe && $bulletin->classe->niveauEtude ? $bulletin->classe->niveauEtude->code : 'BTS' }}</div>
        </div>

        <div style="text-align: center; font-size: 8pt; margin: 5px 0;">
            <div>{{ $config['school_address'] }}</div>
            <div>{{ $config['school_phone'] }} - Email: {{ $config['school_email'] }}</div>
        </div>

        <!-- Student Info -->
        <table class="info-table" style="margin-top: 10px;">
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td width="40%"><strong>Matricule:</strong></td>
                            <td width="60%">{{ $bulletin->etudiant ? $bulletin->etudiant->matricule : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nom et Prénoms:</strong></td>
                            <td>{{ $bulletin->etudiant ? $bulletin->etudiant->nom . ' ' . $bulletin->etudiant->prenoms : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Date de Naissance:</strong></td>
                            <td>{{ $bulletin->etudiant && $bulletin->etudiant->date_naissance ? $bulletin->etudiant->date_naissance->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lieu de Naissance:</strong></td>
                            <td>{{ $bulletin->etudiant ? ($bulletin->etudiant->lieu_naissance ?? 'N/A') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Redoublant:</strong></td>
                            <td>{{ $bulletin->etudiant ? ($bulletin->etudiant->redoublant ? 'Oui' : 'Non') : 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td width="40%"><strong>Année Scolaire:</strong></td>
                            <td width="60%">{{ $bulletin->anneeUniversitaire ? $bulletin->anneeUniversitaire->libelle : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Classe:</strong></td>
                            <td>{{ $bulletin->classe ? $bulletin->classe->libelle : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Filière:</strong></td>
                            <td>{{ $bulletin->classe && $bulletin->classe->filiere ? $bulletin->classe->filiere->nom : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Année d'étude:</strong></td>
                            <td>{{ $bulletin->classe && $bulletin->classe->niveauEtude ? $bulletin->classe->niveauEtude->libelle : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Effectif:</strong></td>
                            <td>{{ $bulletin->effectif_classe }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Grades -->
        <table class="grades-table">
            <thead>
                <tr>
                    <th width="25%">Matière</th>
                    <th width="10%">Moyenne</th>
                    <th width="10%">Coef</th>
                    <th width="10%">Points</th>
                    <th width="10%">Rang</th>
                    <th width="15%">Professeur</th>
                    <th width="20%">Appréciation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="section-title">Enseignement Général</td>
                </tr>
                @php
                    $totalPointsGeneral = 0;
                    $totalCoefGeneral = 0;
                @endphp

                @foreach($resultatsGeneraux as $resultat)
                    <tr>
                        <td class="text-left">{{ $resultat->matiere ? $resultat->matiere->nom : 'Matière non définie' }}</td>
                        <td>{{ number_format($resultat->moyenne, 2) }}</td>
                        <td>{{ $resultat->coefficient }}</td>
                        <td>{{ number_format($resultat->moyenne * $resultat->coefficient, 2) }}</td>
                        <td>{{ $resultat->rang ?? '-' }}</td>
                        <td>{{ $resultat->matiere ? ($resultat->matiere->professor_name ?? 'N/A') : 'N/A' }}</td>
                        <td>{{ $resultat->appreciation ?? ($resultat->matiere ? $resultat->determinerAppreciation() : 'N/A') }}</td>
                    </tr>
                    @php
                        $totalPointsGeneral += $resultat->moyenne * $resultat->coefficient;
                        $totalCoefGeneral += $resultat->coefficient;
                    @endphp
                @endforeach

                <tr>
                    <td colspan="2" class="text-right"><strong>Moyenne Enseignement Général</strong></td>
                    <td>{{ $totalCoefGeneral }}</td>
                    <td>{{ number_format($totalPointsGeneral, 2) }}</td>
                    <td colspan="3">{{ $totalCoefGeneral > 0 ? number_format($totalPointsGeneral / $totalCoefGeneral, 2) : '-' }}</td>
                </tr>

                <tr>
                    <td colspan="7" class="section-title">Enseignement Technique</td>
                </tr>
                @php
                    $totalPointsTechnique = 0;
                    $totalCoefTechnique = 0;
                @endphp

                @foreach($resultatsTechniques as $resultat)
                    <tr>
                        <td class="text-left">{{ $resultat->matiere ? $resultat->matiere->nom : 'Matière non définie' }}</td>
                        <td>{{ number_format($resultat->moyenne, 2) }}</td>
                        <td>{{ $resultat->coefficient }}</td>
                        <td>{{ number_format($resultat->moyenne * $resultat->coefficient, 2) }}</td>
                        <td>{{ $resultat->rang ?? '-' }}</td>
                        <td>{{ $resultat->matiere ? ($resultat->matiere->professor_name ?? 'N/A') : 'N/A' }}</td>
                        <td>{{ $resultat->appreciation ?? ($resultat->matiere ? $resultat->determinerAppreciation() : 'N/A') }}</td>
                    </tr>
                    @php
                        $totalPointsTechnique += $resultat->moyenne * $resultat->coefficient;
                        $totalCoefTechnique += $resultat->coefficient;
                    @endphp
                @endforeach

                <tr>
                    <td colspan="2" class="text-right"><strong>Moyenne Enseignement Technique</strong></td>
                    <td>{{ $totalCoefTechnique }}</td>
                    <td>{{ number_format($totalPointsTechnique, 2) }}</td>
                    <td colspan="3">{{ $totalCoefTechnique > 0 ? number_format($totalPointsTechnique / $totalCoefTechnique, 2) : '-' }}</td>
                </tr>

                <tr>
                    <td colspan="2" class="text-right"><strong>MOYENNE GÉNÉRALE</strong></td>
                    <td>{{ $totalCoefGeneral + $totalCoefTechnique }}</td>
                    <td>{{ number_format($totalPointsGeneral + $totalPointsTechnique, 2) }}</td>
                    <td colspan="3"><strong>{{ number_format($bulletin->moyenne_generale, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Absences -->
        <table class="grades-table">
            <tr>
                <td colspan="7" class="section-title">Nombre d'heures d'absence</td>
            </tr>
            <tr>
                <td width="25%">Absences justifiées</td>
                <td width="25%">{{ number_format($bulletin->absences_justifiees ?? 0, 2) }} heures</td>
                <td width="25%">Absences non justifiées</td>
                <td width="25%">{{ number_format($bulletin->absences_non_justifiees ?? 0, 2) }} heures</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Total des absences</strong></td>
                <td colspan="2"><strong>{{ number_format($bulletin->total_absences ?? 0, 2) }} heures</strong></td>
            </tr>
        </table>

        <!-- Results -->
        <table class="grades-table">
            <tr>
                <td width="25%"><strong>Rang:</strong></td>
                <td width="25%">{{ $bulletin->rang ?? '-' }} / {{ $bulletin->effectif_classe ?? '-' }}</td>
                <td width="25%"><strong>Mention:</strong></td>
                <td width="25%">{{ $bulletin->mention ?? 'Non définie' }}</td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: left; padding: 10px;">
                    <strong>Appréciation générale:</strong><br>
                    {{ $bulletin->appreciation_generale ?? 'Aucune appréciation' }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="text-align: left; padding: 10px;">
                    <strong>Décision du conseil:</strong><br>
                    {{ $bulletin->decision_conseil ?? 'Aucune décision' }}
                </td>
            </tr>
        </table>

        <!-- Signatures -->
        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
            <div class="signature">
                Le Directeur
            </div>
            <div class="signature">
                Le Responsable Pédagogique
            </div>
            <div class="signature">
                Le Parent
            </div>
        </div>

        <div class="footer">
            {{ $config['school_name'] }} - {{ $config['school_address'] }} - {{ $config['school_phone'] }}
        </div>
    </div>
</body>
</html>

@php
function getAppreciation($moyenne) {
    if ($moyenne >= 16) return 'Excellent';
    if ($moyenne >= 14) return 'Très Bien';
    if ($moyenne >= 12) return 'Bien';
    if ($moyenne >= 10) return 'Assez Bien';
    if ($moyenne >= 8) return 'Passable';
    return 'Insuffisant';
}
@endphp
