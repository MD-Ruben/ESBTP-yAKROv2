@extends('layouts.app')

@section('title', 'Mes Absences')

@section('content')
<div class="container-fluid">
    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Filtrer les données</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('esbtp.mes-absences.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date de début</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ $dateDebut ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ $dateFin ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Matière</label>
                    <select name="matiere_id" class="form-select">
                        <option value="">Toutes les matières</option>
                        @foreach($matieres as $id => $nom)
                            <option value="{{ $id }}" {{ request('matiere_id') == $id ? 'selected' : '' }}>
                                {{ $nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification si l'utilisateur vient d'une notification -->
    @if(request()->has('highlight'))
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Notification d'absence :</strong> Veuillez justifier votre absence ci-dessous pour éviter les pénalités sur votre note d'assiduité.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase fw-semibold text-muted mb-0">Total Absences</h6>
                        <div class="stat-icon bg-danger-light rounded-circle p-2">
                            <i class="fas fa-times text-danger"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 display-5 fw-bold">{{ $absences->count() }}</h2>
                    <div class="mt-2">
                        <small class="text-muted">Sur la période sélectionnée</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase fw-semibold text-muted mb-0">Absences Justifiées</h6>
                        <div class="stat-icon bg-success-light rounded-circle p-2">
                            <i class="fas fa-check text-success"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 display-5 fw-bold">{{ $excuses->count() }}</h2>
                    <div class="mt-2">
                        <small class="text-muted">{{ $absences->count() > 0 ? round(($excuses->count() / max($absences->count(), 1)) * 100) : 0 }}% des absences</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase fw-semibold text-muted mb-0">Retards</h6>
                        <div class="stat-icon bg-warning-light rounded-circle p-2">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                    </div>
                    <h2 class="mb-0 display-5 fw-bold">{{ $retards->count() }}</h2>
                    <div class="mt-2">
                        <small class="text-muted">Sur {{ $presences->count() + $absences->count() + $retards->count() + $excuses->count() }} séances</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-uppercase fw-semibold text-muted mb-0">Taux de Présence</h6>
                        <div class="stat-icon bg-info-light rounded-circle p-2">
                            <i class="fas fa-chart-line text-info"></i>
                        </div>
                    </div>
                    @php
                        $totalDays = $presences->count() + $absences->count();
                        $presenceRate = $totalDays > 0 ? round(($presences->count() / $totalDays) * 100) : 100;
                    @endphp
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                            <div class="progress-bar {{ $presenceRate >= 75 ? 'bg-success' : ($presenceRate >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 style="width: {{ $presenceRate }}%"
                                 aria-valuenow="{{ $presenceRate }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <span class="fw-bold">{{ $presenceRate }}%</span>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Objectif: minimum 75%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Absences par matière</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="absencesParMatiereChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Évolution des absences</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="evolutionAbsencesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques par matière -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Statistiques par matière</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Matière</th>
                            <th>Total séances</th>
                            <th>Présences</th>
                            <th>Absences</th>
                            <th>Retards</th>
                            <th>Excusés</th>
                            <th>Taux présence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absencesParMatiere as $matiereId => $statistiques)
                            <tr>
                                <td>{{ $statistiques['nom'] }}</td>
                                <td>{{ $statistiques['total'] }}</td>
                                <td>{{ $statistiques['present'] }}</td>
                                <td>{{ $statistiques['absent'] }}</td>
                                <td>{{ $statistiques['retard'] }}</td>
                                <td>{{ $statistiques['excuse'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 5px; width: 100px;">
                                            <div class="progress-bar {{ $statistiques['taux_presence'] >= 75 ? 'bg-success' : ($statistiques['taux_presence'] >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                                 role="progressbar"
                                                 style="width: {{ $statistiques['taux_presence'] }}%"
                                                 aria-valuenow="{{ $statistiques['taux_presence'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-2">{{ $statistiques['taux_presence'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune donnée disponible</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Liste des absences -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">Historique des absences</h5>
        </div>
        <div class="card-body">
            @if($absences->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Aucune absence enregistrée sur la période sélectionnée.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Matière</th>
                                <th>Type de Séance</th>
                                <th>Statut</th>
                                <th>Justification</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absences as $absence)
                                <tr id="absence_{{ $absence->seanceCours->id ?? 'unknown' }}_{{ $absence->date ? $absence->date->format('Y-m-d') : 'unknown' }}"
                                    class="{{ request('highlight') == 'absence_' . ($absence->seanceCours->id ?? 'unknown') . '_' . ($absence->date ? $absence->date->format('Y-m-d') : 'unknown') ? 'highlighted-row' : '' }}">
                                    <td>{{ $absence->date ? $absence->date->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="subject-icon rounded-circle p-2 me-2 bg-light">
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <span>{{ $absence->seanceCours->matiere->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $absence->seanceCours->type_cours ?? 'N/A' }}</td>
                                    <td>
                                        @if($absence->statut == 'excuse')
                                            <span class="badge bg-success-light text-success">Justifiée</span>
                                        @elseif($absence->justified_at && $absence->statut == 'absent')
                                            <span class="badge bg-warning-light text-warning">En attente de validation</span>
                                        @else
                                            <span class="badge bg-danger-light text-danger">Non justifiée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $hasAdminComment = false;
                                            $adminComment = '';
                                            $studentComment = $absence->commentaire ?? '';

                                            // Check if commentaire contains admin comment
                                            if (strpos($studentComment, "Commentaire de l'administration:") !== false) {
                                                $parts = explode("Commentaire de l'administration:", $studentComment);
                                                $studentComment = trim($parts[0]);
                                                $adminComment = trim($parts[1] ?? '');
                                                $hasAdminComment = true;
                                            }
                                        @endphp

                                        <div class="mb-1">
                                            <strong>Justification :</strong>
                                            <span class="text-muted">{{ Str::limit($studentComment, 100) }}</span>
                                            @if(strlen($studentComment) > 100)
                                                <a href="#" class="small text-primary" data-bs-toggle="modal" data-bs-target="#justificationModal{{ $absence->id }}">
                                                    Voir plus
                                                </a>
                                            @endif
                                        </div>

                                        @if($hasAdminComment)
                                            <div class="mt-2 p-2 border-start border-danger border-3 bg-light">
                                                <strong class="text-danger">Commentaire de l'administration :</strong>
                                                <p class="mb-0">{{ $adminComment }}</p>
                                            </div>
                                        @endif

                                        @if($absence->document_path)
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $absence->document_path) }}" target="_blank" class="text-primary">
                                                    <i class="fas fa-paperclip"></i> Document justificatif
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($absence->statut != 'excuse' && !$absence->justified_at)
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#justifierModal{{ $absence->id }}">
                                                <i class="fas fa-file-alt me-1"></i> Justifier
                                            </button>
                                        @elseif($absence->justified_at && $absence->statut == 'absent' && $hasAdminComment)
                                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resoumettreModal{{ $absence->id }}">
                                                <i class="fas fa-redo me-1"></i> Re-soumettre
                                            </button>
                                        @elseif($absence->justified_at && $absence->statut == 'absent')
                                            <span class="badge bg-secondary">En attente d'examen</span>
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

<!-- Modals de justification -->
@foreach($absences as $absence)
    @php
        $hasAdminComment = false;
        $adminComment = '';
        $studentComment = $absence->commentaire ?? '';

        // Check if commentaire contains admin comment
        if (strpos($studentComment, "Commentaire de l'administration:") !== false) {
            $parts = explode("Commentaire de l'administration:", $studentComment);
            $studentComment = trim($parts[0]);
            $adminComment = trim($parts[1] ?? '');
            $hasAdminComment = true;
        }
    @endphp

    @if($absence->statut != 'excuse' && !$absence->justified_at)
        <div class="modal fade" id="justifierModal{{ $absence->id }}" tabindex="-1" aria-labelledby="justifierModalLabel{{ $absence->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="justifierModalLabel{{ $absence->id }}">
                            Justifier l'absence du {{ $absence->date ? $absence->date->format('d/m/Y') : 'N/A' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('esbtp.esbtp.mes-absences.justify', $absence->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="justification{{ $absence->id }}" class="form-label">Motif de l'absence</label>
                                <textarea class="form-control" id="justification{{ $absence->id }}" name="justification" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="document{{ $absence->id }}" class="form-label">Document justificatif</label>
                                <input type="file" class="form-control" id="document{{ $absence->id }}" name="document" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Formats acceptés: PDF, JPG, PNG. Max: 2MB</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($absence->justified_at && $absence->statut == 'absent' && $hasAdminComment)
        <div class="modal fade" id="resoumettreModal{{ $absence->id }}" tabindex="-1" aria-labelledby="resoumettreModalLabel{{ $absence->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resoumettreModalLabel{{ $absence->id }}">
                            Re-soumettre une justification pour l'absence du {{ $absence->date ? $absence->date->format('d/m/Y') : 'N/A' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('esbtp.esbtp.mes-absences.justify', $absence->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Justification rejetée</strong>
                                <p class="mb-0">Votre précédente justification a été rejetée par l'administration. Veuillez fournir des informations complémentaires.</p>
                            </div>

                            <div class="mb-3 p-3 bg-light rounded">
                                <h6 class="text-danger">Commentaire de l'administration :</h6>
                                <p class="mb-0">{{ $adminComment }}</p>
                            </div>

                            <div class="mb-3">
                                <label for="justification{{ $absence->id }}" class="form-label">Nouvelle justification</label>
                                <textarea class="form-control" id="justification{{ $absence->id }}" name="justification" rows="3" required>{{ $studentComment }}</textarea>
                                <small class="text-muted">Vous pouvez modifier votre justification précédente ou en fournir une nouvelle.</small>
                            </div>

                            <div class="mb-3">
                                <label for="document{{ $absence->id }}" class="form-label">Document justificatif</label>
                                <input type="file" class="form-control" id="document{{ $absence->id }}" name="document" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Formats acceptés: PDF, JPG, PNG. Max: 2MB</small>

                                @if($absence->document_path)
                                <div class="mt-2">
                                    <span class="text-muted">Document actuel :</span>
                                    <a href="{{ asset('storage/' . $absence->document_path) }}" target="_blank" class="text-primary">
                                        <i class="fas fa-paperclip"></i> Voir le document
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-redo me-1"></i> Re-soumettre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Add modal for showing the full justification -->
@foreach($absences as $absence)
    @php
        $hasAdminComment = false;
        $adminComment = '';
        $studentComment = $absence->commentaire ?? '';

        // Check if commentaire contains admin comment
        if (strpos($studentComment, "Commentaire de l'administration:") !== false) {
            $parts = explode("Commentaire de l'administration:", $studentComment);
            $studentComment = trim($parts[0]);
            $adminComment = trim($parts[1] ?? '');
            $hasAdminComment = true;
        }
    @endphp

    <div class="modal fade" id="justificationModal{{ $absence->id }}" tabindex="-1" aria-labelledby="justificationModalLabel{{ $absence->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="justificationModalLabel{{ $absence->id }}">Détails de la justification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6>Votre justification :</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $studentComment }}
                        </div>
                    </div>

                    @if($hasAdminComment)
                        <div class="mb-3">
                            <h6 class="text-danger">Commentaire de l'administration :</h6>
                            <div class="p-3 bg-light rounded border-start border-danger border-3">
                                {{ $adminComment }}
                            </div>
                        </div>
                    @endif

                    @if($absence->document_path)
                        <div class="mb-3">
                            <h6>Document justificatif :</h6>
                            <a href="{{ asset('storage/' . $absence->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file"></i> Voir le document
                            </a>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('styles')
<style>
    .stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .subject-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    .progress {
        background-color: #f8f9fa;
    }
    .chart-container {
        position: relative;
        width: 100%;
    }
    .highlighted-row {
        background-color: rgba(242, 148, 0, 0.2) !important;
        animation: highlight-fade 2s ease-in-out;
    }
    @keyframes highlight-fade {
        0%, 100% { background-color: rgba(242, 148, 0, 0.2); }
        50% { background-color: rgba(242, 148, 0, 0.4); }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des couleurs
    const colors = {
        present: 'rgba(40, 167, 69, 0.7)',
        absent: 'rgba(220, 53, 69, 0.7)',
        retard: 'rgba(255, 193, 7, 0.7)',
        excuse: 'rgba(23, 162, 184, 0.7)',
        border: {
            present: 'rgb(40, 167, 69)',
            absent: 'rgb(220, 53, 69)',
            retard: 'rgb(255, 193, 7)',
            excuse: 'rgb(23, 162, 184)'
        }
    };

    // Graphique des absences par matière
    const absencesParMatiereCtx = document.getElementById('absencesParMatiereChart').getContext('2d');
    const absencesParMatiereData = {
        labels: {!! json_encode(collect($absencesParMatiere)->pluck('nom')->toArray()) !!},
        datasets: [
            {
                label: 'Présences',
                data: {!! json_encode(collect($absencesParMatiere)->pluck('present')->toArray()) !!},
                backgroundColor: colors.present,
                borderColor: colors.border.present,
                borderWidth: 1
            },
            {
                label: 'Absences',
                data: {!! json_encode(collect($absencesParMatiere)->pluck('absent')->toArray()) !!},
                backgroundColor: colors.absent,
                borderColor: colors.border.absent,
                borderWidth: 1
            },
            {
                label: 'Retards',
                data: {!! json_encode(collect($absencesParMatiere)->pluck('retard')->toArray()) !!},
                backgroundColor: colors.retard,
                borderColor: colors.border.retard,
                borderWidth: 1
            },
            {
                label: 'Excusés',
                data: {!! json_encode(collect($absencesParMatiere)->pluck('excuse')->toArray()) !!},
                backgroundColor: colors.excuse,
                borderColor: colors.border.excuse,
                borderWidth: 1
            }
        ]
    };

    new Chart(absencesParMatiereCtx, {
        type: 'bar',
        data: absencesParMatiereData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique d'évolution des absences
    const evolutionAbsencesCtx = document.getElementById('evolutionAbsencesChart').getContext('2d');
    const absencesMensuelles = {!! json_encode($absencesMensuelles) !!};

    const labels = Object.keys(absencesMensuelles).map(month => {
        const [year, monthNum] = month.split('-');
        const date = new Date(year, monthNum - 1);
        return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
    });

    const data = Object.values(absencesMensuelles);

    new Chart(evolutionAbsencesCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre d\'absences',
                data: data,
                fill: false,
                borderColor: colors.border.absent,
                backgroundColor: colors.absent,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Faire défiler automatiquement jusqu'à la ligne en surbrillance
    const highlightedRow = document.querySelector('.highlighted-row');
    if (highlightedRow) {
        setTimeout(() => {
            highlightedRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Ouvrir automatiquement le modal de justification après un court délai
            setTimeout(() => {
                const absenceId = highlightedRow.id.split('_')[0]; // Récupérer l'ID de l'absence
                const justifierBtn = highlightedRow.querySelector('button[data-bs-toggle="modal"]');
                if (justifierBtn) {
                    justifierBtn.click();
                }
            }, 1000);
        }, 500);
    }
});
</script>
@endpush
