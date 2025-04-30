<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin de {{ $bulletin->etudiant ? $bulletin->etudiant->nom . ' ' . $bulletin->etudiant->prenoms : 'l\'étudiant' }}</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Styles généraux */
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            background-color: white;
            color: #000;
        }

        /* En-tête */
        .header {
            width: 100%;
            display: table;
            margin-bottom: 5px;
        }
        .header-row {
            display: table-row;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: left;
            padding: 3px;
        }
        .header-center {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: center;
            padding: 3px;
        }
        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
            padding: 3px;
        }
        .republic-title {
            font-weight: bold;
            font-size: 9pt;
        }
        .motto {
            font-style: italic;
            font-size: 8pt;
        }
        .ministry-title {
            font-size: 8pt;
        }
        .bulletin-header {
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }
        .period-header {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .school-name {
            font-weight: bold;
            font-size: 9pt;
            color: #0A6B31; /* Couleur verte du logo ESBTP */
        }
        .school-contact {
            font-size: 7pt;
        }
        .academic-year {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 2px;
        }

        /* Logo ESBTP */
        .esbtp-logo {
            width: 100px;
            height: 50px;
            margin: 0 auto;
            text-align: center;
        }
        .esbtp-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Informations de l'étudiant */
        .student-info-container {
            border: 1px solid #000;
            padding: 3px;
            margin-top: 5px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 5px;
        }
        .student-table-left, .student-table-right {
            width: 50%;
            float: left;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        .student-table td {
            padding: 3px;
        }
        .fw-bold {
            font-weight: bold;
        }

        /* Tableaux des notes */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 7pt;
        }
        .grades-table th, .grades-table td {
            border: 0.5px solid #000;
            padding: 3px;
            text-align: center;
        }
        .grades-table th {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .section-header {
            background-color: #333;
            color: white;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            padding: 5px 3px; 
        }
        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }

        /* Tableau des absences */
        .absence-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 7pt;
        }
        .absence-table td {
            border: 0.5px solid #000;
            padding: 3px;
        }

        /* Résultats */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            font-size: 7pt;
        }
        .results-table td {
            border: 0.5px solid #000;
            padding: 3px;
        }
        .result-value {
            text-align: center;
            font-weight: bold;
        }
        .result-cadre {
            padding: 0px 1px;
            display: inline-block;
            border: 0.5px solid #000;
        }

        /* Décision et signature */
        .decision-section {
            width: 100%;
            margin-top: 5px;
            display: table;
        }
        .decision-title,
        .signature-title {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 3px;
            text-align: center;
        }
        .decision-result {
            font-weight: bold;
            font-size: 9pt;
            margin-top: 3px;
            font-style: italic;
            text-align: center;
        }
        .decision-col,
        .signature-col {
            width: 33%;
            display: table-cell;
            text-align: center;
            font-size: 7pt;
        }

        /* Pied de page */
        .footer {
            margin-top: 5px;
            font-size: 7pt;
            text-align: center;
            font-style: italic;
            border-top: 0.5px solid #000;
            padding-top: 3px;
        }

        /* Contrôle de la mise en page */
        @page {
            size: A4 portrait;
            margin: 0.5cm;
            @top-center {
        content: element(page-header);
    }
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="header-row">
            <div class="header-left">
                <p class="republic-title">République de Côte d'Ivoire</p>
                <p class="motto">Union-Discipline-Travail</p>
                <p class="ministry-title">Ministère de l'Enseignement Supérieur</p>
                <p class="ministry-title">et de la Recherche Scientifique</p>
            </div>
            <div class="header-center"></div>
            <div class="header-right">
                <p class="bulletin-header">BULLETIN DE NOTES</p>
                <p class="period-header">{{ strtoupper($bulletin->periode == 'semestre1' ? 'PREMIER SEMESTRE' : ($bulletin->periode == 'semestre2' ? 'DEUXIÈME SEMESTRE' : 'ANNUEL')) }}</p>
                <p>Edition du: {{ date('d/m/Y') }}</p>
                <p>Cycle: </p>
                <p>Brevet de Technicien Supérieur</p>
                <p>BTS</p>
            </div>
        </div>

        <div class="header-row">
            <div class="header-left">
                <div class="esbtp-logo">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo ESBTP">
                    @else
                        <!-- Fallback direct URL pour l'image du logo -->
                        <img src="{{ public_path('images/esbtp_logo.png') }}" alt="Logo ESBTP"
                            onerror="this.onerror=null; this.src='{{ public_path('images/logo.jpeg') }}';
                            this.onerror=null; this.style.display='none';">
                    @endif
                </div>
            </div>
            <div class="header-center">
                <p class="school-name">Ecole Spéciale</p>
                <p class="school-name">du Bâtiment et des Travaux Publics</p>
                <p class="school-contact">BP 2541 Yamoussoukro - Email: esbtpabidjan@esbtp-ci.net</p>
                <p class="school-contact">Tél/Fax: 30 64 39 93 - Cel: 07 07 79 84 85</p>
            </div>
            <div class="header-right">
                <p class="academic-year">Année Scolaire: {{ $bulletin->anneeUniversitaire ? $bulletin->anneeUniversitaire->annee_debut . '-' . $bulletin->anneeUniversitaire->annee_fin : 'Non définie' }}</p>
            </div>
        </div>
    </div>

    <div class="student-info-container">
        <!-- Informations de l'étudiant -->
        <div class="student-info">
            <div class="student-table-left">
                <table class="student-table">
                    <tr>
                        <td class="fw-bold">Matricule :</td>
                        <td>{{ $bulletin->etudiant ? $bulletin->etudiant->matricule : 'Non défini' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Nom et Prénoms :</td>
                        <td>{{ $bulletin->etudiant ? $bulletin->etudiant->nom . ' ' . $bulletin->etudiant->prenoms : 'Non défini' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Date de Naissance :</td>
                        <td>{{ $bulletin->etudiant && $bulletin->etudiant->date_naissance ? $bulletin->etudiant->date_naissance->format('d/m/Y') : 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Redoublant :</td>
                        <td>{{ $bulletin->etudiant && $bulletin->etudiant->redoublant ? 'Oui' : 'Non' }}</td>
                    </tr>
                </table>
            </div>
            <div class="student-table-right">
                <table class="student-table">
                    <tr>
                        <td class="fw-bold">Classe :</td>
                        <td>{{ $bulletin->classe ? $bulletin->classe->name : 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Année d'étude :</td>
                        <td>{{ $bulletin->classe && $bulletin->classe->niveau ? $bulletin->classe->niveau->name : 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Effectif :</td>
                        <td>{{ $effectifClasse }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>
        <!-- Tableau des matières et notes - Enseignement Général -->
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Moyenne M</th>
                    <th>Coef C</th>
                    <th>Moy.Pond M*C</th>
                    <th>Rang</th>
                    <th>Professeurs</th>
                    <th>Appréciations</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td colspan="7">Enseignement Général</td>
                </tr>
                @forelse($resultatsGeneraux as $resultat)
                    <tr>
                        <td style="text-align: left;">{{ $resultat->matiere && ($resultat->matiere->nom || $resultat->matiere->name) ? ($resultat->matiere->nom ?: $resultat->matiere->name) : 'Matière non définie' }}</td>
                        <td>{{ number_format($resultat->moyenne, 2, '.', '') }}</td>
                        <td>{{ $resultat->coefficient }}</td>
                        <td>{{ number_format($resultat->moyenne * $resultat->coefficient, 2, '.', '') }}</td>
                        <td>{{ $resultat->rang ?? '-' }}</td>
                        <td>{{ $resultat->professeur ?? 'N/A' }}</td>
                        <td>{{ $resultat->appreciation ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Aucun résultat disponible pour cette section</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td style="text-align: left; width:25%;">Moyenne enseignement général</td>
                    <td colspan="6">{{ number_format($moyenneGeneraux, 2, '.', '') }}</td>                </tr>
            </tbody>
        </table>
        <table class="grades-table">
            <thead>
                <tr>
                    <th>Matière</th>
                    <th>Moyenne M</th>
                    <th>Coef C</th>
                    <th>Moy.Pond M*C</th>
                    <th>Rang</th>
                    <th>Professeurs</th>
                    <th>Appréciations</th>
                </tr>
            </thead>
            <tbody>
                <tr class="section-header">
                    <td colspan="7">Enseignement Technique</td>
                </tr>
                @forelse($resultatsTechniques as $resultat)
                    <tr>
                    <td style="text-align: left;">{{ $resultat->matiere && ($resultat->matiere->nom || $resultat->matiere->name) ? ($resultat->matiere->nom ?: $resultat->matiere->name) : 'Matière non définie' }}</td>
                    <td>{{ number_format($resultat->moyenne, 2, '.', '') }}</td>
                    <td>{{ $resultat->coefficient }}</td>
                    <td>{{ number_format($resultat->moyenne * $resultat->coefficient, 2, '.', '') }}</td>
                    <td>{{ $resultat->rang ?? '-' }}</td>
                    <td>{{ $resultat->professeur ?? 'N/A' }}</td>
                    <td>{{ $resultat->appreciation ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Aucun résultat disponible pour cette section</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td style="text-align: left; width:25%;">Moyenne enseignement technique</td>
                    <td colspan="6">{{ number_format($moyenneTechnique, 2, '.', '') }}</td>
                    </tr>
            </tbody>
        </table>

        <!-- Tableau des notes - Enseignement Technique
        <table class="grades-table">
        <thead>
            <tr>
                <th>Matière</th>
                <th>Moyenne M</th>
                <th>Coef C</th>
                <th>Moy.Pond M*C</th>
                <th>Rang</th>
                <th>Professeurs</th>
                <th>Appréciations</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-header">
                <td colspan="7">Enseignement Technique</td>
            </tr>
            @forelse($resultatsTechniques as $resultat)
                <tr>
                    <td style="text-align: left;">{{ $resultat->matiere && ($resultat->matiere->nom || $resultat->matiere->name) ? ($resultat->matiere->nom ?: $resultat->matiere->name) : 'Matière non définie' }}</td>
                    <td>{{ number_format($resultat->moyenne, 2, '.', '') }}</td>
                    <td>{{ $resultat->coefficient }}</td>
                    <td>{{ number_format($resultat->moyenne * $resultat->coefficient, 2, '.', '') }}</td>
                    <td>{{ $resultat->rang ?? '-' }}</td>
                    <td>{{ $resultat->professeur ?? 'N/A' }}</td>
                    <td>{{ $resultat->appreciation ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Aucun résultat disponible pour cette section</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td style="text-align: left;">Moyenne enseignement technique</td>
                <td colspan="6">{{ number_format($moyenneTechnique, 2, '.', '') }}</td>
            </tr>
        </tbody>
        </table> -->

        <!-- Absences -->
        <table class="absence-table">
            <thead>
                <tr class="section-header">
                    <td colspan="2">Nombre d'heures d'absence</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Absences justifiées</td>
                    <td>{{ isset($absencesJustifiees) ? $absencesJustifiees : (isset($absences_justifiees) ? $absences_justifiees : (isset($bulletin->absences_justifiees) ? $bulletin->absences_justifiees : '00')) }} Heure(s)</td>
                </tr>
                <tr>
                    <td>Absences non justifiées</td>
                    <td>{{ isset($absencesNonJustifiees) ? $absencesNonJustifiees : (isset($absences_non_justifiees) ? $absences_non_justifiees : (isset($bulletin->absences_non_justifiees) ? $bulletin->absences_non_justifiees : '00')) }} Heure(s)</td>
                </tr>
            </tbody>
        </table>

        <!-- Résultats -->
        <table class="results-table">
            <thead>
                <tr class="section-header">
                    <td colspan="6">RESULTATS</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Moyenne Brute</td>
                    <td class="result-value"><span class="result-cadre">{{ number_format($moyenneGenerale, 2, '.', '') }}</span></td>
                    <td style="text-align: center;">MENTION</td>
                    
                    <td></td>
                    <td style="text-align: center;">STATISTIQUES</td>

                    <td></td>
                </tr>
                <tr>
                </tr>
                <tr>
                    <td>Note d'assiduité</td>
                    <td class="result-value"><span class="result-cadre">{{ $noteAssiduite ?? '0.00' }}</span></td>
                    <td style="text-align: center;">Félicitation</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Félicitation') ? 'X' : '' }}</span></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                </tr>
                <tr>
                    <td>Moyenne 1er Semestre</td>
                        <td class="result-value">
                            <span class="result-cadre">
                                @if($moyenneSemestre1 !== null)
                                    {{ number_format($moyenneSemestre1, 2) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                    <td style="text-align: center;">Encouragement</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Encouragement') ? 'X' : '' }}</span></td>
                    <td>Plus forte moyenne</td>
                    <td><span class="result-cadre">{{ $plusForteMoyenne ?? '0.00' }}</span></td>
                </tr>
                <tr>
                    <td>Rang</td>
                    <td class="result-value"><span class="result-cadre">{{$studentRang ?? 'N/A' }}</span></td>
                    <td style="text-align: center;">Tableau d'honneur</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Tableau d\'honneur') ? 'X' : '' }}</span></td>
                    <td>Plus faible moyenne</td>
                    <td><span class="result-cadre">{{ $plusFaibleMoyenne ?? '0.00' }}</span></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: center;">Avertissement (Travail)</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Avertissement (Travail)') ? 'X' : '' }}</span></td>
                    <td>Moyenne de la classe</td>
                    <td><span class="result-cadre">{{ $moyenneClasse ?? '0.00' }}</span></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="text-align: center;">Blâme (Conduite)</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Blâme (Conduite)') ? 'X' : '' }}</span></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

       <!-- Décision et Signature -->
       
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
        <tr>
        <div style="margin-top: 30px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="text-align: center; padding-right:150px; border-top: 1px solid #000; padding-top: 10px;">
                <strong>Décision du conseil de classe</strong><br>
                <div style="min-height: 80px; margin-top: 15px;">
                    {{ $bulletin->decision_conseil ?? '' }}
                </div>
            </td>
            <td style="text-align: center; border-top: 1px solid #000; padding-top: 10px;">
                <strong >Signature de la Directrice des Etudes</strong><br>
                <div style="min-height: 80px; margin-top: 15px;">
                    @if(isset($bulletin->signature_direction) && $bulletin->signature_direction)
                        <img src="{{ public_path('img/signatures/direction.png') }}" height="50" alt="Signature Direction">
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>

        </tr>
    </table>
</div>
    <!-- Pied de page -->
    <div class="footer">
        <p>Bulletin informatisé, aucun duplicata n'est délivré</p>
    </div>
</body>
</html>
