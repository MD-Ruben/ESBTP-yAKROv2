@extends('layouts.app')

@section('title', 'Rapport de présence')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-file-chart"></i>
            </span> Rapport de présence
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esbtp.attendances.index') }}">Présences</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rapport</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title">Rapport de présence - {{ $classe->name }}</h4>
                        <div>
                            <button class="btn btn-gradient-primary btn-sm" onclick="window.print()">
                                <i class="mdi mdi-printer"></i> Imprimer
                            </button>
                            <a href="{{ route('esbtp.attendances.rapport-form') }}" class="btn btn-gradient-info btn-sm">
                                <i class="mdi mdi-refresh"></i> Nouveau rapport
                            </a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($validatedData['date_debut'])->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($validatedData['date_fin'])->format('d/m/Y') }}</p>
                    </div>

                    <!-- Résumé des statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-gradient-success text-white">
                                <div class="card-body">
                                    <h5 class="mb-2" style="color: #000000; font-weight: bold;">Présences</h5>
                                    <h3 class="font-weight-bold mb-0" style="color: #00ff7f; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                        {{ collect($statistiques)->sum('present') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-danger text-white">
                                <div class="card-body">
                                    <h5 class="mb-2" style="color: #ffffff; font-weight: bold;">Absences</h5>
                                    <h3 class="font-weight-bold mb-0" style="color: #ff6b6b; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                        {{ collect($statistiques)->sum('absent') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-warning text-white">
                                <div class="card-body">
                                    <h5 class="mb-2" style="color: #000000; font-weight: bold;">Retards</h5>
                                    <h3 class="font-weight-bold mb-0" style="color: #ffd166; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                        {{ collect($statistiques)->sum('retard') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-info text-white">
                                <div class="card-body">
                                    <h5 class="mb-2" style="color: #000000; font-weight: bold;">Excusés</h5>
                                    <h3 class="font-weight-bold mb-0" style="color: #48bfe3; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">
                                        {{ collect($statistiques)->sum('excuse') }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des statistiques par étudiant -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th class="text-center">Présences</th>
                                    <th class="text-center">Absences</th>
                                    <th class="text-center">Retards</th>
                                    <th class="text-center">Excusés</th>
                                    <th class="text-center">Taux de présence</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($statistiques as $stat)
                                    <tr>
                                        <td>{{ $stat['etudiant']->nom_complet }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-success" style="font-size: 14px; padding: 8px 12px; background-color: #00ff7f !important; color: #000; font-weight: bold;">{{ $stat['present'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger" style="font-size: 14px; padding: 8px 12px; background-color: #ff6b6b !important; color: #fff; font-weight: bold;">{{ $stat['absent'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning" style="font-size: 14px; padding: 8px 12px; background-color: #ffd166 !important; color: #000; font-weight: bold;">{{ $stat['retard'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info" style="font-size: 14px; padding: 8px 12px; background-color: #48bfe3 !important; color: #000; font-weight: bold;">{{ $stat['excuse'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress">
                                                <div class="progress-bar {{ $stat['taux_presence'] > 75 ? 'bg-success' : ($stat['taux_presence'] > 50 ? 'bg-warning' : 'bg-danger') }}"
                                                    role="progressbar"
                                                    style="width: {{ $stat['taux_presence'] }}%"
                                                    aria-valuenow="{{ $stat['taux_presence'] }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    {{ $stat['taux_presence'] }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune donnée disponible</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Graphique -->
                    <div class="mt-4">
                        <h4 class="card-title">Statistiques globales</h4>
                        <div class="chart-container" style="position: relative; height:300px;">
                            <canvas id="presenceChart"></canvas>
                        </div>
                    </div>
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
        const ctx = document.getElementById('presenceChart').getContext('2d');

        // Calculer les totaux
        const presents = {{ collect($statistiques)->sum('present') }};
        const absents = {{ collect($statistiques)->sum('absent') }};
        const retards = {{ collect($statistiques)->sum('retard') }};
        const excuses = {{ collect($statistiques)->sum('excuse') }};

        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Présents', 'Absents', 'Retards', 'Excusés'],
                datasets: [{
                    data: [presents, absents, retards, excuses],
                    backgroundColor: [
                        'rgba(0, 255, 127, 0.7)',
                        'rgba(255, 107, 107, 0.7)',
                        'rgba(255, 209, 102, 0.7)',
                        'rgba(72, 191, 227, 0.7)'
                    ],
                    borderColor: [
                        'rgba(0, 255, 127, 1)',
                        'rgba(255, 107, 107, 1)',
                        'rgba(255, 209, 102, 1)',
                        'rgba(72, 191, 227, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Répartition des présences'
                    }
                }
            }
        });
    });
</script>
@endsection

@section('styles')
<style media="print">
    .btn, nav, .d-print-none {
        display: none !important;
    }

    .content-wrapper {
        margin: 0;
        padding: 0;
    }

    .card {
        box-shadow: none !important;
        border: none !important;
    }

    .chart-container {
        page-break-before: always;
    }
</style>
@endsection
