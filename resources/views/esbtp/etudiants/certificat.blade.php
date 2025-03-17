<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat de Scolarité</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Styles pour le certificat de scolarité */
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #fff;
            color: #000;
            padding: 20px 0;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
        }

        /* En-tête avec logo */
        .header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            margin-right: 20px;
        }

        .header-content {
            flex: 1;
        }

        .header-title {
            color: #00a651;
            font-weight: bold;
            text-align: center;
            font-size: 1.5rem;
            border-bottom: 2px solid #00a651;
            padding-bottom: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .contact-info {
            font-size: 0.8rem;
            text-align: center;
        }

        /* Séparateur */
        .divider {
            height: 10px;
            background: repeating-linear-gradient(45deg, #888, #888 10px, #fff 10px, #fff 20px);
            margin: 15px 0;
        }

        /* Titre du certificat */
        .certificate-title {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            border: 3px double #000;
            border-radius: 10px;
            padding: 10px;
            margin: 20px auto;
            max-width: 90%;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            background-color: #fff;
            position: relative;
            text-transform: uppercase;
        }

        .certificate-title::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            border: 1px solid #000;
            border-radius: 15px;
            z-index: -1;
            opacity: 0.5;
        }

        /* Contenu principal */
        .main-content {
            line-height: 1.6;
        }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Signature */
        .signature {
            text-align: right;
            margin-top: 20px;
            padding-right: 50px;
            position: relative;
        }

        /* Note de bas de page */
        .footer-note {
            font-size: 0.8rem;
            font-style: italic;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        /* Responsive */
        @media print {
            body {
                padding: 0;
            }

            .container {
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('images/logo.jpeg') }}" alt="Logo ESBTP" class="logo">
            <div class="header-content">
                <div class="header-title">
                    ÉCOLE SPÉCIALE<br>
                    DU BÂTIMENT ET DES TRAVAUX PUBLICS
                </div>
                <div class="contact-info">
                    <strong>SIÈGE SOCIAL:</strong> BP 2541 YAMOUSSOUKRO – QUARTIER N'ZUESSY – LOT N° 15<br>
                    <strong>TÉL:</strong> 30 64 39 93 - <strong>FAX:</strong> 30 64 39 93 - <strong>CEL:</strong> 05 93 34 26 / 07 72 88 56<br>
                    <strong>SITE WEB:</strong> www.esbtp-ci.net <strong>FACEBOOK:</strong> esbtp-CI - <strong>E-MAIL:</strong> esbtp.yamoussoukro.abidjan@gmail.com
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Certificate Title -->
        <div class="certificate-title">
            CERTIFICAT DE SCOLARITÉ
        </div>

        <!-- Certificate Content -->
        <div class="main-content">
            <p>Je soussigné(e) la Directrice des Etudes de l'Ecole Spéciale du Bâtiment et des Travaux publics (ESBTP) certifie que :</p>

            <p class="mt-4 mb-2">Mme, M., Mlle <strong>{{ $etudiant->nom_complet }}</strong></p>
            <p class="mb-2">Né(e) le <strong>{{ $etudiant->date_naissance->format('d/m/Y') }}</strong> à <strong>{{ $etudiant->lieu_naissance }}</strong></p>
            <p class="mb-4">Sous le matricule : <strong>{{ $etudiant->matricule }}</strong></p>

            <p>Est régulièrement inscrit(e) sur le registre des effectifs de l'année académique :</p>

            <table>
                <thead>
                    <tr>
                        <th>Année scolaire</th>
                        <th>Classe suivie</th>
                        <th>Filière</th>
                        <th>Moyenne/20</th>
                    </tr>
                </thead>
                <tbody>
                    @if($inscriptions->isEmpty())
                    <tr>
                        <td>
                            @if($etudiant->classe && $etudiant->classe->anneeUniversitaire)
                                {{ $etudiant->classe->anneeUniversitaire->libelle }}
                            @else
                                {{ date('Y').'-'.(date('Y')+1) }}
                            @endif
                        </td>
                        <td>
                            @if($etudiant->classe)
                                {{ $etudiant->classe->code }}
                            @else
                                BTS{{ $etudiant->niveau ?? '1' }}
                            @endif
                        </td>
                        <td>
                            @if($etudiant->classe && $etudiant->classe->filiere)
                                {{ $etudiant->classe->filiere->libelle }}
                            @elseif($etudiant->filiere)
                                {{ $etudiant->filiere->libelle }}
                            @else
                                GÉNIE CIVIL
                            @endif
                        </td>
                        <td>En cours</td>
                    </tr>
                    @else
                    @foreach($inscriptions as $inscription)
                    <tr>
                        <td>
                            @if($inscription->anneeUniversitaire)
                                {{ $inscription->anneeUniversitaire->libelle }}
                            @else
                                {{ date('Y').'-'.(date('Y')+1) }}
                            @endif
                        </td>
                        <td>
                            @if($inscription->classe)
                                {{ $inscription->classe->code }}
                            @else
                                BTS{{ $etudiant->niveau ?? '1' }}
                            @endif
                        </td>
                        <td>
                            @if($inscription->filiere)
                                {{ $inscription->filiere->libelle }}
                            @elseif($etudiant->filiere)
                                {{ $etudiant->filiere->libelle }}
                            @else
                                GÉNIE CIVIL
                            @endif
                        </td>
                        <td>{{ isset($moyennes[$inscription->annee_universitaire_id]) ? number_format($moyennes[$inscription->annee_universitaire_id], 2) : 'En cours' }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

            <p>Suivant l'horaire du programme complet.</p>

            <p><strong>Appréciations générales :</strong><br>
            @php
                // Initialize variables with default values
                $moyenne = 0;
                $appreciation = "PASSABLE";

                // Calculate average and appreciation if there are inscriptions with grades
                if(!$inscriptions->isEmpty()) {
                    // Get the most recent inscription
                    $lastInscription = $inscriptions->first();

                    // Get the average for the most recent academic year
                    if(isset($moyennes[$lastInscription->annee_universitaire_id])) {
                        $moyenne = $moyennes[$lastInscription->annee_universitaire_id];

                        // Determine appreciation based on average
                        if ($moyenne >= 16) {
                            $appreciation = "TRÈS BIEN";
                        } elseif ($moyenne >= 14) {
                            $appreciation = "BIEN";
                        } elseif ($moyenne >= 12) {
                            $appreciation = "ASSEZ BIEN";
                        } elseif ($moyenne >= 10) {
                            $appreciation = "PASSABLE";
                        } else {
                            $appreciation = "INSUFFISANT";
                        }
                    }
                }
            @endphp
            Travail : <em>{{ $appreciation }}</em><br>
            Conduite : <em>BONNE</em></p>

            <p>En foi de quoi, le présent certificat lui est délivré pour servir et valoir ce que de droit.</p>

            <div class="signature">
                @php
                    // Set locale to French
                    setlocale(LC_TIME, 'fr_FR.utf8', 'fra');
                    // Format date in French
                    $date_fr = strftime('%e %B %Y', strtotime($date_generation));
                    // Replace English month with French month if strftime doesn't work
                    $months_en = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    $months_fr = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
                    $date_fr = str_replace($months_en, $months_fr, $date_generation->format('j F Y'));
                @endphp
                <p>Fait à Yamoussoukro, le {{ $date_fr }}</p>
                <p>La Directrice des Etudes</p>
                <div style="height: 120px; position: relative;">
                    <img src="{{ public_path('images/certificat.jpeg') }}" alt="Cachet officiel" style="max-width: 250px; position: absolute; right: 0; top: 0; opacity: 0.9; transform: rotate(-5deg);">
                </div>
                <p><strong>MANGOUA Nadège</strong></p>
            </div>
        </div>

        <p class="footer-note"><strong>NB :</strong> les ratures, grattages, surcharges ou omissions conduisent à la nullité du présent certificat. Toute falsification sera punie par les peines prévues par la loi</p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
