@extends('layouts.app')

@section('title', 'Tableau de bord Super Admin')

@section('content')
<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Tableau de bord</h1>
            <p class="page-subtitle">Gestion administrative ESBTP-yAKRO</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Actualiser">
                <i class="fas fa-sync-alt"></i>
            </button>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-plus me-2"></i> Actions rapides
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="{{ route('esbtp.etudiants.create') }}"><i class="fas fa-user-plus me-2"></i> Nouvel étudiant</a></li>
                    <li><a class="dropdown-item" href="{{ route('esbtp.evaluations.create') }}"><i class="fas fa-file-alt me-2"></i> Créer examen</a></li>
                    <li><a class="dropdown-item" href="{{ route('esbtp.annonces.create') }}"><i class="fas fa-bullhorn me-2"></i> Publier annonce</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('esbtp.bulletins.generate') }}"><i class="fas fa-print me-2"></i> Générer bulletins</a></li>
                </ul>
            </div>
        </div>
    </div>

    @php
        $pendingInscriptionsCount = \App\Models\ESBTPInscription::where('status', 'pending')->count();
    @endphp

    @if($pendingInscriptionsCount > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
            <div>
                <strong>Attention!</strong> Il y a {{ $pendingInscriptionsCount }} inscription(s) en attente de validation.
                <p class="mb-0">Ces inscriptions nécessitent votre vérification pour finaliser le processus d'admission des étudiants.</p>
                <a href="{{ route('esbtp.inscriptions.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning mt-2">
                    <i class="fas fa-check-circle me-1"></i> Consulter et valider
                </a>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Stat Cards -->
    <div class="row dashboard-stats">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-icon bg-primary-light text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ $totalStudents ?? 0 }}</div>
                    <div class="stat-card-label">Étudiants</div>
                    <div class="stat-card-change positive">
                        <i class="fas fa-arrow-up"></i> 4.25%
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-icon bg-success-light text-success">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ $totalFilieres ?? 0 }}</div>
                    <div class="stat-card-label">Filières</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-icon bg-warning-light text-warning">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ $totalMatieres ?? 0 }}</div>
                    <div class="stat-card-label">Matières</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-card-icon bg-info-light text-info">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ $totalClasses ?? 0 }}</div>
                    <div class="stat-card-label">Classes</div>
                    <div class="stat-card-change positive">
                        <i class="fas fa-arrow-up"></i> 2.8%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title">Statistiques des inscriptions</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="chartOptions" data-bs-toggle="dropdown" aria-expanded="false">
                            Mensuel
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chartOptions">
                            <li><a class="dropdown-item active" href="#">Mensuel</a></li>
                            <li><a class="dropdown-item" href="#">Trimestriel</a></li>
                            <li><a class="dropdown-item" href="#">Annuel</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="inscriptionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Overview -->
        <div class="col-xl-4 col-lg-5">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title">Paiements</h5>
                    <a href="{{ route('esbtp.comptabilite.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <div class="text-center">
                            <h6 class="text-muted mb-1">Total payé</h6>
                            <h4 class="mb-0 text-success">{{ number_format(45070000, 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="text-center">
                            <h6 class="text-muted mb-1">Montant dû</h6>
                            <h4 class="mb-0 text-danger">{{ number_format(32400000, 0, ',', ' ') }} FCFA</h4>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="paymentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Recent Inscriptions -->
        <div class="col-xl-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Inscriptions récentes</h5>
                    <a href="{{ route('esbtp.inscriptions.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="nextadmin-table">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Filière</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>Konan Yves</div>
                                        </div>
                                    </td>
                                    <td>Informatique</td>
                                    <td>06/11/2023</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>Touré Fatima</div>
                                        </div>
                                    </td>
                                    <td>Comptabilité</td>
                                    <td>04/11/2023</td>
                                    <td><span class="badge bg-warning text-dark">En attente</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>Diallo Mohamed</div>
                                        </div>
                                    </td>
                                    <td>Électronique</td>
                                    <td>03/11/2023</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>Koffi Anne</div>
                                        </div>
                                    </td>
                                    <td>Génie Civil</td>
                                    <td>01/11/2023</td>
                                    <td><span class="badge bg-success">Validé</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('esbtp.inscriptions.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-right me-1"></i> Voir toutes les inscriptions
                    </a>
                </div>
            </div>
        </div>

        <!-- Upcoming Exams -->
        <div class="col-xl-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Examens à venir</h5>
                    <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="nextadmin-table">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Classe</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mathématiques</td>
                                    <td>1ère année Informatique</td>
                                    <td>15/11/2023</td>
                                    <td>
                                        <button class="table-action-btn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Programmation Java</td>
                                    <td>2ème année Informatique</td>
                                    <td>17/11/2023</td>
                                    <td>
                                        <button class="table-action-btn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Analyse financière</td>
                                    <td>3ème année Comptabilité</td>
                                    <td>18/11/2023</td>
                                    <td>
                                        <button class="table-action-btn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Résistance des matériaux</td>
                                    <td>2ème année Génie Civil</td>
                                    <td>20/11/2023</td>
                                    <td>
                                        <button class="table-action-btn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-right me-1"></i> Voir tous les examens
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Inscriptions Chart
        const inscriptionsCtx = document.getElementById('inscriptionsChart').getContext('2d');
        const inscriptionsChart = new Chart(inscriptionsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'Inscriptions',
                        data: [30, 40, 35, 50, 49, 60, 70, 91, 125, 85, 60, 45],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Validations',
                        data: [20, 35, 30, 45, 40, 55, 65, 85, 115, 80, 50, 40],
                        borderColor: '#22c55e',
                        tension: 0.4,
                        borderDash: [],
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Payments Chart
        const paymentsCtx = document.getElementById('paymentsChart').getContext('2d');
        const paymentsChart = new Chart(paymentsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'Montant reçu',
                        data: [2500000, 3200000, 3500000, 4000000, 4200000, 3800000, 3200000, 3700000, 4500000, 5000000, 4800000, 3000000],
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Montant dû',
                        data: [2000000, 2700000, 3000000, 3500000, 3700000, 3300000, 2700000, 3200000, 4000000, 4500000, 4300000, 2500000],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000) + 'M';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
