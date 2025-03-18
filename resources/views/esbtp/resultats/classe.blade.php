@extends('layouts.app')

@section('title', 'Résultats de la classe ' . $classe->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Résultats de la classe {{ $classe->name }}</h5>
                    <a href="{{ route('esbtp.resultats.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('esbtp.resultats.classe', $classe) }}" method="GET" class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="annee_universitaire_id">Année Universitaire :</label>
                                    <select class="form-select" id="annee_universitaire_id" name="annee_universitaire_id">
                                        @foreach($anneesUniversitaires ?? [] as $annee)
                                            <option value="{{ $annee->id }}" {{ isset($annee_id) && $annee_id == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->annee_debut }}-{{ $annee->annee_fin }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="periode">Période :</label>
                                    <select class="form-select" id="periode" name="periode">
                                        @foreach($periodes ?? [] as $p)
                                            <option value="{{ $p->id }}" {{ isset($periode) && $periode == $p->id ? 'selected' : '' }}>
                                                {{ $p->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Infos classe -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6>Informations de la classe</h6>
                                    <p class="mb-1"><strong>Nom:</strong> {{ $classe->name }}</p>
                                    <p class="mb-1"><strong>Filière:</strong> {{ $classe->filiere->name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Niveau:</strong> {{ $classe->niveau->name ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Nombre d'étudiants:</strong> {{ count($resultats) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-body">
                                    <h6>Statistiques</h6>
                                    @php
                                        $totalMoyennes = 0;
                                        $countMoyennes = 0;
                                        $min = 20;
                                        $max = 0;
                                        $countSucces = 0;
                                        $countEchec = 0;

                                        foreach ($resultats as $resultat) {
                                            if ($resultat['notes_count'] > 0) {
                                                $totalMoyennes += $resultat['moyenne'];
                                                $countMoyennes++;

                                                $min = min($min, $resultat['moyenne']);
                                                $max = max($max, $resultat['moyenne']);

                                                if ($resultat['moyenne'] >= 10) {
                                                    $countSucces++;
                                                } else {
                                                    $countEchec++;
                                                }
                                            }
                                        }

                                        $moyenneClasse = $countMoyennes > 0 ? $totalMoyennes / $countMoyennes : 0;
                                        $tauxReussite = $countMoyennes > 0 ? ($countSucces / $countMoyennes) * 100 : 0;
                                    @endphp

                                    <p class="mb-1"><strong>Moyenne de la classe:</strong> {{ number_format($moyenneClasse, 2) }}/20</p>
                                    <p class="mb-1"><strong>Note minimale:</strong> {{ $countMoyennes > 0 ? number_format($min, 2) : 'N/A' }}/20</p>
                                    <p class="mb-1"><strong>Note maximale:</strong> {{ $countMoyennes > 0 ? number_format($max, 2) : 'N/A' }}/20</p>
                                    <p class="mb-0"><strong>Taux de réussite:</strong> {{ number_format($tauxReussite, 2) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">Matricule</th>
                                    <th style="width: 20%">Nom</th>
                                    <th style="width: 20%">Prénom</th>
                                    <th style="width: 15%">Moyenne</th>
                                    <th style="width: 15%">Nb. Notes</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resultats as $index => $resultat)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $resultat['etudiant']->matricule }}</td>
                                        <td>{{ $resultat['etudiant']->nom }}</td>
                                        <td>{{ $resultat['etudiant']->prenom }}</td>
                                        <td class="{{ $resultat['moyenne'] >= 10 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                            {{ number_format($resultat['moyenne'], 2) }}/20
                                        </td>
                                        <td>{{ $resultat['notes_count'] }}</td>
                                        <td>
                                            <a href="{{ route('esbtp.resultats.etudiant', $resultat['etudiant']) }}?classe_id={{ $classe->id }}&periode={{ $periode }}&annee_universitaire_id={{ $annee_id }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucun résultat trouvé pour cette classe</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
