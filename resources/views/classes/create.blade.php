@extends('layouts.app')

@section('title', 'Créer une Classe')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Créer une Nouvelle Classe
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

                    <form action="{{ route('classes.store') }}" method="POST">
                        @csrf
                        
                        <!-- Nom de la classe -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la classe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
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
                                <option value="1" {{ old('level') == '1' ? 'selected' : '' }}>Première année</option>
                                <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>Deuxième année</option>
                                <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>Troisième année</option>
                                <option value="4" {{ old('level') == '4' ? 'selected' : '' }}>Quatrième année</option>
                                <option value="5" {{ old('level') == '5' ? 'selected' : '' }}>Cinquième année</option>
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
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
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
                            <input type="number" class="form-control @error('max_students') is-invalid @enderror" id="max_students" name="max_students" value="{{ old('max_students', 30) }}" min="1">
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
                                <option value="{{ $prevYear }}-{{ $currentYear }}" {{ old('academic_year') == "$prevYear-$currentYear" ? 'selected' : '' }}>{{ $prevYear }}-{{ $currentYear }}</option>
                                <option value="{{ $currentYear }}-{{ $nextYear }}" {{ old('academic_year', "$currentYear-$nextYear") == "$currentYear-$nextYear" ? 'selected' : '' }}>{{ $currentYear }}-{{ $nextYear }}</option>
                                <option value="{{ $nextYear }}-{{ $nextYear+1 }}" {{ old('academic_year') == "$nextYear-".($nextYear+1) ? 'selected' : '' }}>{{ $nextYear }}-{{ $nextYear+1 }}</option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>L'année académique pour cette classe
                            </small>
                            @error('academic_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Boutons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('classes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Enregistrer
                            </button>
                        </div>
                    </form>
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