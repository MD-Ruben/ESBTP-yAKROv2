@extends('layouts.app')

@section('title', 'Résumé des absences')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Résumé des absences</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Résumé des absences</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if($etudiants->isEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="display-5 text-muted mb-4">
                        <i class="mdi mdi-information-outline"></i>
                    </div>
                    <h4 class="mb-3">Aucun étudiant associé à votre compte</h4>
                    <p class="text-muted">Veuillez contacter l'administration de l'école pour associer vos enfants à votre compte.</p>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Vue d'ensemble des absences</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Classe</th>
                                    <th>Total absences</th>
                                    <th>Justifiées</th>
                                    <th>Non justifiées</th>
                                    <th>Taux de présence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($etudiants as $etudiant)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                @if($etudiant->photo)
                                                <img src="{{ Storage::url($etudiant->photo) }}" alt="{{ $etudiant->nom }}" class="rounded-circle">
                                                @else
                                                <span class="avatar-title rounded-circle bg-primary text-white">
                                                    {{ substr($etudiant->prenoms, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}
                                                </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="font-size-14 mb-1">{{ $etudiant->nom }} {{ $etudiant->prenoms }}</h5>
                                                <p class="text-muted mb-0">{{ $etudiant->matricule }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($etudiant->classe_active)
                                            {{ $etudiant->classe_active->niveau->nom }} {{ $etudiant->classe_active->filiere->code }}
                                        @else
                                            Non définie
                                        @endif
                                    </td>
                                    <td>{{ $absenceStats[$etudiant->id]['total'] }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $absenceStats[$etudiant->id]['justified'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $absenceStats[$etudiant->id]['unjustified'] }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $absenceStats[$etudiant->id]['attendance_rate'] >= 90 ? 'success' : ($absenceStats[$etudiant->id]['attendance_rate'] >= 75 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $absenceStats[$etudiant->id]['attendance_rate'] }}%;" aria-valuenow="{{ $absenceStats[$etudiant->id]['attendance_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2">{{ $absenceStats[$etudiant->id]['attendance_rate'] }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('parent.absences.index', $etudiant->id) }}" class="btn btn-primary btn-sm">
                                            <i class="mdi mdi-eye font-size-16"></i>
                                        </a>
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

    <div class="row">
        @foreach($etudiants as $etudiant)
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-3">Absences récentes - {{ $etudiant->nom }} {{ $etudiant->prenoms }}</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('parent.absences.index', $etudiant->id) }}" class="btn btn-primary btn-sm">
                                Voir tout
                            </a>
                        </div>
                    </div>

                    @if($absenceStats[$etudiant->id]['recent_absences']->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">Aucune absence récente</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Cours</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($absenceStats[$etudiant->id]['recent_absences'] as $absence)
                                <tr>
                                    <td>{{ $absence->date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($absence->cours)
                                            {{ $absence->cours->matiere->nom }}
                                        @else
                                            Non spécifié
                                        @endif
                                    </td>
                                    <td>
                                        @if($absence->justifie)
                                            <span class="badge bg-success">Justifiée</span>
                                        @else
                                            <span class="badge bg-danger">Non justifiée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('parent.absences.show', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" class="btn btn-info btn-sm">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        @if(!$absence->justifie)
                                        <a href="{{ route('parent.absences.edit', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" class="btn btn-warning btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informations sur les absences</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info mb-0">
                                <h5 class="alert-heading">Procédure de justification</h5>
                                <p>Pour justifier une absence, cliquez sur le bouton "Justifier" à côté de l'absence concernée. Vous devrez fournir un motif et, si possible, un document justificatif (certificat médical, attestation, etc.).</p>
                                <hr>
                                <p class="mb-0">La justification sera examinée par l'administration de l'école. Une notification vous sera envoyée une fois qu'elle aura été traitée.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border mb-0">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="mb-0">Impact des absences</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="mdi mdi-circle-medium text-danger me-1"></i>
                                            Les absences répétées peuvent affecter les résultats scolaires
                                        </li>
                                        <li class="mb-2">
                                            <i class="mdi mdi-circle-medium text-danger me-1"></i>
                                            Au-delà de 20% d'absences, l'étudiant peut ne pas être autorisé à passer les examens
                                        </li>
                                        <li class="mb-2">
                                            <i class="mdi mdi-circle-medium text-danger me-1"></i>
                                            Les absences non justifiées peuvent entraîner des sanctions disciplinaires
                                        </li>
                                        <li>
                                            <i class="mdi mdi-circle-medium text-success me-1"></i>
                                            Les absences justifiées sont prises en compte dans l'évaluation globale
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 