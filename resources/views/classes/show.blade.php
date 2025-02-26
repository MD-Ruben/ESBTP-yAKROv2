@extends('layouts.app')

@section('title', 'Détails de la Classe')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec informations de base de la classe -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-2 text-esbtp-green">{{ $class->name }}</h1>
                            <p class="text-muted">
                                <span class="badge bg-esbtp-green">{{ $class->level }}</span>
                                <span class="ms-2">Année académique: {{ $class->academic_year }}</span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-outline-esbtp-orange me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('classes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations détaillées et statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Informations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Niveau
                            <span class="badge bg-esbtp-orange">{{ $class->level }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Capacité maximale
                            <span>{{ $class->max_students }} étudiants</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Étudiants inscrits
                            <span>{{ $class->students_count ?? 0 }} étudiants</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Places disponibles
                            <span>{{ $class->max_students - ($class->students_count ?? 0) }} places</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Année académique
                            <span>{{ $class->academic_year }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0"><i class="fas fa-align-left"></i> Description</h5>
                </div>
                <div class="card-body">
                    <p>{{ $class->description ?? 'Aucune description disponible pour cette classe.' }}</p>
                    
                    <div class="mt-4">
                        <h6 class="text-esbtp-green">Sections</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse($class->sections ?? [] as $section)
                                <span class="badge bg-esbtp-orange-light text-dark p-2">
                                    {{ $section->name }} ({{ $section->students_count ?? 0 }} étudiants)
                                </span>
                            @empty
                                <p class="text-muted">Aucune section n'a été créée pour cette classe.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglets pour les étudiants, enseignants et emploi du temps -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="classDetailsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab" aria-controls="students" aria-selected="true">
                                <i class="fas fa-user-graduate"></i> Étudiants
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-controls="teachers" aria-selected="false">
                                <i class="fas fa-chalkboard-teacher"></i> Enseignants
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timetable-tab" data-bs-toggle="tab" data-bs-target="#timetable" type="button" role="tab" aria-controls="timetable" aria-selected="false">
                                <i class="fas fa-calendar-alt"></i> Emploi du temps
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="classDetailsTabsContent">
                        <!-- Onglet Étudiants -->
                        <div class="tab-pane fade show active" id="students" role="tabpanel" aria-labelledby="students-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-esbtp-green">Liste des étudiants inscrits</h5>
                                <a href="{{ route('students.create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-esbtp-green">
                                    <i class="fas fa-plus"></i> Ajouter un étudiant
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-esbtp-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Section</th>
                                            <th>Date d'admission</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($class->students ?? [] as $student)
                                            <tr>
                                                <td>{{ $student->registration_number }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($student->profile_image)
                                                            <img src="{{ asset('storage/' . $student->profile_image) }}" alt="{{ $student->name }}" class="rounded-circle me-2" width="32" height="32">
                                                        @else
                                                            <div class="bg-esbtp-orange-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                                <span class="text-esbtp-orange">{{ substr($student->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                        {{ $student->name }}
                                                    </div>
                                                </td>
                                                <td>{{ $student->email }}</td>
                                                <td>{{ $student->section->name ?? 'Non assigné' }}</td>
                                                <td>{{ $student->admission_date ? date('d/m/Y', strtotime($student->admission_date)) : 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-esbtp-green">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-outline-esbtp-orange">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <p class="text-muted mb-0">Aucun étudiant inscrit dans cette classe.</p>
                                                    <a href="{{ route('students.create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-esbtp-green mt-2">
                                                        <i class="fas fa-plus"></i> Ajouter un étudiant
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Onglet Enseignants -->
                        <div class="tab-pane fade" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-esbtp-green">Enseignants assignés</h5>
                                <a href="{{ route('timetables.create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-esbtp-green">
                                    <i class="fas fa-plus"></i> Assigner un enseignant
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-esbtp-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Matière</th>
                                            <th>Heures par semaine</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($class->teachers ?? [] as $teacher)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($teacher->profile_image)
                                                            <img src="{{ asset('storage/' . $teacher->profile_image) }}" alt="{{ $teacher->name }}" class="rounded-circle me-2" width="32" height="32">
                                                        @else
                                                            <div class="bg-esbtp-green-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                                <span class="text-esbtp-green">{{ substr($teacher->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                        {{ $teacher->name }}
                                                    </div>
                                                </td>
                                                <td>{{ $teacher->email }}</td>
                                                <td>{{ $teacher->pivot->subject->name ?? 'Non spécifié' }}</td>
                                                <td>{{ $teacher->pivot->hours_per_week ?? 'N/A' }} heures</td>
                                                <td>
                                                    <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-sm btn-outline-esbtp-green">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('timetables.edit', $teacher->pivot->id ?? 0) }}" class="btn btn-sm btn-outline-esbtp-orange">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <p class="text-muted mb-0">Aucun enseignant assigné à cette classe.</p>
                                                    <a href="{{ route('timetables.create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-esbtp-green mt-2">
                                                        <i class="fas fa-plus"></i> Assigner un enseignant
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Onglet Emploi du temps -->
                        <div class="tab-pane fade" id="timetable" role="tabpanel" aria-labelledby="timetable-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-esbtp-green">Emploi du temps de la classe</h5>
                                <a href="{{ route('timetables.class', $class->id) }}" class="btn btn-sm btn-esbtp-green">
                                    <i class="fas fa-external-link-alt"></i> Vue complète
                                </a>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-esbtp-green text-white">
                                        <tr>
                                            <th width="12%">Horaire</th>
                                            <th width="16%">Lundi</th>
                                            <th width="16%">Mardi</th>
                                            <th width="16%">Mercredi</th>
                                            <th width="16%">Jeudi</th>
                                            <th width="16%">Vendredi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $timeSlots = ['08:00 - 09:30', '09:45 - 11:15', '11:30 - 13:00', '14:00 - 15:30', '15:45 - 17:15'];
                                            $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
                                        @endphp
                                        
                                        @foreach($timeSlots as $timeSlot)
                                            <tr>
                                                <td class="bg-light">{{ $timeSlot }}</td>
                                                @foreach($days as $day)
                                                    <td>
                                                        @php
                                                            $entry = ($class->timetable ?? collect())->first(function($entry) use ($day, $timeSlot) {
                                                                return $entry->day == $day && $entry->time_slot == $timeSlot;
                                                            });
                                                        @endphp
                                                        
                                                        @if($entry)
                                                            <div class="p-1 bg-esbtp-orange-light rounded">
                                                                <div class="fw-bold text-esbtp-orange">{{ $entry->subject->name ?? 'N/A' }}</div>
                                                                <div class="small">{{ $entry->teacher->name ?? 'N/A' }}</div>
                                                                @if($entry->room)
                                                                    <div class="small text-muted">Salle: {{ $entry->room }}</div>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-center mt-3">
                                <a href="{{ route('timetables.create', ['class_id' => $class->id]) }}" class="btn btn-esbtp-orange">
                                    <i class="fas fa-plus"></i> Ajouter un cours
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Activer les onglets Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const triggerTabList = [].slice.call(document.querySelectorAll('#classDetailsTabs button'));
        triggerTabList.forEach(function(triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
        
        // Conserver l'onglet actif lors du rechargement de la page
        const activeTab = localStorage.getItem('activeClassTab');
        if (activeTab) {
            const tab = document.querySelector(`#classDetailsTabs button[data-bs-target="${activeTab}"]`);
            if (tab) {
                const instance = new bootstrap.Tab(tab);
                instance.show();
            }
        }
        
        // Enregistrer l'onglet actif lors du changement
        const tabs = document.querySelectorAll('#classDetailsTabs button');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('activeClassTab', event.target.getAttribute('data-bs-target'));
            });
        });
    });
</script>
@endsection 