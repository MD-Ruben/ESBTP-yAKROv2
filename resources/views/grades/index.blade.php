@extends('layouts.app')

@section('title', 'Gestion des Notes')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Notes</h1>
        <a href="{{ route('grades.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Ajouter des Notes
        </a>
    </div>

    <!-- Carte pour la recherche et le filtrage -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recherche et Filtrage</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('grades.index') }}" method="GET" class="row g-3">
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
                    <label for="subject_id" class="form-label">Matière</label>
                    <select class="form-select" id="subject_id" name="subject_id">
                        <option value="">Toutes les matières</option>
                        @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="semester_id" class="form-label">Semestre</label>
                    <select class="form-select" id="semester_id" name="semester_id">
                        <option value="">Tous les semestres</option>
                        @foreach($semesters ?? [] as $semester)
                            <option value="{{ $semester->id }}" {{ request('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="exam_id" class="form-label">Examen</label>
                    <select class="form-select" id="exam_id" name="exam_id">
                        <option value="">Tous les examens</option>
                        @foreach($exams ?? [] as $exam)
                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('grades.index') }}" class="btn btn-secondary">
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

    <!-- Statistiques des notes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Notes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGrades ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
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
                                Note Moyenne</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($averageGrade) ? number_format($averageGrade, 2) : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                Note la Plus Élevée</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $highestGrade ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
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
                                Taux de Réussite</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($passRate) ? number_format($passRate, 1) . '%' : 'N/A' }}
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

    <!-- Tableau des notes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Registre des Notes</h6>
            <div>
                <a href="{{ route('grades.calculate') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-calculator"></i> Calculer les Moyennes
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
                            <th>Matière</th>
                            <th>Examen</th>
                            <th>Note</th>
                            <th>Sur</th>
                            <th>Pourcentage</th>
                            <th>Remarque</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades ?? [] as $grade)
                            <tr>
                                <td>{{ $grade->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($grade->student->photo)
                                            <img src="{{ asset('storage/' . $grade->student->photo) }}" 
                                                 alt="Photo" class="rounded-circle me-2" width="40" height="40">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" 
                                                 alt="Default" class="rounded-circle me-2" width="40" height="40">
                                        @endif
                                        <div>
                                            <div>{{ $grade->student->first_name }} {{ $grade->student->last_name }}</div>
                                            <small class="text-muted">{{ $grade->student->admission_no }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $grade->student->class->name ?? 'N/A' }}</td>
                                <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                                <td>{{ $grade->exam->name ?? 'N/A' }}</td>
                                <td>{{ $grade->marks_obtained }}</td>
                                <td>{{ $grade->total_marks }}</td>
                                <td>
                                    @php
                                        $percentage = ($grade->marks_obtained / $grade->total_marks) * 100;
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar 
                                                @if($percentage >= 80) bg-success 
                                                @elseif($percentage >= 60) bg-info 
                                                @elseif($percentage >= 40) bg-warning 
                                                @else bg-danger @endif" 
                                                role="progressbar" 
                                                style="width: {{ $percentage }}%" 
                                                aria-valuenow="{{ $percentage }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span>{{ number_format($percentage, 1) }}%</span>
                                    </div>
                                </td>
                                <td>{{ $grade->remark ?? 'Aucune remarque' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('grades.report', $grade->student_id) }}" 
                                           class="btn btn-sm btn-info" title="Rapport">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        <a href="{{ route('grades.edit', $grade->id) }}" 
                                           class="btn btn-sm btn-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $grade->id }}" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $grade->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer cette note pour <strong>{{ $grade->student->first_name }} {{ $grade->student->last_name }}</strong> ?
                                                    <p class="text-danger mt-2">Cette action est irréversible.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('grades.destroy', $grade->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Aucune note trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($grades) && $grades->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $grades->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Carte pour le bulletin scolaire -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Générer un Bulletin Scolaire</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('grades.bulletin', ['student' => 0, 'semester' => 0]) }}" method="GET" class="row g-3" id="bulletinForm">
                <div class="col-md-4">
                    <label for="student_id" class="form-label">Étudiant</label>
                    <select class="form-select" id="student_id" name="student_id" required>
                        <option value="">Sélectionner un étudiant</option>
                        @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->admission_no }} - {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="semester_id_bulletin" class="form-label">Semestre</label>
                    <select class="form-select" id="semester_id_bulletin" name="semester_id" required>
                        <option value="">Sélectionner un semestre</option>
                        @foreach($semesters ?? [] as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Générer le Bulletin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour le sélecteur de sections dépendant de la classe -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('class_id');
        const sectionSelect = document.getElementById('section_id');
        const bulletinForm = document.getElementById('bulletinForm');
        const studentSelect = document.getElementById('student_id');
        const semesterSelect = document.getElementById('semester_id_bulletin');
        
        // Gestion du sélecteur de sections dépendant de la classe
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
        
        // Gestion du formulaire de bulletin
        if (bulletinForm && studentSelect && semesterSelect) {
            bulletinForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const studentId = studentSelect.value;
                const semesterId = semesterSelect.value;
                
                if (studentId && semesterId) {
                    window.location.href = `/grades/bulletin/${studentId}/${semesterId}`;
                } else {
                    alert('Veuillez sélectionner un étudiant et un semestre.');
                }
            });
        }
    });
</script>
@endsection 