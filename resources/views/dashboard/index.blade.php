@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenue dans Smart School</h5>
                    <p class="card-text">Système de gestion universitaire</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Étudiants</h5>
                    <p class="card-text display-4">{{ App\Models\Student::count() ?? 0 }}</p>
                    <a href="{{ route('students.index') }}" class="btn btn-light">Voir tous</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Enseignants</h5>
                    <p class="card-text display-4">{{ App\Models\Teacher::count() ?? 0 }}</p>
                    <a href="{{ route('teachers.index') }}" class="btn btn-light">Voir tous</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Présences aujourd'hui</h5>
                    <p class="card-text display-4">{{ App\Models\Attendance::whereDate('created_at', today())->count() ?? 0 }}</p>
                    <a href="{{ route('attendances.index') }}" class="btn btn-light">Voir toutes</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text display-4">{{ App\Models\Notification::count() ?? 0 }}</p>
                    <a href="{{ route('notifications.index') }}" class="btn btn-light">Voir toutes</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Activités récentes
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Aucune activité récente</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Événements à venir
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Aucun événement à venir</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 