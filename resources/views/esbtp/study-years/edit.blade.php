@extends('layouts.app')

@section('title', 'Modifier une Année d\'études')
@section('page_title', 'Modification de l\'Année d\'études')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Formulaire de modification</h5>
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

        <form action="{{ route('esbtp.study-years.update', $studyYear) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- Nom de l'année d'études -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Intitulé de l'année d'études <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $studyYear->name) }}" required>
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
                                <option value="{{ $cycle->id }}" {{ old('cycle_id', $studyYear->cycle_id) == $cycle->id ? 'selected' : '' }}>
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
                                <option value="{{ $specialty->id }}" {{ old('specialty_id', $studyYear->specialty_id) == $specialty->id ? 'selected' : '' }}>
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
                        <label for="semesters_count" class="form-label">Nombre de semestres</label>
                        <input type="number" class="form-control @error('semesters_count') is-invalid @enderror" id="semesters_count" name="semesters_count" value="{{ old('semesters_count', $studyYear->semesters->count()) }}" min="1" max="4" readonly>
                        @error('semesters_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Le nombre de semestres ne peut pas être modifié après la création</small>
                    </div>
                    
                    <!-- Année académique -->
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Année académique</label>
                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror" id="academic_year" name="academic_year" value="{{ old('academic_year', $studyYear->academic_year) }}">
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Exemple: 2023-2024</small>
                    </div>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $studyYear->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Année d'études active</label>
                        </div>
                        <small class="form-text text-muted">Une année d'études inactive ne sera pas visible dans les listes de sélection</small>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $studyYear->description) }}</textarea>
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
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Liste des semestres associés -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title">Semestres associés</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studyYear->semesters as $semester)
                        <tr>
                            <td>{{ $semester->name }}</td>
                            <td>{{ $semester->order }}</td>
                            <td>
                                @if($semester->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-warning">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('esbtp.semesters.edit', $semester) }}" class="btn btn-warning btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('esbtp.semesters.show', $semester) }}" class="btn btn-info btn-sm" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun semestre associé à cette année d'études</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 