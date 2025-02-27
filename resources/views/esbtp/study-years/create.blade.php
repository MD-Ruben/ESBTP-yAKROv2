@extends('layouts.app')

@section('title', 'Créer une Année d\'études')
@section('page_title', 'Nouvelle Année d\'études')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Formulaire de création</h5>
        <a href="{{ route('esbtp.study-years.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
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

        <form action="{{ route('esbtp.study-years.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- Nom de l'année d'études -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Intitulé de l'année d'études <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Exemple: Première Année, Deuxième Année, etc.</small>
                    </div>
                    
                    <!-- Cycle -->
                    <div class="mb-3">
                        <label for="cycle_id" class="form-label">Cycle <span class="text-danger">*</span></label>
                        <select class="form-select @error('cycle_id') is-invalid @enderror" id="cycle_id" name="cycle_id" required>
                            <option value="">Sélectionnez un cycle</option>
                            @foreach($cycles as $cycle)
                                <option value="{{ $cycle->id }}" {{ old('cycle_id') == $cycle->id ? 'selected' : '' }}>
                                    {{ $cycle->name }} ({{ $cycle->department->name ?? 'Sans département' }})
                                </option>
                            @endforeach
                        </select>
                        @error('cycle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Spécialité -->
                    <div class="mb-3">
                        <label for="specialty_id" class="form-label">Spécialité <span class="text-danger">*</span></label>
                        <select class="form-select @error('specialty_id') is-invalid @enderror" id="specialty_id" name="specialty_id" required>
                            <option value="">Sélectionnez une spécialité</option>
                            @foreach($specialties as $specialty)
                                <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                    {{ $specialty->name }} ({{ $specialty->department->name ?? 'Sans département' }})
                                </option>
                            @endforeach
                        </select>
                        @error('specialty_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- Nombre de semestres -->
                    <div class="mb-3">
                        <label for="semesters_count" class="form-label">Nombre de semestres <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('semesters_count') is-invalid @enderror" id="semesters_count" name="semesters_count" value="{{ old('semesters_count', 2) }}" min="1" max="4" required>
                        @error('semesters_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Généralement 2 semestres par année d'études</small>
                    </div>
                    
                    <!-- Année académique -->
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Année académique</label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year') }}">
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Exemple: 2023-2024</small>
                    </div>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Année d'études active</label>
                        </div>
                        <small class="form-text text-muted">Une année d'études inactive ne sera pas visible dans les listes de sélection</small>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Une brève description de cette année d'études (optionnel)</small>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-secondary me-2">
                    <i class="fas fa-undo"></i> Réinitialiser
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script pour filtrer les spécialités en fonction du cycle sélectionné
    document.getElementById('cycle_id').addEventListener('change', function() {
        const cycleId = this.value;
        const specialtySelect = document.getElementById('specialty_id');
        
        // Désactiver toutes les options de spécialité
        Array.from(specialtySelect.options).forEach(option => {
            if (option.value !== '') {
                option.disabled = true;
                option.style.display = 'none';
            }
        });
        
        // Activer uniquement les spécialités correspondant au cycle sélectionné
        if (cycleId) {
            Array.from(specialtySelect.options).forEach(option => {
                if (option.dataset.cycleId === cycleId) {
                    option.disabled = false;
                    option.style.display = '';
                }
            });
        }
        
        // Réinitialiser la sélection
        specialtySelect.value = '';
    });
</script>
@endsection 