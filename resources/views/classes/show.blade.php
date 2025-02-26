@extends('layouts.app')

@section('title', 'Détails de la Classe')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $class->name }}</h1>
        <div>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            @endif
            <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="row">
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
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
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
                                Nombre de Sections</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $class->sections->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
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
                                Enseignants Assignés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeachers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
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
                                Moyenne de la Classe</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classAverage ?? 'N/A' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails de la classe -->
    <div class="row">
        <!-- Informations de base -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de Base</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Niveau :</th>
                            <td>{{ $class->level }}ème Année</td>
                        </tr>
                        <tr>
                            <th>Année Académique :</th>
                            <td>{{ $class->academic_year }}</td>
                        </tr>
                        <tr>
                            <th>Statut :</th>
                            <td>
                                @if($class->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Description :</th>
                            <td>{{ $class->description ?: 'Aucune description' }}</td>
                        </tr>
                        <tr>
                            <th>Professeur Principal :</th>
                            <td>
                                @if($class->mainTeacher)
                                    {{ $class->mainTeacher->full_name }}
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Liste des sections -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Sections</h6>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                            <i class="fas fa-plus"></i> Ajouter une Section
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Section</th>
                                    <th>Capacité</th>
                                    <th>Étudiants</th>
                                    <th>Taux de Remplissage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($class->sections as $section)
                                    <tr>
                                        <td>{{ $section->name }}</td>
                                        <td>{{ $section->capacity }}</td>
                                        <td>{{ $section->students_count ?? 0 }}</td>
                                        <td>
                                            @php
                                                $fillRate = $section->capacity > 0 
                                                    ? (($section->students_count ?? 0) / $section->capacity) * 100 
                                                    : 0;
                                            @endphp
                                            <div class="progress">
                                                <div class="progress-bar {{ $fillRate > 90 ? 'bg-danger' : ($fillRate > 70 ? 'bg-warning' : 'bg-success') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $fillRate }}%"
                                                     aria-valuenow="{{ $fillRate }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                    {{ number_format($fillRate, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sections.show', $section->id) }}" 
                                                   class="btn btn-sm btn-info" title="Voir les étudiants">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                                @if(auth()->check() && auth()->user()->role === 'admin')
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="confirmDeleteSection({{ $section->id }})"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune section trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des enseignants -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Enseignants Assignés</h6>
            @if(auth()->check() && auth()->user()->role === 'admin')
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">
                    <i class="fas fa-plus"></i> Assigner un Enseignant
                </button>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Enseignant</th>
                            <th>Matière</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers ?? [] as $teacher)
                            <tr>
                                <td>{{ $teacher->full_name }}</td>
                                <td>{{ $teacher->subject }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->phone }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teachers.show', $teacher->id) }}" 
                                           class="btn btn-sm btn-info" title="Voir le profil">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmUnassignTeacher({{ $teacher->id }})"
                                                    title="Retirer">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucun enseignant assigné</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de section -->
@if(auth()->check() && auth()->user()->role === 'admin')
    <div class="modal fade" id="addSectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('sections.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $class->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="section_name" class="form-label">Nom de la Section</label>
                            <input type="text" class="form-control" id="section_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="capacity" class="form-label">Capacité</label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   min="1" value="30" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'assignation d'enseignant -->
    <div class="modal fade" id="assignTeacherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assigner un Enseignant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('classes.assign-teacher', $class->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="teacher_id" class="form-label">Enseignant</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">Sélectionner un enseignant</option>
                                @foreach($availableTeachers ?? [] as $teacher)
                                    <option value="{{ $teacher->id }}">
                                        {{ $teacher->full_name }} - {{ $teacher->subject }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Matière</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Assigner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Scripts pour la gestion des actions -->
<script>
function confirmDeleteSection(sectionId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette section ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/sections/${sectionId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function confirmUnassignTeacher(teacherId) {
    if (confirm('Êtes-vous sûr de vouloir retirer cet enseignant de la classe ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('classes.unassign-teacher', $class->id) }}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
            <input type="hidden" name="teacher_id" value="${teacherId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection 