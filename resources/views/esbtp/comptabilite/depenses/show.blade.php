@extends('layouts.app')

@section('title', 'Détails de la dépense')

@section('styles')
<style>
    /* Variables CSS pour la cohérence des couleurs */
    :root {
        --primary-color: #0056b3;
        --secondary-color: #6c757d;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --white-color: #ffffff;
        --border-radius: 0.75rem;
        --box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    /* Style pour les cartes */
    .expense-card {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        overflow: hidden;
        height: 100%;
        margin-bottom: 1.5rem;
    }

    .expense-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.1);
    }

    .expense-header {
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .expense-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: linear-gradient(45deg, rgba(0, 0, 0, 0.1), transparent);
        z-index: 1;
    }

    .expense-header > * {
        position: relative;
        z-index: 2;
    }

    /* Style pour les icônes circulaires */
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    /* Style pour les badges de statut */
    .status-badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.75rem;
        border-radius: 50rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    /* Style pour les cartes de statistiques */
    .stat-card {
        border-radius: var(--border-radius);
        padding: 1.5rem;
        height: 100%;
        border: none;
        box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.05);
        transition: var(--transition);
        background: #f8f9fa;
        overflow: hidden;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        position: absolute;
        right: -15px;
        bottom: -15px;
        font-size: 6rem;
        opacity: 0.1;
        transform: rotate(-15deg);
        transition: var(--transition);
    }

    .stat-card:hover .stat-icon {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.15;
    }

    /* Style pour les boutons d'action */
    .action-btn {
        margin-bottom: 0.75rem;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        transition: var(--transition);
        font-weight: 600;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .action-btn::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        z-index: -1;
        transform: translateX(-100%);
        transition: var(--transition);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .action-btn:hover::after {
        transform: translateX(0);
    }

    /* Style pour les titres de section */
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-bottom: 1.25rem;
        color: var(--primary-color);
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .section-title i {
        margin-right: 0.75rem;
        background: var(--primary-color);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    /* Style pour les lignes d'information */
    .info-row {
        display: flex;
        margin-bottom: 1rem;
        align-items: flex-start;
    }

    .info-label {
        min-width: 150px;
        font-weight: 600;
        color: var(--secondary-color);
        display: flex;
        align-items: center;
    }

    .info-label i {
        margin-right: 0.5rem;
        opacity: 0.6;
        font-size: 0.9rem;
    }

    .info-value {
        flex: 1;
        font-weight: 500;
    }

    /* Style pour les tableaux d'informations */
    .info-table {
        width: 100%;
        margin-bottom: 1.5rem;
    }

    .info-table tr {
        transition: var(--transition);
    }

    .info-table tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .info-table th {
        font-weight: 600;
        color: var(--secondary-color);
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        width: 30%;
    }

    .info-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* Style pour le mode impression */
    @media print {
        .action-btn, .no-print {
            display: none !important;
        }

        .expense-card {
            box-shadow: none !important;
            margin-bottom: 1rem !important;
        }

        .expense-header {
            color: black !important;
            background: #f8f9fa !important;
        }

        .icon-circle {
            background-color: #e9ecef !important;
        }

        .section-title i {
            background-color: #e9ecef !important;
            color: var(--primary-color) !important;
        }
    }
</style>
@endsection

@section('content')
<?php
// Fonction pour déterminer la couleur en fonction du statut
function getStatusColor($status) {
    switch ($status) {
        case 'validée':
            return ['bg' => '#28a745', 'text' => 'white', 'badge' => 'success', 'icon' => 'fa-check-circle'];
        case 'en attente':
            return ['bg' => '#ffc107', 'text' => 'dark', 'badge' => 'warning', 'icon' => 'fa-clock'];
        case 'annulée':
            return ['bg' => '#dc3545', 'text' => 'white', 'badge' => 'danger', 'icon' => 'fa-ban'];
        default:
            return ['bg' => '#6c757d', 'text' => 'white', 'badge' => 'secondary', 'icon' => 'fa-info-circle'];
    }
}

// Fonction pour ajuster la luminosité d'une couleur hexadécimale
function adjustBrightness($color, $percent) {
    $color = ltrim($color, '#');
    if (strlen($color) == 3) {
        $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
    }

    $R = hexdec(substr($color, 0, 2));
    $G = hexdec(substr($color, 2, 2));
    $B = hexdec(substr($color, 4, 2));

    $R = max(0, min(255, $R + ($R * $percent / 100)));
    $G = max(0, min(255, $G + ($G * $percent / 100)));
    $B = max(0, min(255, $B + ($B * $percent / 100)));

    $RR = str_pad(dechex((int)$R), 2, "0", STR_PAD_LEFT);
    $GG = str_pad(dechex((int)$G), 2, "0", STR_PAD_LEFT);
    $BB = str_pad(dechex((int)$B), 2, "0", STR_PAD_LEFT);

    return '#' . $RR . $GG . $BB;
}

$statusColors = getStatusColor($depense->statut);
$gradientColor = adjustBrightness($statusColors['bg'], -20);
?>

<div class="container-fluid py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="fas fa-file-invoice-dollar me-2 text-primary"></i> Détails de la dépense
        </h4>
        <div class="no-print">
            <a href="{{ route('esbtp.comptabilite.depenses') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <a href="#" class="btn btn-outline-primary ms-2" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimer
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Carte principale d'informations -->
            <div class="expense-card">
                <div class="expense-header" style="background: linear-gradient(135deg, {{ $statusColors['bg'] }} 0%, {{ $gradientColor }} 100%); color: {{ $statusColors['text'] }};">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle">
                            <i class="fas {{ $statusColors['icon'] }} fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $depense->libelle }}</h5>
                            <div>
                                <span class="status-badge bg-{{ $statusColors['badge'] }}">
                                    <i class="fas {{ $statusColors['icon'] }} me-1"></i> {{ ucfirst($depense->statut) }}
                                </span>
                                <span class="ms-2 fs-6 opacity-75">
                                    <i class="fas fa-hashtag me-1"></i> {{ $depense->reference ?? 'N°'.$depense->id }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Montant et statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="stat-card">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="text-center">
                                        <div class="fs-1 fw-bold text-danger">
                                            {{ number_format($depense->montant, 0, ',', ' ') }} FCFA
                                        </div>
                                        <div class="text-muted mt-2">
                                            <i class="fas fa-calendar-alt me-1"></i> Dépense du {{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-money-bill-wave stat-icon text-danger"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Informations générales -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-info-circle"></i> Informations générales
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-row">
                                    <div class="info-label"><i class="fas fa-tag"></i> Catégorie</div>
                                    <div class="info-value">
                                        @if($depense->categorie)
                                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(0, 86, 179, 0.1); color: #0056b3;">
                                                {{ $depense->categorie->nom }}
                                            </span>
                                        @else
                                            <span class="text-muted">Non spécifiée</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label"><i class="fas fa-calendar-alt"></i> Date de dépense</div>
                                    <div class="info-value">{{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : 'Non spécifiée' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label"><i class="fas fa-credit-card"></i> Mode de paiement</div>
                                    <div class="info-value">
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                            {{ ucfirst($depense->mode_paiement ?? 'Non spécifié') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-row">
                                    <div class="info-label"><i class="fas fa-hashtag"></i> Référence</div>
                                    <div class="info-value">{{ $depense->reference ?? 'Non spécifiée' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label"><i class="fas fa-receipt"></i> N° transaction</div>
                                    <div class="info-value">{{ $depense->numero_transaction ?? 'Non spécifié' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label"><i class="fas fa-user"></i> Bénéficiaire</div>
                                    <div class="info-value">{{ $depense->beneficiaire ?? 'Non spécifié' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description détaillée -->
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-align-left"></i> Description
                        </div>
                        <div class="card border-0 bg-light rounded-3">
                            <div class="card-body">
                                <p class="mb-0">{{ $depense->description ?: 'Aucune description détaillée fournie.' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fournisseur -->
                    @if($depense->fournisseur)
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-building"></i> Informations du fournisseur
                        </div>
                        <div class="card border-0 bg-light rounded-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <div class="info-label"><i class="fas fa-id-card"></i> Nom</div>
                                            <div class="info-value fw-bold">{{ $depense->fournisseur->nom }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="fas fa-phone"></i> Téléphone</div>
                                            <div class="info-value">{{ $depense->fournisseur->telephone ?? 'Non spécifié' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                                            <div class="info-value">{{ $depense->fournisseur->email ?? 'Non spécifié' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="fas fa-map-marker-alt"></i> Adresse</div>
                                            <div class="info-value">{{ $depense->fournisseur->adresse ?? 'Non spécifiée' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('esbtp.comptabilite.fournisseurs.show', $depense->fournisseur->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i> Voir les détails du fournisseur
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes internes -->
                    @if($depense->notes_internes)
                    <div class="mb-4">
                        <div class="section-title">
                            <i class="fas fa-sticky-note"></i> Notes internes
                        </div>
                        <div class="card border-0 bg-light rounded-3">
                            <div class="card-body">
                                <p class="mb-0 fst-italic">{{ $depense->notes_internes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions -->
            <div class="expense-card no-print">
                <div class="expense-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Actions</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <a href="{{ route('esbtp.comptabilite.depenses.edit', $depense->id) }}" class="btn btn-primary w-100 action-btn">
                        <i class="fas fa-edit me-2"></i> Modifier cette dépense
                    </a>
                    
                    <a href="#" class="btn btn-success w-100 action-btn" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Imprimer ce justificatif
                    </a>
                    
                    @if($depense->path_justificatif)
                    <a href="{{ asset('storage/' . $depense->path_justificatif) }}" class="btn btn-info w-100 action-btn" target="_blank">
                        <i class="fas fa-file-alt me-2"></i> Voir le justificatif original
                    </a>
                    @endif
                    
                    <button type="button" class="btn btn-danger w-100 action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt me-2"></i> Supprimer cette dépense
                    </button>
                </div>
            </div>

            <!-- Informations système -->
            <div class="expense-card">
                <div class="expense-header bg-dark text-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle">
                            <i class="fas fa-server"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Informations système</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <table class="info-table">
                        <tr>
                            <th><i class="fas fa-fingerprint me-1 opacity-50"></i> ID</th>
                            <td><span class="badge bg-dark">{{ $depense->id }}</span></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user-plus me-1 opacity-50"></i> Créé par</th>
                            <td>{{ $depense->createur->name ?? 'Non spécifié' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-plus me-1 opacity-50"></i> Date de création</th>
                            <td>{{ $depense->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        
                        @if($depense->updated_at && $depense->updated_at->ne($depense->created_at))
                        <tr>
                            <th><i class="fas fa-edit me-1 opacity-50"></i> Dernière modification</th>
                            <td>{{ $depense->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                        
                        @if($depense->validateur)
                        <tr>
                            <th><i class="fas fa-user-check me-1 opacity-50"></i> Validé par</th>
                            <td>{{ $depense->validateur->name }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-calendar-check me-1 opacity-50"></i> Date de validation</th>
                            <td>{{ $depense->date_validation ? $depense->date_validation->format('d/m/Y H:i') : 'Non spécifiée' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Statistiques liées -->
            <div class="expense-card">
                <div class="expense-header bg-info text-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-circle">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">Informations complémentaires</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($depense->categorie)
                    <div class="alert alert-info rounded-3">
                        <i class="fas fa-info-circle me-2"></i> Cette dépense est classée dans la catégorie <strong>{{ $depense->categorie->nom }}</strong>.
                        @if($depense->categorie->description)
                        <div class="mt-2 small">{{ $depense->categorie->description }}</div>
                        @endif
                    </div>
                    @endif

                    <div class="alert alert-light rounded-3 border">
                        <p class="mb-0">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            Les détails de cette dépense sont disponibles en format imprimable en cliquant sur le bouton "Imprimer ce justificatif".
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette dépense ? Cette action est irréversible.</p>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i> La suppression entraînera la perte de toutes les données associées à cette dépense.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.comptabilite.depenses.destroy', $depense->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Confirmer la suppression
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 