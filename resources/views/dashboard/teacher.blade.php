@extends('layouts.app')

@section('title', 'Tableau de bord enseignant')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tableau de bord</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Accueil</li>
    </ol>

    <!-- Bienvenue -->
    <div class="card mb-4">
        <div class="card-body">
            <h2>Bienvenue, {{ Auth::user()->name }} !</h2>
            <p>Vous êtes connecté à l'application ESBTP-yAKRO.</p>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Mes séances</div>
                            <div class="text-lg fw-bold">{{ $attendanceStats['totalCourses'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-calendar fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('teacher.timetable') }}">Voir mon emploi du temps</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Taux de présence</div>
                            <div class="text-lg fw-bold">{{ number_format($attendanceStats['attendanceRate'] ?? 0, 1) }}%</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Voir les détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Notes à saisir</div>
                            <div class="text-lg fw-bold">3</div>
                        </div>
                        <i class="fas fa-edit fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Saisir des notes</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Messages</div>
                            <div class="text-lg fw-bold">7</div>
                        </div>
                        <i class="fas fa-envelope fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">Voir les messages</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <!-- Séances de cours à venir -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-calendar-alt me-1"></i>
                        Séances de cours à venir
                    </div>
                    <a href="{{ route('teacher.timetable') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Mon emploi du temps
                    </a>
                </div>
                <div class="card-body">
                    <div class="upcoming-classes">
                        @if(isset($upcomingClasses) && count($upcomingClasses) > 0)
                            @foreach($upcomingClasses as $seance)
                                <div class="class-item p-3 mb-3 border rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 text-center">
                                            <div class="date-badge rounded-pill px-3 py-2 bg-primary-light text-primary">
                                                {{ $seance->date->format('d/m') }}
                                                <div class="small">{{ $joursSemaine[$seance->jour] ?? '' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="mb-1 fw-semibold">{{ $seance->matiere->name ?? 'Matière non définie' }}</h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-users me-1"></i> {{ $seance->emploiTemps->classe->name ?? 'Classe non définie' }}
                                                <span class="ms-2">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $seance->salle ?? 'Salle non définie' }}
                                                </span>
                                                <span class="ms-2">
                                                    <i class="fas fa-tag me-1"></i>
                                                    <span class="badge {{ $seance->type_seance == 'cours' ? 'bg-primary' : ($seance->type_seance == 'td' ? 'bg-secondary' : 'bg-success') }}">
                                                        {{ strtoupper($seance->type_seance) }}
                                                    </span>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <span class="badge bg-primary">
                                                {{ Carbon\Carbon::parse($seance->heure_debut)->format('H:i') }} -
                                                {{ Carbon\Carbon::parse($seance->heure_fin)->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-check fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">Aucun cours programmé prochainement</h6>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="{{ route('teacher.timetable') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-calendar me-1"></i> Voir l'emploi du temps complet
                    </a>
                </div>
            </div>
        </div>

        <!-- Taux de présence -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Taux de présence
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="attendance-chart">
                                    <!-- Canvas pour le graphique circulaire -->
                                    <canvas id="attendanceChart" width="200" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="attendance-stats">
                                <div class="stat-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold">Total des cours</span>
                                        <span class="badge bg-primary">{{ $attendanceStats['totalCourses'] ?? 0 }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                                
                                <div class="stat-item mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold">Cours assurés</span>
                                        <span class="badge bg-success">{{ $attendanceStats['attendedCourses'] ?? 0 }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ isset($attendanceStats['attendedCourses']) && isset($attendanceStats['totalCourses']) && $attendanceStats['totalCourses'] > 0 ? ($attendanceStats['attendedCourses'] / $attendanceStats['totalCourses'] * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold">Cours manqués</span>
                                        <span class="badge bg-danger">{{ $attendanceStats['absentCourses'] ?? 0 }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: {{ isset($attendanceStats['absentCourses']) && isset($attendanceStats['totalCourses']) && $attendanceStats['totalCourses'] > 0 ? ($attendanceStats['absentCourses'] / $attendanceStats['totalCourses'] * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    Taux de présence global: {{ isset($attendanceStats['attendanceRate']) ? number_format($attendanceStats['attendanceRate'], 1) : '0' }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications ou alertes -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-bell me-1"></i>
                    Notifications
                </div>
                <div class="card-body">
                    @if(isset($notifications) && count($notifications) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Message</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notification)
                                        <tr>
                                            <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $notification->message }}</td>
                                            <td>
                                                <span class="badge bg-{{ $notification->type == 'urgent' ? 'danger' : 'info' }}">
                                                    {{ $notification->type }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            Aucune notification pour le moment.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuration du graphique de taux de présence
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('attendanceChart');
        
        if(ctx) {
            var attendanceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Présent', 'Absent'],
                    datasets: [{
                        data: [
                            {{ isset($attendanceStats['attendedCourses']) ? $attendanceStats['attendedCourses'] : 0 }}, 
                            {{ isset($attendanceStats['absentCourses']) ? $attendanceStats['absentCourses'] : 0 }}
                        ],
                        backgroundColor: ['#22c55e', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    cutout: '75%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${percentage}%`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection

@section('styles')
<style>
    .date-badge {
        background-color: rgba(99, 102, 241, 0.1);
        color: var(--nextadmin-primary);
        font-weight: 600;
    }
    
    .class-item {
        transition: all 0.3s ease;
        border-left: 4px solid var(--nextadmin-primary) !important;
    }
    
    .class-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection 