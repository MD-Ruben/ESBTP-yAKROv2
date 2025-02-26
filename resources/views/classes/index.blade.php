@extends('layouts.app')

@section('title', 'Gestion des Classes')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Classes</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">
            <i class="fas fa-plus-circle"></i> Ajouter une Classe
        </button>
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistiques des classes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalClasses ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
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
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Moyenne d'Étudiants par Classe</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ isset($totalStudents, $totalClasses) && $totalClasses > 0 
                                   ? number_format($totalStudents / $totalClasses, 1) 
                                   : 'N/A' }}
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total des Sections</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSections ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des classes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Classes</h6>
            <button class="btn btn-sm btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Niveau</th>
                            <th>Sections</th>
                            <th>Étudiants</th>
                            <th>Enseignants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes ?? [] as $class)
                            <tr>
                                <td>{{ $class->id }}</td>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->level }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $class->sections_count ?? 0 }} sections</span>
                                    <button type="button" class="btn btn-sm btn-link" data-bs-toggle="modal" data-bs-target="#sectionsModal{{ $class->id }}">
                                        Voir
                                    </button>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $class->students_count ?? 0 }} étudiants</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $class->teachers_count ?? 0 }} enseignants</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('timetable.class', $class->id) }}" class="btn btn-sm btn-info" title="Emploi du temps">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editClassModal{{ $class->id }}" 
                                                title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addSectionModal{{ $class->id }}" 
                                                title="Ajouter une section">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteClassModal{{ $class->id }}" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal des sections -->
                                    <div class="modal fade" id="sectionsModal{{ $class->id }}" tabindex="-1" aria-labelledby="sectionsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="sectionsModalLabel">Sections de {{ $class->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if($class->sections && $class->sections->count() > 0)
                                                        <div class="list-group">
                                                            @foreach($class->sections as $section)
                                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <h6 class="mb-1">{{ $section->name }}</h6>
                                                                        <small>{{ $section->students_count ?? 0 }} étudiants</small>
                                                                    </div>
                                                                    <div>
                                                                        <button type="button" class="btn btn-sm btn-primary" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#editSectionModal{{ $section->id }}" 
                                                                                title="Modifier">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                                data-bs-toggle="modal" 
                                                                                data-bs-target="#deleteSectionModal{{ $section->id }}" 
                                                                                title="Supprimer">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="text-center">Aucune section trouvée pour cette classe.</p>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <button type="button" class="btn btn-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#addSectionModal{{ $class->id }}">
                                                        Ajouter une Section
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de modification de classe -->
                                    <div class="modal fade" id="editClassModal{{ $class->id }}" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editClassModalLabel">Modifier la Classe</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('classes.update', $class->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="name{{ $class->id }}" class="form-label">Nom de la Classe</label>
                                                            <input type="text" class="form-control" id="name{{ $class->id }}" name="name" value="{{ $class->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="level{{ $class->id }}" class="form-label">Niveau</label>
                                                            <select class="form-select" id="level{{ $class->id }}" name="level" required>
                                                                <option value="1" {{ $class->level == 1 ? 'selected' : '' }}>1ère Année</option>
                                                                <option value="2" {{ $class->level == 2 ? 'selected' : '' }}>2ème Année</option>
                                                                <option value="3" {{ $class->level == 3 ? 'selected' : '' }}>3ème Année</option>
                                                                <option value="4" {{ $class->level == 4 ? 'selected' : '' }}>4ème Année</option>
                                                                <option value="5" {{ $class->level == 5 ? 'selected' : '' }}>5ème Année</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description{{ $class->id }}" class="form-label">Description</label>
                                                            <textarea class="form-control" id="description{{ $class->id }}" name="description" rows="3">{{ $class->description }}</textarea>
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

                                    <!-- Modal d'ajout de section -->
                                    <div class="modal fade" id="addSectionModal{{ $class->id }}" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addSectionModalLabel">Ajouter une Section à {{ $class->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                                            <input type="number" class="form-control" id="capacity" name="capacity" min="1" value="30">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="section_description" class="form-label">Description</label>
                                                            <textarea class="form-control" id="section_description" name="description" rows="3"></textarea>
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

                                    <!-- Modal de suppression de classe -->
                                    <div class="modal fade" id="deleteClassModal{{ $class->id }}" tabindex="-1" aria-labelledby="deleteClassModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteClassModalLabel">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer la classe <strong>{{ $class->name }}</strong> ?</p>
                                                    <p class="text-danger">Cette action supprimera également toutes les sections associées et les données liées à cette classe.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('classes.destroy', $class->id) }}" method="POST">
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
                                <td colspan="7" class="text-center">Aucune classe trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($classes) && $classes->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'ajout de classe -->
<div class="modal fade" id="createClassModal" tabindex="-1" aria-labelledby="createClassModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClassModalLabel">Ajouter une Nouvelle Classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom de la Classe</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="level" class="form-label">Niveau</label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="1">1ère Année</option>
                            <option value="2">2ème Année</option>
                            <option value="3">3ème Année</option>
                            <option value="4">4ème Année</option>
                            <option value="5">5ème Année</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="create_default_sections" name="create_default_sections" value="1" checked>
                        <label class="form-check-label" for="create_default_sections">Créer des sections par défaut (A, B, C)</label>
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

<!-- Modals pour les sections (édition et suppression) -->
@if(isset($classes))
    @foreach($classes as $class)
        @if($class->sections)
            @foreach($class->sections as $section)
                <!-- Modal de modification de section -->
                <div class="modal fade" id="editSectionModal{{ $section->id }}" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSectionModalLabel">Modifier la Section</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('sections.update', $section->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="section_name{{ $section->id }}" class="form-label">Nom de la Section</label>
                                        <input type="text" class="form-control" id="section_name{{ $section->id }}" name="name" value="{{ $section->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="capacity{{ $section->id }}" class="form-label">Capacité</label>
                                        <input type="number" class="form-control" id="capacity{{ $section->id }}" name="capacity" min="1" value="{{ $section->capacity }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="section_description{{ $section->id }}" class="form-label">Description</label>
                                        <textarea class="form-control" id="section_description{{ $section->id }}" name="description" rows="3">{{ $section->description }}</textarea>
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

                <!-- Modal de suppression de section -->
                <div class="modal fade" id="deleteSectionModal{{ $section->id }}" tabindex="-1" aria-labelledby="deleteSectionModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteSectionModalLabel">Confirmer la suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Êtes-vous sûr de vouloir supprimer la section <strong>{{ $section->name }}</strong> de la classe <strong>{{ $class->name }}</strong> ?</p>
                                <p class="text-danger">Cette action supprimera également toutes les données liées à cette section.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <form action="{{ route('sections.destroy', $section->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach
@endif
@endsection 