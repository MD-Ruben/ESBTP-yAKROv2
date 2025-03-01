@extends('layouts.esbtp')

@section('title', 'Résultats de la classe - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Résultats - {{ $classe->name }} ({{ $periode_texte }}) - {{ $annee->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.bulletins.select') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la sélection
                        </a>
                        <a href="{{ route('esbtp.bulletins.generer-classe') }}" class="btn btn-success" 
                           onclick="event.preventDefault(); document.getElementById('generer-form').submit();">
                            <i class="fas fa-file-pdf me-1"></i>Générer tous les bulletins
                        </a>
                        <form id="generer-form" action="{{ route('esbtp.bulletins.generer-classe') }}" method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="classe_id" value="{{ $classe->id }}">
                            <input type="hidden" name="annee_universitaire_id" value="{{ $annee->id }}">
                            <input type="hidden" name="periode" value="{{ $periode }}">
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">Rang</th>
                                    <th rowspan="2" class="align-middle">Matricule</th>
                                    <th rowspan="2" class="align-middle">Nom et prénom(s)</th>
                                    @foreach($matieres as $matiere)
                                        <th class="text-center">{{ $matiere->code }}</th>
                                    @endforeach
                                    <th rowspan="2" class="align-middle text-center">Moyenne</th>
                                    <th rowspan="2" class="align-middle text-center">Mention</th>
                                    <th rowspan="2" class="align-middle text-center">Actions</th>
                                </tr>
                                <tr>
                                    @foreach($matieres as $matiere)
                                        <th class="text-center small">Coef. {{ $matiere->coefficient_default }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bulletins as $index => $bulletin)
                                    <tr>
                                        <td class="text-center">{{ $bulletin->rang ?: 'N/A' }}</td>
                                        <td>{{ $bulletin->etudiant->matricule }}</td>
                                        <td>{{ $bulletin->etudiant->nom }} {{ $bulletin->etudiant->prenoms }}</td>
                                        
                                        @foreach($matieres as $matiere)
                                            @php
                                                $resultatMatiere = $bulletin->resultatsMatiere->firstWhere('matiere_id', $matiere->id);
                                            @endphp
                                            <td class="text-center {{ $resultatMatiere && $resultatMatiere->moyenne < 10 ? 'text-danger' : '' }}">
                                                @if($resultatMatiere)
                                                    {{ number_format($resultatMatiere->moyenne, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                        
                                        <td class="text-center fw-bold {{ $bulletin->moyenne_generale < 10 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($bulletin->moyenne_generale, 2) }}
                                        </td>
                                        <td class="text-center">
                                            @if($bulletin->moyenne_generale >= 16)
                                                <span class="badge bg-success">Très Bien</span>
                                            @elseif($bulletin->moyenne_generale >= 14)
                                                <span class="badge bg-primary">Bien</span>
                                            @elseif($bulletin->moyenne_generale >= 12)
                                                <span class="badge bg-info">Assez Bien</span>
                                            @elseif($bulletin->moyenne_generale >= 10)
                                                <span class="badge bg-warning">Passable</span>
                                            @else
                                                <span class="badge bg-danger">Insuffisant</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.bulletins.show', $bulletin) }}" class="btn btn-sm btn-info" title="Voir le détail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.bulletins.pdf', $bulletin) }}" class="btn btn-sm btn-success" title="Télécharger le PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="{{ route('esbtp.bulletins.edit', $bulletin) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + count($matieres) }}" class="text-center">
                                            Aucun bulletin n'a été généré pour cette classe.
                                            <a href="#" class="btn btn-sm btn-primary ms-2" 
                                               onclick="event.preventDefault(); document.getElementById('generer-form').submit();">
                                                Générer les bulletins
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques de la classe -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Répartition des moyennes</h5>
                </div>
                <div class="card-body">
                    <canvas id="moyennesChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Statistiques de la classe</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Effectif total
                            <span class="badge bg-primary rounded-pill">{{ count($bulletins) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Moyenne générale de la classe
                            <span class="badge bg-info rounded-pill">
                                {{ number_format($bulletins->avg('moyenne_generale') ?? 0, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Meilleure moyenne
                            <span class="badge bg-success rounded-pill">
                                {{ number_format($bulletins->max('moyenne_generale') ?? 0, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Moyenne la plus basse
                            <span class="badge bg-danger rounded-pill">
                                {{ number_format($bulletins->min('moyenne_generale') ?? 0, 2) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nombre d'admis (≥ 10)
                            <span class="badge bg-success rounded-pill">
                                {{ $bulletins->where('moyenne_generale', '>=', 10)->count() }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Taux de réussite
                            <span class="badge bg-primary rounded-pill">
                                {{ count($bulletins) > 0 ? number_format(($bulletins->where('moyenne_generale', '>=', 10)->count() / count($bulletins)) * 100, 2) : 0 }}%
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Répartition des mentions</h5>
                </div>
                <div class="card-body">
                    <canvas id="mentionsChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            },
            "order": [[0, 'asc']] // Trier par rang par défaut
        });
        
        // Données pour les graphiques
        const moyennesData = {
            labels: ['[0-5[', '[5-10[', '[10-12[', '[12-14[', '[14-16[', '[16-20]'],
            datasets: [{
                label: 'Nombre d\'étudiants',
                data: [
                    {{ $bulletins->where('moyenne_generale', '<', 5)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 5)->where('moyenne_generale', '<', 10)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 10)->where('moyenne_generale', '<', 12)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 12)->where('moyenne_generale', '<', 14)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 14)->where('moyenne_generale', '<', 16)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 16)->count() }}
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(255, 205, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 1
            }]
        };
        
        const mentionsData = {
            labels: ['Insuffisant', 'Passable', 'Assez Bien', 'Bien', 'Très Bien'],
            datasets: [{
                label: 'Nombre d\'étudiants',
                data: [
                    {{ $bulletins->where('moyenne_generale', '<', 10)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 10)->where('moyenne_generale', '<', 12)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 12)->where('moyenne_generale', '<', 14)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 14)->where('moyenne_generale', '<', 16)->count() }},
                    {{ $bulletins->where('moyenne_generale', '>=', 16)->count() }}
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(255, 205, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)'
                ],
                borderWidth: 1
            }]
        };
        
        // Configuration des graphiques
        const moyennesConfig = {
            type: 'bar',
            data: moyennesData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Répartition des moyennes'
                    }
                }
            }
        };
        
        const mentionsConfig = {
            type: 'pie',
            data: mentionsData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    title: {
                        display: true,
                        text: 'Répartition des mentions'
                    }
                }
            }
        };
        
        // Création des graphiques
        new Chart(document.getElementById('moyennesChart'), moyennesConfig);
        new Chart(document.getElementById('mentionsChart'), mentionsConfig);
    });
</script>
@endsection 