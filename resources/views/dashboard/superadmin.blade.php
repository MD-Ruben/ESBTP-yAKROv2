@extends('layouts.app')

@section('title', 'Tableau de bord Super Admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="my-4">Bienvenue, {{ $user->name }}</h1>
    <p class="text-muted">Gestion administrative ESBTP-yAKRO</p>

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

    <div class="row">
        @if($pendingInscriptionsCount > 0)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" style="border-left: 5px solid #f6c23e;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inscriptions en attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingInscriptionsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.inscriptions.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning mt-3">
                        <i class="fas fa-check me-1"></i>Valider les inscriptions
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalStudents))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Étudiants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.etudiants-inscriptions.index') }}" class="btn btn-sm btn-primary mt-3">Gérer les étudiants</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalSecretaires))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Secrétaires</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSecretaires }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.secretaires.index') }}" class="btn btn-sm btn-success mt-3">Gérer les secrétaires</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalFilieres))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Filières</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFilieres }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-sm btn-info mt-3">Gérer les filières</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalFormations))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Formations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFormations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.formations.index') }}" class="btn btn-sm btn-warning mt-3">Gérer les formations</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        @if(isset($totalNiveaux))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Niveaux d'études</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNiveaux }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="btn btn-sm btn-primary mt-3">Gérer les niveaux</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalClasses))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalClasses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.student.classes.index') }}" class="btn btn-sm btn-success mt-3">Gérer les classes</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalMatieres))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Matières</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMatieres }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.matieres.index') }}" class="btn btn-sm btn-info mt-3">Gérer les matières</a>
                </div>
            </div>
        </div>
        @endif

        @if(isset($totalExamens))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Examens</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalExamens }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-sm btn-warning mt-3">Gérer les examens</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Examens à venir -->
    @if(isset($upcomingExamens) && $upcomingExamens->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Examens à venir</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Type</th>
                                    <th>Matière</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingExamens as $examen)
                                <tr>
                                    <td>{{ $examen->classe->nom ?? 'N/A' }}</td>
                                    <td>{{ $examen->type }}</td>
                                    <td>{{ $examen->matiere->nom ?? 'N/A' }}</td>
                                    <td>{{ $examen->date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('evaluations.show', $examen->id) }}" class="btn btn-sm btn-info">Détails</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Messages récents -->
    @if(isset($recentMessages) && $recentMessages->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Messages récents</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($recentMessages as $message)
                        <a href="{{ route('messages.show', $message->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $message->subject }}</h5>
                                <small>{{ $message->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                            <small>De: {{ $message->sender->name ?? 'Système' }}</small>
                        </a>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('messages.index') }}" class="btn btn-primary">Voir tous les messages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Notifications récentes -->
    @if(isset($recentNotifications) && $recentNotifications->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notifications récentes</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($recentNotifications as $notification)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $notification->title }}</h5>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $notification->message }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
