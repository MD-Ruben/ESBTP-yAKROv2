<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin de Notes - {{ $bulletin->etudiant ? $bulletin->etudiant->nom_complet : 'Étudiant non défini' }}</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            width: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8pt; /* Taille de police réduite */
            line-height: 1.1; /* Interligne réduit */
        }
        body {
            max-width: 100%;
            max-height: 100%;
            padding: 2px; /* Padding réduit */
        }

        /* Clearfix pour les flottants */
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Mise en page principale */
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        /* En-tête */
        .header {
            width: 100%;
            margin-bottom: 2px; /* Marge réduite */
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1px; /* Marge réduite */
        }
        .header-left,
        .header-center,
        .header-right {
            width: 33%;
            text-align: center;
        }
        /* Styles header spécifiques */
        .republic-title {
            font-weight: bold;
            font-size: 6pt; /* Taille de police réduite */
            margin-bottom: 0px;
        }
        .motto {
            font-style: italic;
            font-size: 5pt; /* Taille de police réduite */
            margin-bottom: 0px;
        }
        .ministry-title {
            font-size: 5pt; /* Taille de police réduite */
            margin-bottom: 0px;
        }
        .bulletin-header {
            font-weight: bold;
            font-size: 7pt; /* Taille de police réduite */
            margin-bottom: 0px;
            text-transform: uppercase;
        }
        .period-header {
            font-weight: bold;
            font-size: 6pt; /* Taille de police réduite */
            margin-bottom: 0px;
            text-transform: uppercase;
        }

        /* Logo et info école */
        .esbtp-logo {
            text-align: center;
            margin-bottom: 1px; /* Marge réduite */
        }
        .esbtp-logo img {
            height: 35px; /* Hauteur réduite */
        }
        .school-name {
            font-size: 7pt; /* Taille de police réduite */
            font-weight: bold;
            color: #009900;  /* Vert ESBTP */
            margin-bottom: 0px;
        }
        .school-contact {
            font-size: 5pt; /* Taille de police réduite */
            margin-bottom: 0px;
        }
        .academic-year {
            font-weight: bold;
            font-size: 6pt; /* Taille de police réduite */
            margin-top: 1px; /* Marge réduite */
        }

        /* Informations de l'étudiant */
        .student-info-container {
            border: 0.5px solid #000; /* Bordure plus fine */
            padding: 1px; /* Padding réduit */
            margin-bottom: 1px; /* Marge réduite */
        }
        .student-info {
            width: 100%;
            margin-bottom: 1px; /* Marge réduite */
        }
        .student-table {
            width: 100%;
        }
        .student-table-left {
            width: 50%;
            float: left;
        }
        .student-table-right {
            width: 50%;
            float: right;
        }
        .student-table td {
            padding: 0px 1px; /* Padding réduit */
            font-size: 6pt; /* Taille de police réduite */
        }
        .fw-bold {
            font-weight: bold;
        }

        /* Tableaux des notes */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px; /* Marge réduite */
        }
        .grades-table th,
        .grades-table td {
            border: 0.5px solid #000; /* Bordure plus fine */
            padding: 1px 0px; /* Padding réduit */
            text-align: center;
            font-size: 6pt; /* Taille de police réduite */
        }
        .grades-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .section-header {
            background-color: #000;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 0px; /* Padding réduit */
        }
        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Absences */
        .absence-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px; /* Marge réduite */
        }
        .absence-table th,
        .absence-table td {
            border: 0.5px solid #000; /* Bordure plus fine */
            padding: 0px; /* Padding réduit */
            text-align: center;
            font-size: 6pt; /* Taille de police réduite */
        }

        /* Résultats */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1px; /* Marge réduite */
        }
        .results-table td {
            border: 0.5px solid #000; /* Bordure plus fine */
            padding: 0px; /* Padding réduit */
            font-size: 6pt; /* Taille de police réduite */
        }
        .result-value {
            text-align: center;
            font-weight: bold;
        }
        .result-cadre {
            border: 0.5px solid #000; /* Bordure plus fine */
            padding: 0px 1px; /* Padding réduit */
            display: inline-block;
        }

        /* Décision et signature */
        .decision-section {
            width: 100%;
            margin-top: 2px; /* Marge réduite */
            display: table;
        }
        .decision-title,
        .signature-title {
            font-weight: bold;
            font-size: 6pt; /* Taille de police réduite */
            margin-bottom: 1px; /* Marge réduite */
            text-align: center;
        }
        .decision-result {
            font-weight: bold;
            font-size: 7pt; /* Taille de police réduite */
            margin-top: 1px; /* Marge réduite */
            font-style: italic;
            text-align: center;
        }
        .decision-col,
        .signature-col {
            width: 33%;
            display: table-cell;
            text-align: center;
            font-size: 6pt; /* Taille de police réduite */
        }

        /* Pied de page */
        .footer {
            margin-top: 1px; /* Marge réduite */
            font-size: 5pt; /* Taille de police réduite */
            text-align: center;
            font-style: italic;
        }

        /* Contrôle de la mise en page */
        @page {
            size: A4 portrait;
            margin: 0.1cm; /* Marge très réduite */
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
                        <div style="height: 40px; width: 90px; text-align: center; border: 1px dashed #ccc;">Logo ESBTP</div>
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
                        <td>{{ $bulletin->effectif_classe ?? '0' }}</td>
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
                        <td style="text-align: left;">{{ $resultat->matiere && $resultat->matiere->nom ? $resultat->matiere->nom : 'Matière non définie' }}</td>
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
                    <td style="text-align: left;">Moyenne enseignement général</td>
                    <td colspan="6">{{ number_format($moyenneGeneraux, 2, '.', '') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Tableau des notes - Enseignement Technique -->
        <table class="grades-table">
            <thead>
                <tr class="section-header">
                    <td colspan="7">Enseignement Technique</td>
                </tr>
            </thead>
            <tbody>
                @forelse($resultatsTechniques as $resultat)
                    <tr>
                        <td style="text-align: left;">{{ $resultat->matiere && $resultat->matiere->nom ? $resultat->matiere->nom : 'Matière non définie' }}</td>
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
        </table>

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
                    <td>{{ $absencesJustifiees ?? '00' }} Heure(s)</td>
                </tr>
                <tr>
                    <td>Absences non justifiées</td>
                    <td>{{ $absencesNonJustifiees ?? '00' }} Heure(s)</td>
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
                    <td>Note d'assiduité</td>
                    <td class="result-value"><span class="result-cadre">{{ $noteAssiduite ?? '0.00' }}</span></td>
                    <td style="text-align: center;">Félicitation</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Félicitation') ? 'X' : '' }}</span></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Moyenne 1er Semestre</td>
                    <td class="result-value"><span class="result-cadre">{{ $moyenneSemestre1 ?? '0.00' }}</span></td>
                    <td style="text-align: center;">Encouragement</td>
                    <td><span class="result-cadre">{{ (isset($bulletin->mention) && $bulletin->mention == 'Encouragement') ? 'X' : '' }}</span></td>
                    <td>Plus forte moyenne</td>
                    <td><span class="result-cadre">{{ $plusForteMoyenne ?? '0.00' }}</span></td>
                </tr>
                <tr>
                    <td>Rang</td>
                    <td class="result-value"><span class="result-cadre">{{ $bulletin->rang ?? 'N/A' }}</span></td>
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

        <!-- Signatures et décision -->
        <div class="decision-section">
            <div class="decision-col">
                <p class="decision-title">Décision du conseil de classe</p>
                <p class="decision-result">{{ $bulletin->decision_conseil ?? 'Travail Insuffisant' }}</p>
            </div>
            <div class="signature-col">
                <p class="signature-title">Signature de la Directrice des Etudes</p>
                @if(isset($bulletin->signature_direction) && $bulletin->signature_direction)
                    <img src="{{ public_path('img/signatures/direction.png') }}" height="40" alt="Signature Direction">
                @endif
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>Bulletin informatisé, aucun duplicata n'est délivré</p>
    </div>
</body>
</html>
