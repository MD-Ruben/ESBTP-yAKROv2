@extends('layouts.app')

@section('title', 'Ajouter un Enseignant')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ajouter un Enseignant</h1>
        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
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
            <h6 class="m-0 font-weight-bold text-primary">Informations de l'Enseignant</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('teachers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations personnelles -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="text-gray-800">Informations Personnelles</h5>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                               value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                               value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_of_birth" class="form-label">Date de Naissance</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                               value="{{ old('date_of_birth') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Genre</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">Sélectionner</option>
                            <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculin</option>
                            <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Féminin</option>
                        </select>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="text-gray-800">Informations de Contact</h5>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <textarea class="form-control" id="address" name="address" 
                                  rows="3">{{ old('address') }}</textarea>
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="text-gray-800">Informations Professionnelles</h5>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label">Département <span class="text-danger">*</span></label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Sélectionner un département</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}" 
                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="joining_date" class="form-label">Date d'entrée en fonction</label>
                        <input type="date" class="form-control" id="joining_date" name="joining_date" 
                               value="{{ old('joining_date') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="qualification" class="form-label">Qualification</label>
                        <input type="text" class="form-control" id="qualification" name="qualification" 
                               value="{{ old('qualification') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="experience" class="form-label">Expérience (années)</label>
                        <input type="number" class="form-control" id="experience" name="experience" 
                               value="{{ old('experience') }}" min="0">
                    </div>
                </div>

                <!-- Photo et statut -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="text-gray-800">Photo et Statut</h5>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Format accepté : JPG, PNG. Taille max : 2MB</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="is_active" class="form-label">Statut</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                </div>

                <!-- Informations de connexion -->
                <div class="row mb-4">
                    <div class="col-md-12 mb-4">
                        <h5 class="text-gray-800">Informations de Connexion</h5>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour la prévisualisation de l'image -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert('La taille de l\'image ne doit pas dépasser 2MB');
                this.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Seuls les formats JPG et PNG sont acceptés');
                this.value = '';
                return;
            }
        }
    });
});
</script>
@endsection 