@extends('layouts.app')

@section('title', 'Mes Notes | ESBTP-yAKRO')

@section('page_title', 'Mes Notes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-2 font-weight-bold">Mes Notes</h1>
                    <p class="mb-0">Consultez vos notes par matière et par période d'évaluation</p>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('mes-notes.index') }}" method="GET" class="form-inline">
                                <div class="form-group mr-3 mb-2">
                                    <label for="annee_universitaire_id" class="mr-2">Année universitaire :</label>
                                    <select class="form-control" id="annee_universitaire_id" name="annee_universitaire_id">
                                        @foreach($anneesUniversitaires as $annee)
                                            <option value="{{ $annee->id }}" {{ $anneeId == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->libelle }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3 mb-2">
                                    <label for="periode" class="mr-2">Période :</label>
                                    <select class="form-control" id="periode" name="periode">
                                        <option value="">Toutes les périodes</option>
                                        <option value="semestre1" {{ $periode == 'semestre1' ? 'selected' : '' }}>Semestre 1</option>
                                        <option value="semestre2" {{ $periode == 'semestre2' ? 'selected' : '' }}>Semestre 2</option>
                                        <option value="trimestre1" {{ $periode == 'trimestre1' ? 'selected' : '' }}>Trimestre 1</option>
                                        <option value="trimestre2" {{ $periode == 'trimestre2' ? 'selected' : '' }}>Trimestre 2</option>
                                        <option value="trimestre3" {{ $periode == 'trimestre3' ? 'selected' : '' }}>Trimestre 3</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mb-2">Filtrer</button>
                            </form>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Moyenne générale</h5>
                                    <h2 class="display-4">{{ number_format($moyenneGenerale, 2) }}/20</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Matières validées</h5>
                                    <h2 class="display-4">{{ $matieresValidees }} / {{ $totalMatieres }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Meilleure note</h5>
                                    <h2 class="display-4">{{ number_format($meilleureNote, 2) }}/20</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Note la plus basse</h5>
                                    <h2 class="display-4">{{ number_format($noteLaPlusBasse, 2) }}/20</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des notes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Matière</th>
                                    <th>Coefficient</th>
                                    <th>Type d'évaluation</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Moyenne de classe</th>
                                    <th>Rang</th>
                                    <th>Observations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notes as $note)
                                <tr>
                                    <td>{{ $note->evaluation->matiere->libelle }}</td>
                                    <td>{{ $note->evaluation->matiere->coefficient }}</td>
                                    <td>{{ $note->evaluation->type }}</td>
                                    <td>{{ $note->evaluation->date_evaluation->format('d/m/Y') }}</td>
                                    <td class="{{ $note->valeur < 10 ? 'text-danger' : 'text-success' }} font-weight-bold">
                                        {{ number_format($note->valeur, 2) }}/20
                                    </td>
                                    <td>{{ number_format($note->moyenne_classe, 2) }}/20</td>
                                    <td>{{ $note->rang }}/{{ $note->total_etudiants }}</td>
                                    <td>{{ $note->observations }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Aucune note disponible pour cette période.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphique d'évolution -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Évolution de mes notes</h5>
                </div>
                <div class="card-body">
                    <canvas id="evolutionChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique (à remplacer par des données dynamiques)
        const ctx = document.getElementById('evolutionChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($graphData['labels']) !!},
                datasets: [{
                    label: 'Évolution de mes notes',
                    data: {!! json_encode($graphData['values']) !!},
                    backgroundColor: 'rgba(1, 99, 47, 0.2)',
                    borderColor: 'rgba(1, 99, 47, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }, {
                    label: 'Moyenne de classe',
                    data: {!! json_encode($graphData['moyenneClasse']) !!},
                    backgroundColor: 'rgba(242, 148, 0, 0.2)',
                    borderColor: 'rgba(242, 148, 0, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 20
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection 