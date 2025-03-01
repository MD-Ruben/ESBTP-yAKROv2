@extends('layouts.app')

@section('title', 'Tableau de bord Étudiant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenue {{ $student->user->name }}</h5>
                    <p class="card-text">Votre espace étudiant ESBTP-yAKRO</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Informations personnelles</h5>
                    <p class="mb-1"><strong>N° d'étudiant :</strong> {{ $student->registration_number }}</p>
                    <p class="mb-1"><strong>Filière :</strong> {{ $student->filiere->name ?? 'Non définie' }}</p>
                    <p class="mb-1"><strong>Niveau :</strong> {{ $student->niveau->name ?? 'Non défini' }}</p>
                    <p class="mb-0"><strong>Classe :</strong> {{ $student->classe->name ?? 'Non définie' }}</p>
                    <div class="mt-3">
                        <a href="{{ route('esbtp.student.profile') }}" class="btn btn-light">Voir mon profil</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Notifications</h5>
                    <p class="display-4 mb-0">{{ $unreadNotifications ?? 0 }}</p>
                    <p class="mb-0">non lues</p>
                    <div class="mt-3">
                        <a href="{{ route('esbtp.notifications.index') }}" class="btn btn-light">Voir toutes</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Examens à venir</h5>
                    <p class="display-4 mb-0">{{ count($recentExams ?? []) }}</p>
                    <p class="mb-0">prochains examens</p>
                    <div class="mt-3">
                        <a href="{{ route('esbtp.exams.student') }}" class="btn btn-light">Voir le calendrier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Emploi du temps aujourd'hui
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($todayTimetable as $session)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary">{{ $session->start_time }} - {{ $session->end_time }}</span>
                                        <strong class="ms-2">{{ $session->subject->name ?? 'Matière inconnue' }}</strong>
                                    </div>
                                    <span>{{ $session->teacher->user->name ?? 'Enseignant non défini' }}</span>
                                </div>
                                <div class="mt-1 small text-muted">
                                    Salle: {{ $session->classroom ?? 'Non définie' }}
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun cours prévu aujourd'hui</li>
                        @endforelse
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('esbtp.timetables.student') }}" class="btn btn-outline-primary">Voir tout l'emploi du temps</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Dernières notes obtenues
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentGrades as $grade)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $grade->subject->name ?? 'Matière inconnue' }}</strong>
                                        <span class="text-muted">({{ $grade->exam->title ?? 'Examen' }})</span>
                                    </div>
                                    <span class="badge {{ $grade->score >= 10 ? 'bg-success' : 'bg-danger' }} rounded-pill">{{ $grade->score }}/20</span>
                                </div>
                                <div class="mt-1 small text-muted">
                                    {{ $grade->created_at->format('d/m/Y') }}
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">Aucune note récente</li>
                        @endforelse
                    </ul>
                    <div class="mt-3">
                        <a href="{{ route('esbtp.grades.student') }}" class="btn btn-outline-primary">Voir toutes mes notes</a>
                        <a href="{{ route('esbtp.bulletin.student') }}" class="btn btn-outline-success">Voir mon bulletin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Notifications récentes
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentNotifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $notification->title }}</strong>
                                    <p class="mb-0">{{ Str::limit($notification->message, 100) }}</p>
                                </div>
                                <div>
                                    <span class="badge {{ $notification->is_read ? 'bg-secondary' : 'bg-danger' }}">
                                        {{ $notification->is_read ? 'Lu' : 'Non lu' }}
                                    </span>
                                    <small class="ms-2">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">Aucune notification récente</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection