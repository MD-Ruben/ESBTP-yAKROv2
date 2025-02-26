@extends('layouts.app')

@section('title', 'Marquer les présences')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Marquer les présences</h5>
                </div>
                <div class="card-body">
                    <form id="attendance-filter-form">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="class-id" class="form-label">Classe</label>
                                <select id="class-id" name="class_id" class="form-select" required>
                                    <option value="">Sélectionner une classe</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="section-id" class="form-label">Section</label>
                                <select id="section-id" name="section_id" class="form-select" required>
                                    <option value="">Sélectionner une section</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" data-class="{{ $section->class_id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="attendance-date" class="form-label">Date</label>
                                <input type="date" id="attendance-date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Afficher
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($students) && $students->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Liste des étudiants</h5>
                    <div id="mark-all-container">
                        <button id="mark-all-present" class="btn btn-success">Tous présents</button>
                        <button id="mark-all-absent" class="btn btn-danger ms-2">Tous absents</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Numéro d'étudiant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $index => $student)
                                    @php
                                        $attendance = $attendances->get($student->id);
                                        $status = $attendance ? $attendance->status : null;
                                        $rowClass = '';
                                        
                                        if ($status === 'present') {
                                            $rowClass = 'table-success';
                                        } elseif ($status === 'absent') {
                                            $rowClass = 'table-danger';
                                        } elseif ($status === 'late') {
                                            $rowClass = 'table-warning';
                                        } elseif ($status === 'excused') {
                                            $rowClass = 'table-info';
                                        }
                                    @endphp
                                    <tr class="{{ $rowClass }}" data-student-id="{{ $student->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $student->user->name }}</td>
                                        <td>{{ $student->student_id }}</td>
                                        <td class="status-cell">
                                            @if($status)
                                                @if($status === 'present')
                                                    <span class="badge bg-success">Présent</span>
                                                @elseif($status === 'absent')
                                                    <span class="badge bg-danger">Absent</span>
                                                @elseif($status === 'late')
                                                    <span class="badge bg-warning text-dark">En retard</span>
                                                @elseif($status === 'excused')
                                                    <span class="badge bg-info text-dark">Excusé</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Non marqué</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                    class="btn btn-sm {{ $status === 'present' ? 'btn-success' : 'btn-outline-success' }} attendance-btn" 
                                                    data-student-id="{{ $student->id }}" 
                                                    data-date="{{ $date }}" 
                                                    data-status="present"
                                                    data-original-status="{{ $status }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                    class="btn btn-sm {{ $status === 'absent' ? 'btn-danger' : 'btn-outline-danger' }} attendance-btn" 
                                                    data-student-id="{{ $student->id }}" 
                                                    data-date="{{ $date }}" 
                                                    data-status="absent"
                                                    data-original-status="{{ $status }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button type="button" 
                                                    class="btn btn-sm {{ $status === 'late' ? 'btn-warning' : 'btn-outline-warning' }} attendance-btn" 
                                                    data-student-id="{{ $student->id }}" 
                                                    data-date="{{ $date }}" 
                                                    data-status="late"
                                                    data-original-status="{{ $status }}">
                                                    <i class="fas fa-clock"></i>
                                                </button>
                                                <button type="button" 
                                                    class="btn btn-sm {{ $status === 'excused' ? 'btn-info' : 'btn-outline-info' }} attendance-btn" 
                                                    data-student-id="{{ $student->id }}" 
                                                    data-date="{{ $date }}" 
                                                    data-status="excused"
                                                    data-original-status="{{ $status }}">
                                                    <i class="fas fa-notes-medical"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif(request('class_id') && request('section_id'))
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                Aucun étudiant trouvé pour cette classe et section.
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<!-- Inclure Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Inclure nos fichiers JavaScript -->
<script src="{{ asset('js/ajax-helpers.js') }}"></script>
<script src="{{ asset('js/attendance.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtrer les sections en fonction de la classe sélectionnée
        const classSelect = document.getElementById('class-id');
        const sectionSelect = document.getElementById('section-id');
        
        function filterSections() {
            const classId = classSelect.value;
            
            // Cacher toutes les options de section
            Array.from(sectionSelect.options).forEach(option => {
                if (option.value === '') {
                    // Ne pas cacher l'option par défaut
                    option.style.display = '';
                } else {
                    const sectionClassId = option.getAttribute('data-class');
                    
                    if (classId === '' || sectionClassId === classId) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Réinitialiser la sélection si la section actuelle n'est pas valide pour la classe
            const currentSectionOption = sectionSelect.options[sectionSelect.selectedIndex];
            if (currentSectionOption.style.display === 'none') {
                sectionSelect.value = '';
            }
        }
        
        // Appliquer le filtre au chargement de la page
        filterSections();
        
        // Appliquer le filtre lorsque la classe change
        classSelect.addEventListener('change', filterSections);
    });
</script>
@endsection