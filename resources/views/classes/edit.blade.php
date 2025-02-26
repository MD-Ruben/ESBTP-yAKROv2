@extends('layouts.app')

@section('title', 'Modifier une Classe')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier la Classe : {{ $class->name }}</h1>
        <a href="{{ route('classes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Messages d'erreur -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulaire de modification -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de la Classe</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.update', $class->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Informations de base -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nom de la Classe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $class->name) }}" required
                               placeholder="Ex: Première Année Génie Civil">
                        <small class="text-muted">Le nom qui identifie cette classe</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">Niveau <span class="text-danger">*</span></label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">Sélectionner un niveau</option>
                            <option value="1" {{ old('level', $class->level) == '1' ? 'selected' : '' }}>1ère Année</option>
                            <option value="2" {{ old('level', $class->level) == '2' ? 'selected' : '' }}>2ème Année</option>
                            <option value="3" {{ old('level', $class->level) == '3' ? 'selected' : '' }}>3ème Année</option>
                            <option value="4" {{ old('level', $class->level) == '4' ? 'selected' : '' }}>4ème Année</option>
                            <option value="5" {{ old('level', $class->level) == '5' ? 'selected' : '' }}>5ème Année</option>
                        </select>
                    </div>
                </div>

                <!-- Sections existantes -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="text-gray-800">Sections Existantes</h5>
                        <hr>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nom de la Section</th>
                                        <th>Capacité</th>
                                        <th>Nombre d'Étudiants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($class->sections as $section)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" 
                                                       name="existing_sections[{{ $section->id }}][name]" 
                                                       value="{{ $section->name }}" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" 
                                                       name="existing_sections[{{ $section->id }}][capacity]" 
                                                       value="{{ $section->capacity }}" min="1" required>
                                            </td>
                                            <td class="text-center">
                                                {{ $section->students_count ?? 0 }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-section" 
                                                        data-section-id="{{ $section->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Aucune section existante</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Nouvelles sections -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="text-gray-800">Ajouter de Nouvelles Sections</h5>
                        <hr>
                    </div>
                    <div class="col-12">
                        <div id="new_sections">
                            <!-- Les nouvelles sections seront ajoutées ici -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="add_section">
                            <i class="fas fa-plus"></i> Ajouter une Section
                        </button>
                    </div>
                </div>

                <!-- Informations supplémentaires -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="text-gray-800">Informations Supplémentaires</h5>
                        <hr>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Description de la classe">{{ old('description', $class->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="academic_year" class="form-label">Année Académique</label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" 
                               value="{{ old('academic_year', $class->academic_year) }}"
                               placeholder="Ex: 2023-2024">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" {{ old('status', $class->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $class->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les Modifications
                        </button>
                        <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour la gestion des sections -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const newSectionsContainer = document.getElementById('new_sections');
    const addSectionBtn = document.getElementById('add_section');

    // Fonction pour ajouter une nouvelle section
    function addNewSection() {
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'row mb-2 align-items-center';
        sectionDiv.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="new_sections[][name]" 
                       placeholder="Nom de la section" required>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="new_sections[][capacity]" 
                       placeholder="Capacité" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-section">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        newSectionsContainer.appendChild(sectionDiv);

        // Ajouter l'événement de suppression
        sectionDiv.querySelector('.remove-section').addEventListener('click', function() {
            sectionDiv.remove();
        });
    }

    // Événement pour ajouter une nouvelle section
    addSectionBtn.addEventListener('click', addNewSection);

    // Gestion de la suppression des sections existantes
    document.querySelectorAll('.delete-section').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette section ?')) {
                const sectionId = this.dataset.sectionId;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'sections_to_delete[]';
                input.value = sectionId;
                this.closest('form').appendChild(input);
                this.closest('tr').remove();
            }
        });
    });
});
</script>
@endsection 