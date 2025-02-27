@extends('layouts.app')

@section('title', 'Modifier un Semestre')
@section('page_title', 'Modification du Semestre')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Formulaire de modification</h5>
        <a href="{{ route('esbtp.semesters.index') }}" class="btn btn-secondary">
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

        <form action="{{ route('esbtp.semesters.update', $semester) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- Nom du semestre -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du semestre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $semester->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Exemple: Semestre 1, Premier Semestre, etc.</small>
                    </div>
                    
                    <!-- Année d'études -->
                    <div class="mb-3">
                        <label for="study_year_id" class="form-label">Année d'études <span class="text-danger">*</span></label>
                        <select class="form-select @error('study_year_id') is-invalid @enderror" id="study_year_id" name="study_year_id" required>
                            <option value="">Sélectionnez une année d'études</option>
                            @foreach($studyYears as $studyYear)
                                <option value="{{ $studyYear->id }}" {{ old('study_year_id', $semester->study_year_id) == $studyYear->id ? 'selected' : '' }}>
                                    {{ $studyYear->name }} - {{ $studyYear->specialty->name ?? 'Sans spécialité' }} ({{ $studyYear->cycle->name ?? 'Sans cycle' }})
                                </option>
                            @endforeach
                        </select>
                        @error('study_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- Ordre du semestre -->
                    <div class="mb-3">
                        <label for="order" class="form-label">Ordre du semestre <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $semester->order) }}" min="1" required>
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">L'ordre d'apparition du semestre dans l'année d'études</small>
                    </div>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $semester->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Semestre actif</label>
                        </div>
                        <small class="form-text text-muted">Un semestre inactif ne sera pas visible dans les listes de sélection</small>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $semester->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Une brève description de ce semestre (optionnel)</small>
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

<!-- Liste des cours associés -->
@if($semester->courses && $semester->courses->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title">Cours associés</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Intitulé</th>
                        <th>Crédits</th>
                        <th>Heures</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semester->courses as $course)
                        <tr>
                            <td>{{ $course->code }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->credits }}</td>
                            <td>{{ $course->hours }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="#" class="btn btn-info btn-sm" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-warning btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection 