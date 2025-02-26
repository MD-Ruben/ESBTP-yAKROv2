@extends('layouts.app')

@section('title', 'Modifier une Classe')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier la Classe: {{ $class->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('classes.update', $class->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nom de la classe -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $class->name) }}" required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Exemple: Première Année BTP, Licence 3 Architecture, etc.
                            </small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Niveau -->
                        <div class="mb-3">
                            <label for="level" class="form-label">Niveau <span class="text-danger">*</span></label>
                            <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                <option value="">Sélectionner un niveau</option>
                                <option value="1" {{ old('level', $class->level) == '1' ? 'selected' : '' }}>Première année</option>
                                <option value="2" {{ old('level', $class->level) == '2' ? 'selected' : '' }}>Deuxième année</option>
                                <option value="3" {{ old('level', $class->level) == '3' ? 'selected' : '' }}>Troisième année</option>
                                <option value="4" {{ old('level', $class->level) == '4' ? 'selected' : '' }}>Quatrième année</option>
                                <option value="5" {{ old('level', $class->level) == '5' ? 'selected' : '' }}>Cinquième année</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Le niveau académique de cette classe
                            </small>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $class->description) }}</textarea>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Une brève description de cette classe (optionnel)
                            </small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Capacité maximale -->
                        <div class="mb-3">
                            <label for="max_students" class="form-label">Capacité maximale</label>
                            <input type="number" class="form-control @error('max_students') is-invalid @enderror" id="max_students" name="max_students" value="{{ old('max_students', $class->max_students) }}" min="1">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Nombre maximum d'étudiants dans cette classe
                            </small>
                            @error('max_students')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Année académique -->
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Année académique <span class="text-danger">*</span></label>
                            <select class="form-select @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" required>
                                <option value="">Sélectionner une année académique</option>
                                @php
                                    $currentYear = date('Y');
                                    $nextYear = $currentYear + 1;
                                    $prevYear = $currentYear - 1;
                                @endphp
                                <option value="{{ $prevYear }}-{{ $currentYear }}" {{ old('academic_year', $class->academic_year) == "$prevYear-$currentYear" ? 'selected' : '' }}>{{ $prevYear }}-{{ $currentYear }}</option>
                                <option value="{{ $currentYear }}-{{ $nextYear }}" {{ old('academic_year', $class->academic_year) == "$currentYear-$nextYear" ? 'selected' : '' }}>{{ $currentYear }}-{{ $nextYear }}</option>
                                <option value="{{ $nextYear }}-{{ $nextYear+1 }}" {{ old('academic_year', $class->academic_year) == "$nextYear-".($nextYear+1) ? 'selected' : '' }}>{{ $nextYear }}-{{ $nextYear+1 }}</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>L'année académique pour cette classe
                            </small>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Statut de la classe -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="active" {{ old('status', $class->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $class->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="completed" {{ old('status', $class->status) == 'completed' ? 'selected' : '' }}>Terminée</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Le statut actuel de cette classe
                            </small>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('classes.show', $class->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Sections de la classe -->
            <div class="card mt-4">
                <div class="card-header bg-esbtp-orange text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-layer-group me-2"></i>Sections de la classe
                    </h5>
                    <a href="{{ route('sections.create', ['class_id' => $class->id]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>Ajouter une section
                    </a>
                </div>
                <div class="card-body">
                    @if($class->sections && $class->sections->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Étudiants</th>
                                        <th>Capacité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($class->sections as $section)
                                        <tr>
                                            <td>{{ $section->name }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $section->students_count ?? $section->students->count() }}</span>
                                            </td>
                                            <td>{{ $section->capacity ?? 'Non définie' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('sections.edit', $section->id) }}" class="btn btn-sm btn-warning text-white" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteSectionModal{{ $section->id }}" 
                                                        title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modal de confirmation de suppression -->
                                                <div class="modal fade" id="deleteSectionModal{{ $section->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Êtes-vous sûr de vouloir supprimer la section <strong>{{ $section->name }}</strong> ?</p>
                                                                @if($section->students_count > 0 || ($section->students && $section->students->count() > 0))
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        Cette section contient des étudiants. La suppression déplacera ces étudiants vers la section "Non assignée".
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-1"></i>Annuler
                                                                </button>
                                                                <form action="{{ route('sections.destroy', $section->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-trash me-1"></i>Supprimer
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune section n'a été créée pour cette classe.
                            <div class="mt-2">
                                <a href="{{ route('sections.create', ['class_id' => $class->id]) }}" class="btn btn-sm btn-esbtp-orange">
                                    <i class="fas fa-plus-circle me-1"></i>Créer une section
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script pour valider le formulaire côté client
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validation du nom
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                isValid = false;
                nameInput.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = 'Le nom de la classe est requis';
                nameInput.parentNode.appendChild(feedback);
            }
            
            // Validation du niveau
            const levelSelect = document.getElementById('level');
            if (!levelSelect.value) {
                isValid = false;
                levelSelect.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = 'Le niveau est requis';
                levelSelect.parentNode.appendChild(feedback);
            }
            
            // Validation de l'année académique
            const yearSelect = document.getElementById('academic_year');
            if (!yearSelect.value) {
                isValid = false;
                yearSelect.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                feedback.textContent = "L'année académique est requise";
                yearSelect.parentNode.appendChild(feedback);
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>
@endpush 