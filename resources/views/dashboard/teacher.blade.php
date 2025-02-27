@extends('layouts.app')

@section('title', 'Tableau de bord enseignant')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 bg-gradient-primary text-white overflow-hidden animate__animated animate__fadeIn" 
                 style="background: linear-gradient(135deg, var(--esbtp-green), var(--esbtp-green-dark)); border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="display-5 fw-bold mb-3 animate__animated animate__fadeInUp">Bienvenue, {{ Auth::user()->name }}</h2>
                            <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1">Gérez vos cours, étudiants et activités pédagogiques depuis votre tableau de bord enseignant.</p>
                            <div class="d-flex gap-2 animate__animated animate__fadeInUp animate__delay-2">
                                <a href="{{ route('grades.index') }}" class="btn btn-light px-4 py-2">
                                    <i class="fas fa-graduation-cap me-2"></i> Gérer les notes
                                </a>
                                <a href="{{ route('attendances.mark') }}" class="btn btn-outline-light px-4 py-2">
                                    <i class="fas fa-clipboard-check me-2"></i> Faire l'appel
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 d-none d-md-block position-relative">
                            <div class="position-absolute top-0 end-0 mt-3 me-4 animate__animated animate__fadeInRight">
                                <div class="d-flex align-items-center">
                                    <div class="bg-white rounded-circle p-3 me-3 shadow-sm">
                                        <i class="fas fa-calendar-alt text-success fa-2x"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-white">{{ now()->format('d M Y') }}</h6>
                                        <small class="text-white-50">{{ now()->format('l') }}</small>
                                    </div>
                                </div>
                            </div>
                            <img src="https://img.freepik.com/free-vector/teacher-concept-illustration_114360-2166.jpg" 
                                 alt="Teacher" class="img-fluid rounded-3 mt-3 animate__animated animate__fadeInUp" style="max-height: 200px; opacity: 0.9;">
                        </div>
                    </div>
                </div>
                <div class="position-absolute bottom-0 end-0 mb-n3 me-n3 d-none d-lg-block">
                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="100" r="100" fill="rgba(255,255,255,0.1)"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card animate__animated animate__fadeInUp">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Mes classes</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $classesTaught ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-primary-light rounded-circle p-3">
                            <i class="fas fa-chalkboard text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                    <a href="{{ route('timetables.index') }}" class="stretched-link text-decoration-none">
                        <div class="d-flex align-items-center mt-3">
                            <small class="text-primary fw-semibold">Voir l'emploi du temps</small>
                            <i class="fas fa-arrow-right ms-auto text-primary"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card animate__animated animate__fadeInUp animate__delay-1">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Étudiants</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $totalStudents ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-warning-light rounded-circle p-3">
                            <i class="fas fa-user-graduate text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                    </div>
                    <a href="{{ route('students.index') }}" class="stretched-link text-decoration-none">
                        <div class="d-flex align-items-center mt-3">
                            <small class="text-warning fw-semibold">Voir tous</small>
                            <i class="fas fa-arrow-right ms-auto text-warning"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card animate__animated animate__fadeInUp animate__delay-2">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Notifications</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $unreadNotifications ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-success-light rounded-circle p-3">
                            <i class="fas fa-bell text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="stretched-link text-decoration-none">
                        <div class="d-flex align-items-center mt-3">
                            <small class="text-success fw-semibold">Voir toutes</small>
                            <i class="fas fa-arrow-right ms-auto text-success"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Emploi du temps et Notifications -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <i class="fas fa-calendar-day text-primary me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Emploi du temps d'aujourd'hui</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($todayTimetable) && $todayTimetable->count() > 0)
                        <div class="timetable-list">
                            @foreach($todayTimetable as $entry)
                                <div class="timetable-item p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 text-center">
                                            <div class="time-badge bg-light rounded-pill px-3 py-2 d-inline-block">
                                                {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }}
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h6 class="mb-1 fw-semibold">{{ $entry->subject->name }}</h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-users me-1"></i> {{ $entry->class->name }} 
                                                <span class="mx-2">|</span> 
                                                <i class="fas fa-layer-group me-1"></i> {{ $entry->section->name }}
                                            </p>
                                        </div>
                                        <div class="col-md-2 text-end">
                                            <span class="badge bg-primary-light text-primary">
                                                {{ \Carbon\Carbon::parse($entry->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($entry->end_time)->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-calendar-check fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">Aucun cours programmé aujourd'hui</h6>
                            <p class="text-muted small mb-0">Profitez de votre journée libre</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white py-3">
                    <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-calendar-alt me-2"></i> Voir l'emploi du temps complet
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-1">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bell text-warning me-2"></i>
                        <h5 class="card-title mb-0 fw-bold">Notifications récentes</h5>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-warning rounded-pill">
                        Voir toutes
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentNotifications) && $recentNotifications->count() > 0)
                        <div class="notification-list">
                            @foreach($recentNotifications as $notification)
                                <div class="notification-item p-3 border-bottom">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="notification-icon bg-light rounded-circle p-2">
                                                <i class="fas fa-info-circle text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 fw-semibold">{{ $notification->title }}</h6>
                                            <p class="mb-1 text-muted small">{{ Str::limit($notification->message, 80) }}</p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                <span class="ms-auto">
                                                    <a href="#" class="btn btn-sm btn-link text-decoration-none p-0">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">Aucune notification récente</h6>
                            <p class="text-muted small mb-0">Les nouvelles notifications apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp animate__delay-2">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('attendances.mark') }}" class="card action-card border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body text-center p-4">
                                    <div class="action-icon bg-primary-light rounded-circle mx-auto mb-3">
                                        <i class="fas fa-clipboard-check text-primary"></i>
                                    </div>
                                    <h6 class="fw-semibold">Faire l'appel</h6>
                                    <p class="text-muted small mb-0">Marquer les présences des étudiants</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('grades.index') }}" class="card action-card border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body text-center p-4">
                                    <div class="action-icon bg-success-light rounded-circle mx-auto mb-3">
                                        <i class="fas fa-graduation-cap text-success"></i>
                                    </div>
                                    <h6 class="fw-semibold">Saisir des notes</h6>
                                    <p class="text-muted small mb-0">Gérer les notes des étudiants</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('notifications.create') }}" class="card action-card border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body text-center p-4">
                                    <div class="action-icon bg-warning-light rounded-circle mx-auto mb-3">
                                        <i class="fas fa-bell text-warning"></i>
                                    </div>
                                    <h6 class="fw-semibold">Envoyer notification</h6>
                                    <p class="text-muted small mb-0">Informer les étudiants et collègues</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('timetables.index') }}" class="card action-card border-0 shadow-sm h-100 text-decoration-none">
                                <div class="card-body text-center p-4">
                                    <div class="action-icon bg-info-light rounded-circle mx-auto mb-3">
                                        <i class="fas fa-calendar-alt text-info"></i>
                                    </div>
                                    <h6 class="fw-semibold">Emploi du temps</h6>
                                    <p class="text-muted small mb-0">Consulter votre planning</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styles pour les cartes statistiques */
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    /* Styles pour les cartes d'action */
    .action-card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }
    
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    /* Styles pour les notifications */
    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notification-item {
        transition: all 0.2s ease;
    }
    
    .notification-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Styles pour l'emploi du temps */
    .timetable-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .timetable-item {
        transition: all 0.2s ease;
    }
    
    .timetable-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .time-badge {
        font-weight: 600;
        color: var(--esbtp-green);
    }
    
    /* Animation */
    .animate__delay-1 {
        animation-delay: 0.1s;
    }
    
    .animate__delay-2 {
        animation-delay: 0.2s;
    }
</style>
@endsection 