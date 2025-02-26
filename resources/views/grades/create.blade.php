@extends('layouts.app')

@section('title', 'Saisie des Notes')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Saisie des Notes</h1>
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulaire de sélection -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sélection de la Classe et de la Matière</h6>
        </div>
        <div class="card-body">
            <form id="selectionForm" class="row g-3">
                <div class="col-md-4">
                    <label for="class_id" class="form-label">Classe</label>
                    <select class="form-select" id="class_id" name="class_id" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach($classes ?? [] as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="section_id" class="form-label">Section</label>
                    <select class="form-select" id="section_id" name="section_id" required>
                        <option value="">Sélectionner une section</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="subject_id" class="form-label">Matière</label>
                    <select class="form-select" id="subject_id" name="subject_id" required>
                        <option value="">Sélectionner une matière</option>
                        @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}" data-coefficient="{{ $subject->coefficient }}">
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="semester" class="form-label">Semestre</label>
                    <select class="form-select" id="semester" name="semester" required>
                        <option value="1">Premier Semestre</option>
                        <option value="2">Deuxième Semestre</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="exam_type" class="form-label">Type d'Évaluation</label>
                    <select class="form-select" id="exam_type" name="exam_type" required>
                        <option value="cc">Contrôle Continu</option>
                        <option value="exam">Examen</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire de saisie des notes -->
    <div class="card shadow mb-4" id="gradesCard" style="display: none;">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Saisie des Notes</h6>
            <div>
                <span class="me-3">Coefficient: <span id="coefficientDisplay">-</span></span>
                <button type="button" class="btn btn-success" id="saveAllBtn">
                    <i class="fas fa-save"></i> Enregistrer Toutes les Notes
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="gradesForm" action="{{ route('grades.store') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_id" id="hidden_subject_id">
                <input type="hidden" name="class_id" id="hidden_class_id">
                <input type="hidden" name="section_id" id="hidden_section_id">
                <input type="hidden" name="semester" id="hidden_semester">
                <input type="hidden" name="exam_type" id="hidden_exam_type">
                <input type="hidden" name="date" id="hidden_date">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N°</th>
                                <th>Matricule</th>
                                <th>Nom et Prénoms</th>
                                <th>Note (/20)</th>
                                <th>Appréciation</th>
                            </tr>
                        </thead>
                        <tbody id="studentsList">
                            <!-- Les étudiants seront chargés dynamiquement ici -->
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour la gestion dynamique du formulaire -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section_id');
    const subjectSelect = document.getElementById('subject_id');
    const semesterSelect = document.getElementById('semester');
    const examTypeSelect = document.getElementById('exam_type');
    const dateInput = document.getElementById('date');
    const gradesCard = document.getElementById('gradesCard');
    const coefficientDisplay = document.getElementById('coefficientDisplay');
    const studentsList = document.getElementById('studentsList');
    const gradesForm = document.getElementById('gradesForm');
    const saveAllBtn = document.getElementById('saveAllBtn');

    // Fonction pour mettre à jour les sections selon la classe
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        sectionSelect.innerHTML = '<option value="">Sélectionner une section</option>';
        
        if (classId) {
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
                .catch(error => console.error('Erreur:', error));
        }
    });

    // Fonction pour afficher le coefficient de la matière
    subjectSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const coefficient = selectedOption.dataset.coefficient || '-';
        coefficientDisplay.textContent = coefficient;
    });

    // Fonction pour charger les étudiants
    function loadStudents() {
        const classId = classSelect.value;
        const sectionId = sectionSelect.value;
        const subjectId = subjectSelect.value;
        const semester = semesterSelect.value;
        const examType = examTypeSelect.value;
        const date = dateInput.value;

        if (classId && sectionId && subjectId && semester && examType && date) {
            // Mettre à jour les champs cachés
            document.getElementById('hidden_subject_id').value = subjectId;
            document.getElementById('hidden_class_id').value = classId;
            document.getElementById('hidden_section_id').value = sectionId;
            document.getElementById('hidden_semester').value = semester;
            document.getElementById('hidden_exam_type').value = examType;
            document.getElementById('hidden_date').value = date;

            // Charger les étudiants
            fetch(`/api/students/by-section/${sectionId}`)
                .then(response => response.json())
                .then(data => {
                    studentsList.innerHTML = '';
                    data.forEach((student, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${student.admission_no}</td>
                            <td>${student.last_name} ${student.first_name}</td>
                            <td>
                                <input type="number" class="form-control grade-input" 
                                       name="grades[${student.id}][mark]" min="0" max="20" step="0.25" required>
                            </td>
                            <td>
                                <select class="form-select appreciation-select" name="grades[${student.id}][appreciation]" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Très bien">Très bien</option>
                                    <option value="Bien">Bien</option>
                                    <option value="Assez bien">Assez bien</option>
                                    <option value="Passable">Passable</option>
                                    <option value="Insuffisant">Insuffisant</option>
                                </select>
                            </td>
                        `;
                        studentsList.appendChild(row);
                    });

                    // Afficher le formulaire de notes
                    gradesCard.style.display = 'block';

                    // Gérer l'appréciation automatique
                    const gradeInputs = document.querySelectorAll('.grade-input');
                    gradeInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            const grade = parseFloat(this.value);
                            const appreciationSelect = this.closest('tr').querySelector('.appreciation-select');
                            
                            if (grade >= 16) {
                                appreciationSelect.value = 'Très bien';
                            } else if (grade >= 14) {
                                appreciationSelect.value = 'Bien';
                            } else if (grade >= 12) {
                                appreciationSelect.value = 'Assez bien';
                            } else if (grade >= 10) {
                                appreciationSelect.value = 'Passable';
                            } else {
                                appreciationSelect.value = 'Insuffisant';
                            }
                        });
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }
    }

    // Écouter les changements dans les champs de sélection
    [classSelect, sectionSelect, subjectSelect, semesterSelect, examTypeSelect, dateInput].forEach(element => {
        element.addEventListener('change', loadStudents);
    });

    // Gérer la soumission du formulaire
    saveAllBtn.addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir enregistrer toutes les notes ?')) {
            gradesForm.submit();
        }
    });
});
</script>
@endsection 