@extends('layouts.app')

@section('title', 'Mon Profil')

@section('page_title', 'Mon Profil Étudiant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Carte d'informations personnelles -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-user-circle me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($etudiant->photo)
                            <img src="{{ $etudiant->photo }}" alt="Photo de {{ $etudiant->prenoms }}" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 150px; height: 150px; margin: 0 auto;">
                                <span style="font-size: 3rem;">{{ strtoupper(substr($etudiant->prenoms, 0, 1) . substr($etudiant->nom, 0, 1)) }}</span>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $etudiant->prenoms }} {{ $etudiant->nom }}</h4>
                        <p class="badge bg-success">Étudiant</p>
                        <p class="text-muted">Matricule: {{ $etudiant->matricule }}</p>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Date de naissance:</div>
                        <div class="col-md-7">{{ $etudiant->date_naissance ? $etudiant->date_naissance->format('d/m/Y') : 'Non spécifiée' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Lieu de naissance:</div>
                        <div class="col-md-7">{{ $etudiant->lieu_naissance ?: 'Non spécifié' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Nationalité:</div>
                        <div class="col-md-7">{{ $etudiant->nationalite ?: 'Non spécifiée' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Sexe:</div>
                        <div class="col-md-7">{{ $etudiant->sexe == 'M' ? 'Masculin' : 'Féminin' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Adresse:</div>
                        <div class="col-md-7">{{ $etudiant->adresse ?: 'Non spécifiée' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Téléphone:</div>
                        <div class="col-md-7">{{ $etudiant->telephone ?: 'Non spécifié' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-5 fw-bold">Email personnel:</div>
                        <div class="col-md-7">{{ $etudiant->email_personnel ?: 'Non spécifié' }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="#" class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-pen me-2"></i>Demander une mise à jour</a>
                </div>
            </div>
        </div>

        <!-- Carte d'informations académiques -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-graduation-cap me-2"></i>Informations académiques</h5>
                </div>
                <div class="card-body">
                    @if($inscription)
                        <div class="alert alert-success">
                            <h5 class="alert-heading">Inscription active</h5>
                            <p>Vous êtes actuellement inscrit(e) pour l'année universitaire {{ $inscription->anneeUniversitaire->name ?? 'Non spécifiée' }}.</p>
                        </div>

<div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-book-open me-2"></i>Filière</h6>
                                        <h5 class="card-title">{{ $inscription->filiere->name ?? 'Non spécifiée' }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-layer-group me-2"></i>Niveau</h6>
                                        <h5 class="card-title">{{ $inscription->niveau->name ?? 'Non spécifié' }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

<div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-chalkboard me-2"></i>Classe</h6>
                                        <h5 class="card-title">{{ $inscription->classe->name ?? 'Non spécifiée' }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-calendar-alt me-2"></i>Date d'inscription</h6>
                                        <h5 class="card-title">{{ \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

<h5 class="mt-4 mb-3">Informations financières</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Frais d'inscription</th>
                                        <th>Montant scolarité</th>
                                        <th>Montant payé</th>
                                        <th>Reste à payer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ number_format($inscription->frais_inscription, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($inscription->montant_scolarite, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($inscription->paiements->sum('montant'), 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($inscription->montant_scolarite - $inscription->paiements->sum('montant'), 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">Aucune inscription active</h5>
                            <p>Vous n'avez pas d'inscription active pour l'année universitaire en cours. Veuillez contacter l'administration pour plus d'informations.</p>
                        </div>
                    @endif
                </div>
            </div>

<!-- Historique des inscriptions -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Historique des inscriptions</h5>
                </div>
                <div class="card-body">
                    @if($etudiant->inscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Année universitaire</th>
                                        <th>Filière</th>
                                        <th>Niveau</th>
                                        <th>Classe</th>
                                        <th>Statut</th>
                                        <th>Date d'inscription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etudiant->inscriptions as $inscription)
                                    <tr>
                                        <td>{{ $inscription->anneeUniversitaire->name ?? 'Non spécifiée' }}</td>
                                        <td>{{ $inscription->filiere->name ?? 'Non spécifiée' }}</td>
                                        <td>{{ $inscription->niveau->name ?? 'Non spécifié' }}</td>
                                        <td>{{ $inscription->classe->name ?? 'Non spécifiée' }}</td>
                                        <td>
                                            @if($inscription->statut == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($inscription->statut == 'completed')
                                                <span class="badge bg-info">Terminée</span>
                                            @elseif($inscription->statut == 'cancelled')
                                                <span class="badge bg-danger">Annulée</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($inscription->statut) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucun historique d'inscription disponible.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
