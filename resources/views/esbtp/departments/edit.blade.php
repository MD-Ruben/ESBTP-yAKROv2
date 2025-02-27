@extends('layouts.app')

@section('title', 'Modifier un Département')
@section('page_title', 'Modification du Département')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Formulaire de modification</h5>
        <a href="{{ route('esbtp.departments.index') }}" class="btn btn-secondary">
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

        <form action="{{ route('esbtp.departments.update', $department) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <!-- Nom du département -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du département <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Exemple: Département de Génie Civil</small>
                    </div>
                    
                    <!-- Code du département -->
                    <div class="mb-3">
                        <label for="code" class="form-label">Code du département <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $department->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Exemple: GC-DEP</small>
                    </div>
                    
                    <!-- Nom du responsable -->
                    <div class="mb-3">
                        <label for="head_name" class="form-label">Nom du responsable</label>
                        <input type="text" class="form-control @error('head_name') is-invalid @enderror" id="head_name" name="head_name" value="{{ old('head_name', $department->head_name) }}">
                        @error('head_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <!-- Logo du département -->
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo du département</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format recommandé: PNG ou JPG, max 2MB. Laissez vide pour conserver le logo actuel.</small>
                    </div>
                    
                    <!-- Statut -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Département actif</label>
                        </div>
                        <small class="form-text text-muted">Un département inactif ne sera pas visible dans les listes de sélection</small>
                    </div>
                    
                    <!-- Aperçu du logo -->
                    <div class="mb-3">
                        <label class="form-label">Logo actuel</label>
                        <div class="border rounded p-2 text-center bg-light" id="logo-preview">
                            @if($department->logo)
                                <img src="{{ asset('storage/' . $department->logo) }}" class="img-fluid" style="max-height: 150px;" alt="Logo du département">
                            @else
                                <i class="fas fa-building fa-3x text-secondary"></i>
                                <p class="small text-muted mt-2">Aucun logo défini</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Une brève description du département et de ses activités</small>
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
@endsection

@section('scripts')
<script>
    // Aperçu du logo lors du téléchargement
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const previewDiv = document.getElementById('logo-preview');
                previewDiv.innerHTML = `<img src="${event.target.result}" class="img-fluid" style="max-height: 150px;" alt="Aperçu du logo">`;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection 