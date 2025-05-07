<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu de Paiement - {{ $paiement->numero_recu }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .receipt-number {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            border: 1px solid #333;
            padding: 5px;
            background-color: #f5f5f5;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-title {
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 40%;
            font-weight: bold;
        }
        .info-value {
            width: 60%;
        }
        .payment-details {
            margin: 20px 0;
            border: 1px solid #333;
            padding: 10px;
        }
        .payment-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
            background-color: #f5f5f5;
            padding: 5px;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            border-top: 1px solid #333;
            padding-top: 5px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/LOGO-KLASSCI-PNG.png') }}" alt="Logo KLASSCI" class="logo">
            <div class="title">Ecole Spéciale du Bâtiment et des Travaux Publics</div>
            <div class="subtitle">Reçu de Paiement</div>
        </div>

        <div class="receipt-number">
            REÇU N° {{ $paiement->numero_recu }}
        </div>

        <div class="info-section">
            <div class="info-title">Informations de l'Étudiant</div>
            <table>
                <tr>
                    <th width="40%">Matricule</th>
                    <td>{{ $paiement->etudiant->matricule }}</td>
                </tr>
                <tr>
                    <th>Nom et Prénoms</th>
                    <td>{{ $paiement->etudiant->user->name }}</td>
                </tr>
                <tr>
                    <th>Filière</th>
                    <td>{{ $paiement->inscription->filiere->name }}</td>
                </tr>
                <tr>
                    <th>Niveau</th>
                    <td>{{ $paiement->inscription->niveauEtude->name }}</td>
                </tr>
                <tr>
                    <th>Année Universitaire</th>
                    <td>{{ $paiement->inscription->anneeUniversitaire->libelle }}</td>
                </tr>
            </table>
        </div>

        <div class="payment-details">
            <div class="payment-title">Détails du Paiement</div>
            <table>
                <tr>
                    <th width="40%">Date de paiement</th>
                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Motif</th>
                    <td>{{ $paiement->motif }}</td>
                </tr>
                @if($paiement->tranche)
                <tr>
                    <th>Tranche</th>
                    <td>{{ $paiement->tranche }}</td>
                </tr>
                @endif
                <tr>
                    <th>Mode de paiement</th>
                    <td>{{ $paiement->mode_paiement }}</td>
                </tr>
                @if($paiement->reference_paiement)
                <tr>
                    <th>Référence</th>
                    <td>{{ $paiement->reference_paiement }}</td>
                </tr>
                @endif
            </table>

            <div class="amount">
                Montant: {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
            </div>

            <div style="text-align: center; font-style: italic; margin-top: 10px;">
                {{ ucfirst(\App\Services\NumberToWords::convert($paiement->montant)) }} Francs CFA
            </div>
        </div>

        <div class="signature">
            <div class="signature-box">
                <div>Date d'émission</div>
                <div>{{ $paiement->date_validation ? $paiement->date_validation->format('d/m/Y') : date('d/m/Y') }}</div>
            </div>

            <div class="signature-box">
                <div>Signature et Cachet</div>
                <div>{{ $paiement->validatedBy ? $paiement->validatedBy->name : 'Le Comptable' }}</div>
            </div>
        </div>

        <div class="footer">
            <p>Ce reçu est un document officiel. Toute falsification constitue un délit passible de poursuites judiciaires.</p>
            <p>ESBTP - BP 2541 Yamoussoukro - Email: esbtp@aviso.ci - Tél/Fax: 30 64 39 93 - Cel: 05 93 34 26 / 07 72 88 56</p>
        </div>
    </div>
</body>
</html>
