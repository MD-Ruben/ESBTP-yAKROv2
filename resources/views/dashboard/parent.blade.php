@extends('layouts.app')

@section('title', 'Tableau de bord parent')

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
                            <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1">Suivez la scolarité de vos enfants depuis votre tableau de bord parent.</p>
                            <div class="d-flex gap-2 animate__animated animate__fadeInUp animate__delay-2">
                                <a href="{{ route('children.index') }}" class="btn btn-light px-4 py-2">
                                    <i class="fas fa-users me-2"></i> Voir mes enfants
                                </a>
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-light px-4 py-2">
                                    <i class="fas fa-credit-card me-2"></i> Gérer les paiements
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
                            <img src="https://img.freepik.com/free-vector/happy-family-concept-illustration_114360-1549.jpg" 
                                 alt="Family" class="img-fluid rounded-3 mt-3 animate__animated animate__fadeInUp" style="max-height: 200px; opacity: 0.9;">
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
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Mes enfants</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $childrenCount ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-primary-light rounded-circle p-3">
                            <i class="fas fa-users text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                    <a href="{{ route('children.index') }}" class="stretched-link text-decoration-none">
                        <div class="d-flex align-items-center mt-3">
                            <small class="text-primary fw-semibold">Voir détails</small>
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
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Notifications</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $unreadNotifications ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-warning-light rounded-circle p-3">
                            <i class="fas fa-bell text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="stretched-link text-decoration-none">
                        <div class="d-flex align-items-center mt-3">
                            <small class="text-warning fw-semibold">Voir toutes</small>
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
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Paiements</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $pendingPayments ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-success-light rounded-circle p-3">
                            <i class="fas fa-credit-card text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                    <a href="{{ route('payments.index') }}" class="stretched-link text-decoration-none">
                        <div class="d-flex align-items-center mt-3">
                            <small class="text-success fw-semibold">Voir détails</small>
                            <i class="fas fa-arrow-right ms-auto text-success"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mes enfants -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <i class="fas fa-users text-primary me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Mes enfants</h5>
                </div>
                <div class="card-body">
                    @if(isset($children) && $children->count() > 0)
                        <div class="row g-3">
                            @foreach($children as $child)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card student-card border-0 shadow-sm h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-wrapper rounded-circle overflow-hidden bg-light" style="width: 70px; height: 70px;">
                                                        <img src="{{ $child->profile_photo ?? asset('images/default-avatar.png') }}" 
                                                             alt="{{ $child->name }}" class="w-100 h-100 object-fit-cover">
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="mb-1 fw-bold">{{ $child->name }}</h5>
                                                    <p class="mb-0 text-muted">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        {{ $child->class->name ?? 'N/A' }} {{ $child->section->name ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="student-stats mb-3">
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="stat-item p-2 rounded bg-light">
                                                            <small class="text-muted d-block">Moyenne</small>
                                                            <span class="badge {{ ($child->average_grade ?? 0) >= 10 ? 'bg-success' : 'bg-danger' }} rounded-pill px-2 py-1">
                                                                {{ $child->average_grade ?? 'N/A' }}/20
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="stat-item p-2 rounded bg-light">
                                                            <small class="text-muted d-block">Présence</small>
                                                            <span class="badge bg-primary rounded-pill px-2 py-1">
                                                                {{ $child->attendance_percentage ?? 'N/A' }}%
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('children.show', $child->id) }}" class="btn btn-primary w-100">
                                                <i class="fas fa-eye me-2"></i> Voir détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">Aucun enfant enregistré</h6>
                            <p class="text-muted small mb-0">Contactez l'administration pour ajouter vos enfants</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications et Paiements -->
    <div class="row mb-4">
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
        
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 animate__animated animate__fadeInUp animate__delay-2">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card text-success me-2"></i>
                        <h5 class="card-title mb-0 fw-bold">Paiements récents</h5>
                    </div>
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-outline-success rounded-pill">
                        Voir tous
                    </a>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentPayments) && $recentPayments->count() > 0)
                        <div class="payment-list">
                            @foreach($recentPayments as $payment)
                                <div class="payment-item p-3 border-bottom">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="payment-icon bg-light rounded-circle p-2">
                                                <i class="fas fa-receipt text-success"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 fw-semibold">{{ $payment->description }}</h6>
                                            <p class="mb-1 text-muted small">
                                                <i class="fas fa-user me-1"></i> {{ $payment->student->name }}
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <span class="badge {{ $payment->status === 'paid' ? 'bg-success' : 'bg-warning' }} rounded-pill">
                                                    {{ $payment->status === 'paid' ? 'Payé' : 'En attente' }}
                                                </span>
                                                <span class="ms-2 fw-semibold">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                                                <small class="text-muted ms-auto">{{ $payment->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-file-invoice fa-3x text-muted"></i>
                            </div>
                            <h6 class="text-muted">Aucun paiement récent</h6>
                            <p class="text-muted small mb-0">Les paiements récents apparaîtront ici</p>
                        </div>
                    @endif
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
    
    /* Styles pour les cartes d'étudiant */
    .student-card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }
    
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Styles pour les notifications et paiements */
    .notification-list, .payment-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notification-item, .payment-item {
        transition: all 0.2s ease;
    }
    
    .notification-item:hover, .payment-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .notification-icon, .payment-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
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