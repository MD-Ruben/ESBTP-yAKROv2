@extends('layouts.app')

@section('title', 'Mes Bulletins | ESBTP-yAKRO')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h1 class="h3 mb-2 font-weight-bold">Mes Bulletins</h1>
                    <p class="text-muted">Consultez vos bulletins et résultats scolaires pour l'année universitaire en cours.</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Informations étudiant</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-bold">Matricule :</span>
                                    <span>{{ $etudiant->matricule }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-bold">Nom et prénom :</span>
                                    <span>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-bold">Classe actuelle :</span>
                                    <span>
                                        @if($etudiant->inscriptionActive() && $etudiant->inscriptionActive()->classe)
                                            {{ $etudiant->inscriptionActive()->classe->nom }}
                                        @else
                                            <span class="badge bg-warning">Non inscrit</span>
                                        @endif
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-bold">Année universitaire :</span>
                                    <span>
                                        @if($etudiant->inscriptionActive() && $etudiant->inscriptionActive()->anneeUniversitaire)
                                            {{ $etudiant->inscriptionActive()->anneeUniversitaire->annee_debut }}-{{ $etudiant->inscriptionActive()->anneeUniversitaire->annee_fin }}
                                        @else
                                            <span class="badge bg-warning">Non inscrit</span>
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Filtrer mes bulletins</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('mon-bulletin.index') }}" method="GET" id="filter-form">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="annee_universitaire_id" class="form-label">Année universitaire</label>
                                <select class="form-select" id="annee_universitaire_id" name="annee_universitaire_id">
                                    <option value="">Toutes les années</option>
                                    @foreach($anneesUniversitaires as $annee)
                                        <option value="{{ $annee->id }}" {{ $annee_id == $annee->id ? 'selected' : '' }}>
                                            {{ $annee->annee_debut }}-{{ $annee->annee_fin }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label for="periode" class="form-label">Période</label>
                                <select class="form-select" id="periode" name="periode">
                                    <option value="">Toutes les périodes</option>
                                    <option value="Premier Semestre" {{ $periode == 'Premier Semestre' ? 'selected' : '' }}>Premier Semestre</option>
                                    <option value="Deuxième Semestre" {{ $periode == 'Deuxième Semestre' ? 'selected' : '' }}>Deuxième Semestre</option>
                                    <option value="Annuel" {{ $periode == 'Annuel' ? 'selected' : '' }}>Annuel</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Mes bulletins</h5>
                </div>
                <div class="card-body">
                    @if(count($bulletins) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Année</th>
                                        <th>Classe</th>
                                        <th>Période</th>
                                        <th>Date</th>
                                        <th>Moyenne</th>
                                        <th>Rang</th>
                                        <th>Appréciation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bulletins as $bulletin)
                                        <tr>
                                            <td>
                                                @if($bulletin->anneeUniversitaire)
                                                    {{ $bulletin->anneeUniversitaire->annee_debut }}-{{ $bulletin->anneeUniversitaire->annee_fin }}
                                                @else
                                                    <span class="text-muted">Non définie</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($bulletin->classe)
                                                    {{ $bulletin->classe->nom }}
                                                @else
                                                    <span class="text-muted">Non définie</span>
                                                @endif
                                            </td>
                                            <td>{{ $bulletin->periode }}</td>
                                            <td>{{ $bulletin->date_generation->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge {{ $bulletin->moyenne_generale >= 10 ? 'bg-success' : 'bg-danger' }} fs-6">
                                                    {{ number_format($bulletin->moyenne_generale, 2) }}/20
                                                </span>
                                            </td>
                                            <td>{{ $bulletin->rang }}/{{ $bulletin->effectif_classe }}</td>
                                            <td>{{ $bulletin->appreciation }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('esbtp.bulletins.show', $bulletin->id) }}" class="btn btn-sm btn-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('esbtp.bulletins.pdf', $bulletin->id) }}" class="btn btn-sm btn-danger" title="Télécharger PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> Aucun bulletin n'est disponible pour le moment.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Soumettre le formulaire automatiquement lors d'un changement de filtre
        document.querySelectorAll('#filter-form select').forEach(function(element) {
            element.addEventListener('change', function() {
                document.getElementById('filter-form').submit();
            });
        });
    });
</script>
@endsection 