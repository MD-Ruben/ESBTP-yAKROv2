@extends('layouts.app')

@section('title', 'Détails de l\'étudiant - ' . $etudiant->prenoms . ' ' . $etudiant->nom)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'étudiant</h5>
                    <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour au tableau de bord
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <img src="{{ $etudiant->photo ? asset('storage/'.$etudiant->photo) : asset('images/student-avatar.png') }}"
                                 class="img-thumbnail rounded-circle" alt="{{ $etudiant->prenoms }}"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="mt-3">{{ $etudiant->prenoms }} {{ $etudiant->nom }}</h5>
                            <p class="text-muted mb-1">Matricule: {{ $etudiant->matricule }}</p>
                            <p class="text-muted mb-1">
                                <i class="fas fa-graduation-cap me-1"></i>
                                @if($inscriptionActuelle && $inscriptionActuelle->classe)
                                    {{ $inscriptionActuelle->classe->name }}
                                @else
                                    Non inscrit
                                @endif
                            </p>
                            <div class="mt-3">
                                <span class="badge {{ $etudiant->statut === 'actif' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                    {{ ucfirst($etudiant->statut) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="card border-0 h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Informations personnelles</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <th width="40%">Date de naissance</th>
                                                        <td>{{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : 'Non renseignée' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lieu de naissance</th>
                                                        <td>{{ $etudiant->lieu_naissance ?? 'Non renseigné' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Genre</th>
                                                        <td>{{ $etudiant->sexe === 'M' ? 'Masculin' : 'Féminin' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Nationalité</th>
                                                        <td>{{ $etudiant->nationalite ?? 'Non renseignée' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Téléphone</th>
                                                        <td>{{ $etudiant->telephone ?? 'Non renseigné' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>{{ $etudiant->email_personnel ?? 'Non renseigné' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card border-0 h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Informations académiques</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($inscriptionActuelle)
                                                <table class="table table-borderless">
                                                    <tbody>
                                                        <tr>
                                                            <th width="40%">Filière</th>
                                                            <td>{{ $inscriptionActuelle->filiere->name ?? 'Non renseignée' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Niveau d'étude</th>
                                                            <td>{{ $inscriptionActuelle->niveau->name ?? 'Non renseigné' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Année universitaire</th>
                                                            <td>{{ $inscriptionActuelle->anneeUniversitaire->name ?? 'Non renseignée' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Classe</th>
                                                            <td>{{ $inscriptionActuelle->classe->name ?? 'Non renseignée' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Date d'inscription</th>
                                                            <td>{{ $inscriptionActuelle->date_inscription ? $inscriptionActuelle->date_inscription->format('d/m/Y') : 'Non renseignée' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Type d'inscription</th>
                                                            <td>{{ ucfirst(str_replace('_', ' ', $inscriptionActuelle->type_inscription ?? 'Non renseigné')) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-info">
                                                    Aucune inscription en cours pour cet étudiant.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistiques de présence</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Taux de présence</h6>
                                <h4 class="mb-0">{{ $statsPresence['taux_presence'] }}%</h4>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Nombre d'absences</h6>
                                <h4 class="mb-0">{{ $statsPresence['absences'] }}</h4>
                                <small class="text-muted">dont {{ $statsPresence['absences_justifiees'] }} justifiées</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $statsPresence['taux_presence'] }}%"
                            aria-valuenow="{{ $statsPresence['taux_presence'] }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $statsPresence['taux_presence'] }}%
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Voir toutes les absences
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistiques des notes</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Moyenne générale</h6>
                                <h4 class="mb-0">{{ $statsNotes['moyenne_generale'] }}/20</h4>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Note max</h6>
                                <h4 class="mb-0">{{ $statsNotes['note_max'] }}/20</h4>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-1">Note min</h6>
                                <h4 class="mb-0">{{ $statsNotes['note_min'] }}/20</h4>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-1"></i>Voir toutes les notes
                        </a>
                        <a href="#" class="btn btn-outline-success btn-sm ms-2">
                            <i class="fas fa-file-alt me-1"></i>Voir le bulletin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes récentes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notes récentes</h5>
                </div>
                <div class="card-body">
                    @if($etudiant->notes && $etudiant->notes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Matière</th>
                                        <th>Évaluation</th>
                                        <th class="text-center">Note</th>
                                        <th>Observation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etudiant->notes as $note)
                                        <tr>
                                            <td>{{ $note->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $note->matiere->name ?? 'N/A' }}</td>
                                            <td>{{ $note->evaluation->title ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $note->note >= 10 ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                                    {{ $note->note }}/20
                                                </span>
                                            </td>
                                            <td>{{ $note->observation ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">Aucune note récente disponible.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Absences récentes -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Absences récentes</h5>
                </div>
                <div class="card-body">
                    @if($etudiant->absences && $etudiant->absences->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Cours</th>
                                        <th>Professeur</th>
                                        <th class="text-center">Statut</th>
                                        <th>Motif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etudiant->absences as $absence)
                                        <tr>
                                            <td>{{ $absence->date_absence->format('d/m/Y') }}</td>
                                            <td>{{ $absence->cours->matiere->name ?? 'N/A' }}</td>
                                            <td>{{ $absence->cours->professeur->nom_complet ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $absence->justifie ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                                                    {{ $absence->justifie ? 'Justifiée' : 'Non justifiée' }}
                                                </span>
                                            </td>
                                            <td>{{ $absence->motif ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">Aucune absence récente.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informations du parent / tuteur</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                            <i class="fas fa-user-tie fa-3x text-muted"></i>
                        </div>
                        <h5 class="mt-3">{{ $student->parent->user->name ?? 'Non renseigné' }}</h5>
                        <p class="text-muted">Parent / Tuteur</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
