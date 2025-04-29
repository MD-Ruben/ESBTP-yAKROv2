@extends('layouts.app')

@section('title', 'Gestion des présences - ESBTP-yAKRO')

@php
// Définir la fonction getInitials localement si elle n'existe pas
if (!function_exists('getInitials')) {
    function getInitials($name) {
        if (empty($name)) {
            return 'NA';
        }
        $words = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word[0])) {
                $initials .= strtoupper($word[0]);
                if (strlen($initials) >= 2) break;
            }
        }
        return $initials ?: 'NA';
    }
}
@endphp

@section('content')
<div class="container-fluid">
    <!-- Header and actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">Gestion des présences</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('esbtp.attendances.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-clipboard-check me-2"></i>Nouvelle feuille de présence
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des feuilles de présence</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAttendances }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Présences ce mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $attendancesThisMonth }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Taux de présence moyen</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $averageAttendanceRate }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                            style="width: {{ $averageAttendanceRate }}%" 
                                            aria-valuenow="{{ $averageAttendanceRate }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Classes suivies</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classesWithAttendance }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main content -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Liste des feuilles de présence
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" id="attendancesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Matière</th>
                                    <th>Classe</th>
                                    <th>Enseignant</th>
                                    <th>Taux de présence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                        <td>{{ $attendance->matiere->name ?? 'Non définie' }}</td>
                                        <td>{{ $attendance->classe->name ?? 'Non définie' }}</td>
                                        <td>{{ $attendance->teacher->name ?? 'Non défini' }}</td>
                                        <td>
                                            @php
                                                $totalStudents = $attendance->attendanceDetails->count();
                                                $presentStudents = $attendance->attendanceDetails->where('status', 'present')->count();
                                                $rate = $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100) : 0;
                                                
                                                if ($rate >= 75) {
                                                    $badgeClass = 'bg-success';
                                                } elseif ($rate >= 50) {
                                                    $badgeClass = 'bg-warning';
                                                } else {
                                                    $badgeClass = 'bg-danger';
                                                }
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar {{ $badgeClass }}" role="progressbar" style="width: {{ $rate }}%;" 
                                                        aria-valuenow="{{ $rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="badge {{ $badgeClass }}">{{ $rate }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.attendances.show', $attendance->id) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.attendances.edit', $attendance->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(auth()->user()->hasRole('superAdmin') && auth()->user()->can('delete_attendances'))
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $attendance->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>

                                            @if(auth()->user()->hasRole('superAdmin') && auth()->user()->can('delete_attendances'))
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $attendance->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $attendance->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $attendance->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                <strong>Attention :</strong> Cette action est irréversible.
                                                            </div>
                                                            <p>Êtes-vous sûr de vouloir supprimer cette feuille de présence ?</p>
                                                            <p><strong>Date :</strong> {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</p>
                                                            <p><strong>Matière :</strong> {{ $attendance->matiere->name ?? 'Non définie' }}</p>
                                                            <p><strong>Classe :</strong> {{ $attendance->classe->name ?? 'Non définie' }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.attendances.destroy', $attendance->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                <p class="mb-0">Aucune feuille de présence n'a été créée.</p>
                                                <a href="{{ route('esbtp.attendances.create') }}" class="btn btn-primary mt-3">
                                                    <i class="fas fa-plus-circle me-1"></i>Créer une feuille de présence
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with filters and quick actions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer les présences
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('esbtp.attendances.index') }}" method="GET" id="filterForm">
                        <div class="mb-3">
                            <label for="classe_id" class="form-label">Classe</label>
                            <select class="form-select select2" id="classe_id" name="classe_id">
                                <option value="">Toutes les classes</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                        {{ $classe->name }} ({{ $classe->filiere->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Enseignant</label>
                            <select class="form-select select2" id="teacher_id" name="teacher_id">
                                <option value="">Tous les enseignants</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="matiere_id" class="form-label">Matière</label>
                            <select class="form-select select2" id="matiere_id" name="matiere_id">
                                <option value="">Toutes les matières</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                        {{ $matiere->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_range" class="form-label">Période</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}" placeholder="Date de début">
                                <span class="input-group-text">au</span>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}" placeholder="Date de fin">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Appliquer les filtres
                            </button>
                            <a href="{{ route('esbtp.attendances.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo-alt me-2"></i>Réinitialiser les filtres
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('esbtp.attendances.create') }}" class="btn btn-success">
                            <i class="fas fa-clipboard-check me-2"></i>Nouvelle feuille de présence
                        </a>
                        <a href="{{ route('esbtp.attendance-reports') }}" class="btn btn-info">
                            <i class="fas fa-chart-bar me-2"></i>Rapports de présence
                        </a>
                        <a href="{{ route('esbtp.export-attendances') }}" class="btn btn-primary">
                            <i class="fas fa-file-export me-2"></i>Exporter les données
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        const table = $('#attendancesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']], // Sort by date descending
            columnDefs: [
                { responsivePriority: 1, targets: [0, 1, 5] },
                { responsivePriority: 2, targets: [2, 4] }
            ]
        });

        // Initialize Select2 for enhanced selects
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Submit filter form when selects change
        $('#classe_id, #teacher_id, #matiere_id').change(function() {
            $('#filterForm').submit();
        });
    });
</script>
@endsection
