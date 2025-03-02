@extends('layouts.app')

@section('title', 'Absences de ' . $etudiant->nom . ' ' . $etudiant->prenoms)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Absences de {{ $etudiant->nom }} {{ $etudiant->prenoms }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent.student.show', $etudiant->id) }}">{{ $etudiant->nom }} {{ $etudiant->prenoms }}</a></li>
                        <li class="breadcrumb-item active">Absences</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4 class="card-title">Liste des absences</h4>
                        </div>
                    </div>

                    @if($absences->isEmpty())
                    <div class="text-center mb-4">
                        <h4>Aucune absence enregistrée pour cet étudiant.</h4>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Cours</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($absences as $absence)
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
                                        @if($absence->heure_debut && $absence->heure_fin)
                                            {{ $absence->heure_debut->format('H:i') }} - {{ $absence->heure_fin->format('H:i') }}
                                            ({{ $absence->duree_heures }} heures)
                                        @else
                                            Journée complète
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
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('parent.absences.show', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="mdi mdi-eye"></i> Détails
                                            </a>
                                            @if(!$absence->justifie)
                                            <a href="{{ route('parent.absences.edit', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-pencil"></i> Justifier
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $absences->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Statistiques des absences</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="text-muted font-size-14">Total des absences</h5>
                                <h2 class="text-primary">{{ $absences->total() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="text-muted font-size-14">Absences justifiées</h5>
                                <h2 class="text-success">{{ $absences->where('justifie', true)->count() }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="text-muted font-size-14">Absences non justifiées</h5>
                                <h2 class="text-danger">{{ $absences->where('justifie', false)->count() }}</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="text-muted font-size-14">Ce mois-ci</h5>
                                @php
                                    $currentMonth = now()->month;
                                    $absencesThisMonth = $absences->filter(function($item) use ($currentMonth) {
                                        return $item->date->month === $currentMonth;
                                    })->count();
                                @endphp
                                <h2 class="text-info">{{ $absencesThisMonth }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Informations sur la justification</h4>
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Comment justifier une absence ?</h5>
                        <p>Pour justifier une absence, veuillez cliquer sur le bouton "Justifier" à côté de l'absence concernée. Vous pourrez alors fournir un motif et télécharger un document justificatif (certificat médical, etc.).</p>
                        <hr>
                        <p class="mb-0">La justification sera examinée par l'administration de l'école. Une absence n'est considérée comme justifiée qu'après validation par l'administration.</p>
                    </div>
                    <div class="mt-4">
                        <h5>Documents acceptés :</h5>
                        <ul class="list-group">
                            <li class="list-group-item">Certificat médical</li>
                            <li class="list-group-item">Convocation officielle</li>
                            <li class="list-group-item">Attestation d'événement familial</li>
                            <li class="list-group-item">Autre document officiel</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 