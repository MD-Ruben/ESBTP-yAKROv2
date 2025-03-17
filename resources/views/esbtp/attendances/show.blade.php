@extends('layouts.app')

@section('title', 'Détails de la présence')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-calendar-check"></i>
            </span> Détails de la présence
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esbtp.attendances.index') }}">Présences</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détails</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Informations sur la présence</h4>

                    <div class="d-flex justify-content-end mb-4">
                        <a href="{{ route('esbtp.attendances.edit', $attendance) }}" class="btn btn-gradient-info btn-sm">
                            <i class="mdi mdi-pencil"></i> Modifier
                        </a>
                        <form action="{{ route('esbtp.attendances.destroy', $attendance) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-gradient-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette présence ?')">
                                <i class="mdi mdi-delete"></i> Supprimer
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th width="30%">Étudiant</th>
                                    <td>{{ $attendance->etudiant->nom_complet }}</td>
                                </tr>
                                <tr>
                                    <th>Classe</th>
                                    <td>{{ $attendance->seanceCours->emploiTemps->classe->name }}</td>
                                </tr>
                                <tr>
                                    <th>Matière</th>
                                    <td>{{ $attendance->seanceCours->matiere->nom }}</td>
                                </tr>
                                <tr>
                                    <th>Séance</th>
                                    <td>{{ $attendance->seanceCours->jour }} - {{ $attendance->seanceCours->plage_horaire }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        @if($attendance->statut == 'present')
                                            <span class="badge badge-success">Présent</span>
                                        @elseif($attendance->statut == 'absent')
                                            <span class="badge badge-danger">Absent</span>
                                        @elseif($attendance->statut == 'retard')
                                            <span class="badge badge-warning">En retard</span>
                                        @elseif($attendance->statut == 'excuse')
                                            <span class="badge badge-info">Excusé</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Commentaire</th>
                                    <td>{{ $attendance->commentaire ?? 'Aucun commentaire' }}</td>
                                </tr>
                                <tr>
                                    <th>Créé par</th>
                                    <td>{{ $attendance->createdBy->name }} le {{ $attendance->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                @if($attendance->updated_by)
                                <tr>
                                    <th>Dernière modification</th>
                                    <td>{{ $attendance->updatedBy->name }} le {{ $attendance->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('esbtp.attendances.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Informations sur l'étudiant</h4>

                    <div class="text-center mb-4">
                        @if($attendance->etudiant->photo)
                            <img src="{{ asset('storage/' . $attendance->etudiant->photo) }}" alt="Photo de l'étudiant" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/images/avatar.jpg') }}" alt="Photo par défaut" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                    </div>

                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Matricule</span>
                            <span class="badge badge-primary">{{ $attendance->etudiant->matricule }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Téléphone</span>
                            <span>{{ $attendance->etudiant->telephone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Email</span>
                            <span>{{ $attendance->etudiant->email_personnel }}</span>
                        </li>
                    </ul>

                    <div class="mt-4">
                        <a href="{{ route('esbtp.etudiants.show', $attendance->etudiant) }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-account"></i> Voir le profil complet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
