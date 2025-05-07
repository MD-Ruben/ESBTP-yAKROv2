<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche de Paie - {{ $salaire->user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            float: left;
            max-width: 150px;
        }
        .company-info {
            float: right;
            text-align: right;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .employee-details {
            width: 100%;
            margin-bottom: 20px;
        }
        .employee-details td {
            padding: 5px 10px;
        }
        .employee-details td:first-child {
            font-weight: bold;
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .final-total {
            background-color: #e6e6e6;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ccc;
            font-size: 10px;
            text-align: center;
        }
        .signature {
            margin-top: 50px;
            margin-bottom: 30px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 50px;
            text-align: center;
        }
        .signature-section {
            display: inline-block;
            width: 45%;
            vertical-align: top;
        }
        .float-left {
            float: left;
        }
        .float-right {
            float: right;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <div class="logo">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo ESBTP" width="120">
            </div>
            <div class="company-info">
                <h3>ESBTP YAKRO</h3>
                <p>École Supérieure du Bâtiment et des Travaux Publics</p>
                <p>BP 123, Yamoussoukro, Côte d'Ivoire</p>
                <p>Tél: +225 XX XX XX XX</p>
                <p>Email: contact@esbtpyakro.ci</p>
            </div>
        </div>

        <div class="title">BULLETIN DE PAIE</div>
        <div class="subtitle">Période : 
            @php
                $mois = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            @endphp
            {{ $mois[$salaire->mois] }} {{ $salaire->annee }}
        </div>

        <table class="employee-details">
            <tr>
                <td>Nom et Prénom :</td>
                <td>{{ $salaire->user->name }}</td>
                <td>Fonction :</td>
                <td>{{ $salaire->user->employee_role ?? 'Non spécifiée' }}</td>
            </tr>
            <tr>
                <td>Numéro matricule :</td>
                <td>{{ $salaire->user->employee_id ?? 'N/A' }}</td>
                <td>Date d'embauche :</td>
                <td>{{ $salaire->user->hire_date ? date('d/m/Y', strtotime($salaire->user->hire_date)) : 'N/A' }}</td>
            </tr>
            <tr>
                <td>Date de paiement :</td>
                <td>{{ $salaire->date_paiement ? date('d/m/Y', strtotime($salaire->date_paiement)) : 'Non payé' }}</td>
                <td>Mode de paiement :</td>
                <td>{{ $salaire->mode_paiement ?? 'Virement bancaire' }}</td>
            </tr>
        </table>

        <!-- Calcul des totaux -->
        @php
            $totalBrut = $salaire->salaire_base + 
                        ($salaire->heures_supplementaires ?? 0) + 
                        ($salaire->primes ?? 0) + 
                        ($salaire->indemnites ?? 0);
            
            $totalRetenues = ($salaire->retenues ?? 0) + 
                            ($salaire->charges_sociales ?? 0) + 
                            ($salaire->impots ?? 0);
            
            $salairenet = $totalBrut - $totalRetenues;
        @endphp

        <table>
            <thead>
                <tr>
                    <th colspan="4" class="text-center">DÉTAIL DU SALAIRE</th>
                </tr>
                <tr>
                    <th>Désignation</th>
                    <th class="text-right">Base</th>
                    <th class="text-right">Taux</th>
                    <th class="text-right">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Partie Rémunérations -->
                <tr>
                    <td colspan="4" style="background-color: #e0e0e0; font-weight: bold;">ÉLÉMENTS DE RÉMUNÉRATION</td>
                </tr>
                <tr>
                    <td>Salaire de base</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($salaire->salaire_base, 0, ',', ' ') }}</td>
                </tr>
                @if($salaire->heures_supplementaires > 0)
                <tr>
                    <td>Heures supplémentaires</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($salaire->heures_supplementaires, 0, ',', ' ') }}</td>
                </tr>
                @endif
                @if($salaire->primes > 0)
                <tr>
                    <td>Primes</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($salaire->primes, 0, ',', ' ') }}</td>
                </tr>
                @endif
                @if($salaire->indemnites > 0)
                <tr>
                    <td>Indemnités</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($salaire->indemnites, 0, ',', ' ') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3">TOTAL BRUT</td>
                    <td class="text-right">{{ number_format($totalBrut, 0, ',', ' ') }}</td>
                </tr>

                <!-- Partie Retenues -->
                <tr>
                    <td colspan="4" style="background-color: #e0e0e0; font-weight: bold;">RETENUES</td>
                </tr>
                @if($salaire->retenues > 0)
                <tr>
                    <td>Retenues diverses</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($salaire->retenues, 0, ',', ' ') }}</td>
                </tr>
                @endif
                @if($salaire->charges_sociales > 0)
                <tr>
                    <td>Charges sociales</td>
                    <td class="text-right">{{ number_format($totalBrut, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format(($salaire->charges_sociales / $totalBrut) * 100, 2, ',', ' ') }}%</td>
                    <td class="text-right">{{ number_format($salaire->charges_sociales, 0, ',', ' ') }}</td>
                </tr>
                @endif
                @if($salaire->impots > 0)
                <tr>
                    <td>Impôts sur le revenu</td>
                    <td class="text-right">{{ number_format($totalBrut - $salaire->charges_sociales, 0, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format(($salaire->impots / ($totalBrut - $salaire->charges_sociales)) * 100, 2, ',', ' ') }}%</td>
                    <td class="text-right">{{ number_format($salaire->impots, 0, ',', ' ') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3">TOTAL RETENUES</td>
                    <td class="text-right">{{ number_format($totalRetenues, 0, ',', ' ') }}</td>
                </tr>

                <!-- Salaire net -->
                <tr class="final-total">
                    <td colspan="3">NET À PAYER</td>
                    <td class="text-right">{{ number_format($salairenet, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tbody>
        </table>

        <!-- Partie observations si commentaire présent -->
        @if($salaire->commentaire)
        <div style="margin-top: 20px; border: 1px solid #ccc; padding: 10px;">
            <h4 style="margin-top: 0;">Observations</h4>
            <p>{{ $salaire->commentaire }}</p>
        </div>
        @endif

        <!-- Signature -->
        <div class="signature clearfix">
            <div class="signature-section float-left">
                <p>L'employeur</p>
                <div class="signature-line"></div>
                <p>Directeur ESBTP YAKRO</p>
            </div>

            <div class="signature-section float-right">
                <p>Signature de l'employé</p>
                <div class="signature-line"></div>
                <p>{{ $salaire->user->name }}</p>
            </div>
        </div>

        <div class="footer">
            <p>Ce bulletin de paie doit être conservé sans limitation de durée.</p>
            <p>ESBTP YAKRO - École Supérieure du Bâtiment et des Travaux Publics - {{ date('Y') }} &copy; Tous droits réservés.</p>
        </div>
    </div>
</body>
</html> 