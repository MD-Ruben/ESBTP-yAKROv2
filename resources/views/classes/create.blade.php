@extends('layouts.app')

@section('title', 'Ajouter une Classe')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ajouter une Classe</h1>
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

    <!-- Formulaire d'ajout -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de la Classe</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                
                <!-- Informations de base -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nom de la Classe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name') }}" required
                               placeholder="Ex: Première Année Génie Civil">
                        <small class="text-muted">Le nom qui identifiera cette classe</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="level" class="form-label">Niveau <span class="text-danger">*</span></label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">Sélectionner un niveau</option>
                            <option value="1" {{ old('level') == '1' ? 'selected' : '' }}>1ère Année</option>
                            <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>2ème Année</option>
                            <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>3ème Année</option>
                            <option value="4" {{ old('level') == '4' ? 'selected' : '' }}>4ème Année</option>
                            <option value="5" {{ old('level') == '5' ? 'selected' : '' }}>5ème Année</option>
                        </select>
                    </div>
                </div>

                <!-- Sections -->
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h5 class="text-gray-800">Sections</h5>
                        <hr>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="create_default_sections" 
                                   name="create_default_sections" value="1" checked>
                            <label class="form-check-label" for="create_default_sections">
                                Créer des sections par défaut (A, B, C)
                            </label>
                        </div>
                        <div id="custom_sections" class="mt-3" style="display: none;">
                            <label class="form-label">Sections Personnalisées</label>
                            <div class="section-list">
                                <div class="row mb-2 section-item">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="sections[]" 
                                               placeholder="Nom de la section">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" name="capacities[]" 
                                               placeholder="Capacité" min="1">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-remove-section">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-success mt-2" id="add_section">
                                <i class="fas fa-plus"></i> Ajouter une section
                            </button>
                        </div>
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
                                  rows="3" placeholder="Description de la classe">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="academic_year" class="form-label">Année Académique</label>
                        <input type="text" class="form-control" id="academic_year" name="academic_year" 
                               value="{{ old('academic_year', date('Y').'-'.(date('Y')+1)) }}"
                               placeholder="Ex: 2023-2024">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
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
    const createDefaultSections = document.getElementById('create_default_sections');
    const customSections = document.getElementById('custom_sections');
    const addSectionBtn = document.getElementById('add_section');
    const sectionList = document.querySelector('.section-list');

    // Gestion de l'affichage des sections personnalisées
    createDefaultSections.addEventListener('change', function() {
        customSections.style.display = this.checked ? 'none' : 'block';
    });

    // Ajout d'une nouvelle section
    addSectionBtn.addEventListener('click', function() {
        const sectionItem = document.createElement('div');
        sectionItem.className = 'row mb-2 section-item';
        sectionItem.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control" name="sections[]" placeholder="Nom de la section">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="capacities[]" placeholder="Capacité" min="1">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-remove-section">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        sectionList.appendChild(sectionItem);

        // Gestionnaire pour le bouton de suppression
        sectionItem.querySelector('.btn-remove-section').addEventListener('click', function() {
            sectionItem.remove();
        });
    });

    // Gestionnaire pour les boutons de suppression existants
    document.querySelectorAll('.btn-remove-section').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.section-item').remove();
        });
    });
});
</script>
@endsection 