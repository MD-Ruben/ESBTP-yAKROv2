@extends('layouts.app')

@section('title', 'Gestion des Notes | ESBTP-yAKRO')

@section('page_title', 'Gestion des Notes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h3 class="card-title mb-0 text-primary">
                        <i class="fas fa-graduation-cap me-2"></i>Liste des Notes
                    </h3>
                    <div>
                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire') || auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant') || auth()->user()->can('create_grade'))
                        <a href="{{ route('esbtp.notes.create') }}" class="btn btn-primary shadow-sm">
                            <i class="fas fa-plus-circle me-1"></i> Ajouter une note
                        </a>
                        @endif
                        <a href="{{ route('esbtp.notes.index') }}" class="btn btn-outline-secondary ms-2 shadow-sm">
                            <i class="fas fa-sync me-1"></i> Actualiser
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-start border-success border-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-start border-danger border-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show shadow-sm border-start border-info border-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="card mb-4 shadow-sm border-0 bg-light">
                        <div class="card-body">
                            <h5 class="card-title mb-3 text-primary">
                                <i class="fas fa-filter me-2"></i>Filtres de recherche
                            </h5>
                            <form action="{{ route('esbtp.notes.index') }}" method="GET">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label for="classe_id" class="form-label text-muted small">Classe</label>
                                        <select class="form-select shadow-sm" id="classe_id" name="classe_id">
                                            <option value="">Toutes les classes</option>
                                            @foreach($classes as $classe)
                                                <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                                    {{ $classe->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="matiere_id" class="form-label text-muted small">Matière</label>
                                        <select class="form-select shadow-sm" id="matiere_id" name="matiere_id">
                                            <option value="">Toutes les matières</option>
                                            @foreach($matieres as $matiere)
                                                <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                                    {{ $matiere->name ?? $matiere->nom ?? 'Matière sans nom' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                            <i class="fas fa-search me-1"></i> Filtrer
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des notes -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 dataTable" id="notesTable">
                                    <thead class="bg-light text-primary">
                                        <tr>
                                            <th>Étudiant</th>
                                            <th>Classe</th>
                                            <th>Matière</th>
                                            <th>Évaluation</th>
                                            <th>Note</th>
                                            <th>Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($notes as $note)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <i class="fas fa-user-graduate fs-4 text-secondary"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-medium d-block">{{ $note->etudiant->nom }} {{ $note->etudiant->prenoms }}</span>
                                                            <small class="text-muted">{{ $note->etudiant->matricule ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $note->evaluation && $note->evaluation->classe ? $note->evaluation->classe->name : 'N/A' }}</td>
                                                <td>{{ $note->evaluation && $note->evaluation->matiere ? $note->evaluation->matiere->name : 'N/A' }}</td>
                                                <td>
                                                    @php
                                                        $typeIcons = [
                                                            'examen' => '<i class="fas fa-file-alt text-primary me-1"></i>',
                                                            'devoir' => '<i class="fas fa-pencil-alt text-success me-1"></i>',
                                                            'tp' => '<i class="fas fa-flask text-warning me-1"></i>',
                                                            'projet' => '<i class="fas fa-project-diagram text-info me-1"></i>',
                                                            'controle' => '<i class="fas fa-tasks text-secondary me-1"></i>',
                                                            'rattrapage' => '<i class="fas fa-redo text-danger me-1"></i>',
                                                        ];
                                                        $type = $note->evaluation ? $note->evaluation->type : '';
                                                        $icon = $typeIcons[$type] ?? '<i class="fas fa-question-circle text-muted me-1"></i>';
                                                    @endphp
                                                    <div>
                                                        <span class="d-block">{!! $icon !!} {{ $note->evaluation ? $note->evaluation->titre : 'N/A' }}</span>
                                                        @if($note->evaluation)
                                                            <small class="text-muted">{{ date('d/m/Y', strtotime($note->evaluation->date_evaluation)) }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($note->is_absent)
                                                        <span class="badge bg-danger rounded-pill shadow-sm">
                                                            <i class="fas fa-user-slash me-1"></i> Absent
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success rounded-pill shadow-sm">
                                                            {{ $note->note }}/{{ $note->evaluation->bareme }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                                    {{ $note->created_at->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="{{ route('esbtp.notes.show', $note->id) }}" class="btn btn-sm btn-info me-2 shadow-sm" data-bs-toggle="tooltip" title="Voir les détails">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire') || auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant') || auth()->user()->can('edit_grades'))
                                                        <a href="{{ route('esbtp.notes.edit', $note->id) }}" class="btn btn-sm btn-warning me-2 shadow-sm" data-bs-toggle="tooltip" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        @endif
                                                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->can('delete_grades'))
                                                        <button type="button" class="btn btn-sm btn-danger shadow-sm" onclick="confirmDelete('{{ $note->id }}')" data-bs-toggle="tooltip" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="my-4 text-muted">
                                                        <i class="fas fa-info-circle fs-1 mb-3 d-block"></i>
                                                        <p class="mb-0">Aucune note trouvée</p>
                                                        @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire') || auth()->user()->hasRole('teacher') || auth()->user()->hasRole('enseignant') || auth()->user()->can('create_grade'))
                                                        <p class="small">Utilisez le bouton "Ajouter une note" pour commencer</p>
                                                        @endif
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
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmation de suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.</p>
                <p class="mb-0 small text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    La suppression peut affecter les calculs de moyennes et de bulletins.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-1"></i>
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire de suppression caché -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
    /* Variables de couleurs ESBTP */
    :root {
        --esbtp-primary: #01632f;
        --esbtp-primary-dark: #014a23;
        --esbtp-primary-light: rgba(1, 99, 47, 0.1);
        --esbtp-secondary: #f29400;
    }

    /* Styles généraux */
    .text-primary {
        color: var(--esbtp-primary) !important;
    }

    .bg-primary {
        background-color: var(--esbtp-primary) !important;
    }

    .btn-primary {
        background-color: var(--esbtp-primary);
        border-color: var(--esbtp-primary);
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--esbtp-primary-dark);
        border-color: var(--esbtp-primary-dark);
    }

    .btn-outline-primary {
        color: var(--esbtp-primary);
        border-color: var(--esbtp-primary);
    }

    .btn-outline-primary:hover {
        background-color: var(--esbtp-primary);
        border-color: var(--esbtp-primary);
    }

    /* Styles spécifiques à la page */
    .table th {
        font-weight: 600;
        color: var(--esbtp-primary);
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }

    .form-select:focus, .form-control:focus {
        border-color: var(--esbtp-primary);
        box-shadow: 0 0 0 0.25rem rgba(1, 99, 47, 0.25);
    }

    .card {
        border-radius: 0.5rem;
    }

    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }

    .btn {
        border-radius: 0.375rem;
    }

    /* Animation pour les actions */
    .btn-sm {
        transition: transform 0.2s;
    }

    .btn-sm:hover {
        transform: translateY(-2px);
    }

    /* Style pour le tableau */
    .table-hover tbody tr:hover {
        background-color: var(--esbtp-primary-light);
    }

    /* Style pour les badges */
    .badge.bg-success {
        background-color: var(--esbtp-primary) !important;
    }

    /* Style pour les icônes dans le tableau */
    .fa-user-graduate {
        color: var(--esbtp-primary);
    }

    /* Style pour le modal */
    .modal-content {
        border: none;
        border-radius: 0.5rem;
    }

    .modal-header {
        border-radius: 0.5rem 0.5rem 0 0;
    }

    /* DataTables personnalisation */
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--esbtp-primary) !important;
        border-color: var(--esbtp-primary) !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--esbtp-primary-light) !important;
        border-color: var(--esbtp-primary) !important;
        color: var(--esbtp-primary) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialisation de Select2
        $('.form-select').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Initialisation de DataTables avec configuration en français
        $('#notesTable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            order: [[ 5, "desc" ]],
            responsive: true,
            pageLength: 25,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        });
    });

    // Gestion de la suppression
    let noteIdToDelete;

    function confirmDelete(noteId) {
        noteIdToDelete = noteId;
        $('#deleteModal').modal('show');
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        var form = document.getElementById('delete-form');
        form.action = "/esbtp/notes/" + noteIdToDelete;
        form.submit();
    });

    // Animation des badges au survol
    document.querySelectorAll('.badge').forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.transition = 'transform 0.2s';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
</script>
@endpush
