@extends('layouts.app')

@section('title', 'Gestion des Présences')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Présences</h1>
        <a href="{{ route('attendance.mark-page') }}" class="btn btn-primary">
            <i class="fas fa-clipboard-check"></i> Marquer les Présences
        </a>
    </div>

    <!-- Carte pour la recherche et le filtrage -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recherche et Filtrage</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('attendances.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="class_id" class="form-label">Classe</label>
                    <select class="form-select" id="class_id" name="class_id">
                        <option value="">Toutes les classes</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="section_id" class="form-label">Section</label>
                    <select class="form-select" id="section_id" name="section_id">
                        <option value="">Toutes les sections</option>
                        @foreach($sections ?? [] as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistiques de présence -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Étudiants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Présents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $presentCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Absents</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absentCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
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
                                Taux de Présence</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($presentCount, $totalStudents) && $totalStudents > 0 
                                   ? number_format(($presentCount / $totalStudents) * 100, 1) . '%' 
                                   : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des présences -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Registre des Présences</h6>
            <div>
                <a href="{{ route('attendance.report') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-file-alt"></i> Rapport Détaillé
                </a>
                <button class="btn btn-sm btn-success" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimer
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Étudiant</th>
                            <th>Classe</th>
                            <th>Section</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Remarque</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances ?? [] as $attendance)
                            <tr>
                                <td>{{ $attendance->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($attendance->student->photo)
                                            <img src="{{ asset('storage/' . $attendance->student->photo) }}" 
                                                 alt="Photo" class="rounded-circle me-2" width="40" height="40">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" 
                                                 alt="Default" class="rounded-circle me-2" width="40" height="40">
                                        @endif
                                        <div>
                                            <div>{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</div>
                                            <small class="text-muted">{{ $attendance->student->admission_no }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $attendance->student->class->name ?? 'N/A' }}</td>
                                <td>{{ $attendance->student->section->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                <td>
                                    @if($attendance->status == 'present')
                                        <span class="badge bg-success">Présent</span>
                                    @elseif($attendance->status == 'absent')
                                        <span class="badge bg-danger">Absent</span>
                                    @elseif($attendance->status == 'late')
                                        <span class="badge bg-warning">En retard</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $attendance->remark ?? 'Aucune remarque' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('attendance.student', $attendance->student_id) }}" 
                                           class="btn btn-sm btn-info" title="Historique">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal{{ $attendance->id }}" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de modification -->
                                    <div class="modal fade" id="editModal{{ $attendance->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel">Modifier la Présence</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="status{{ $attendance->id }}" class="form-label">Statut</label>
                                                            <select class="form-select" id="status{{ $attendance->id }}" name="status" required>
                                                                <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Présent</option>
                                                                <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                                                <option value="late" {{ $attendance->status == 'late' ? 'selected' : '' }}>En retard</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="remark{{ $attendance->id }}" class="form-label">Remarque</label>
                                                            <textarea class="form-control" id="remark{{ $attendance->id }}" name="remark" rows="3">{{ $attendance->remark }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucune donnée de présence trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($attendances) && $attendances->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Script pour le sélecteur de sections dépendant de la classe -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('class_id');
        const sectionSelect = document.getElementById('section_id');
        
        if (classSelect && sectionSelect) {
            classSelect.addEventListener('change', function() {
                const classId = this.value;
                
                // Réinitialiser le sélecteur de sections
                sectionSelect.innerHTML = '<option value="">Toutes les sections</option>';
                
                if (classId) {
                    // Charger les sections pour cette classe
                    fetch(`/api/sections/by-class/${classId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.id;
                                option.textContent = section.name;
                                sectionSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Erreur lors du chargement des sections:', error));
                }
            });
        }
    });
</script>
@endsection 