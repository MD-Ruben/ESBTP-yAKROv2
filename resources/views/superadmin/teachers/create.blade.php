@extends('layouts.app')

@section('title', 'Ajouter un Enseignant')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Ajouter un Enseignant</h1>
    <p class="mb-4">Remplissez le formulaire ci-dessous pour ajouter un nouvel enseignant.</p>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.teachers.index') }}">Enseignants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ajouter</li>
        </ol>
    </nav>

    <!-- Affichage des messages d'erreur -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de l'enseignant</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.teachers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Informations personnelles -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="font-weight-bold text-primary mb-0">Informations personnelles</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Nom complet <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Téléphone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="address">Adresse</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="date_of_birth">Date de naissance</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="gender">Genre</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Sélectionner</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculin</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Féminin</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="profile_picture">Photo de profil</label>
                                    <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
                                    <small class="form-text text-muted">Format recommandé: JPG, PNG. Max: 2MB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations professionnelles -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="font-weight-bold text-primary mb-0">Informations professionnelles</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="employee_id">ID Employé <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="department_id">Département <span class="text-danger">*</span></label>
                                    <select class="form-control" id="department_id" name="department_id" required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="designation_id">Fonction <span class="text-danger">*</span></label>
                                    <select class="form-control" id="designation_id" name="designation_id" required>
                                        <option value="">Sélectionner une fonction</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ old('designation_id') == $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="join_date">Date d'entrée en fonction</label>
                                    <input type="date" class="form-control" id="join_date" name="join_date" value="{{ old('join_date') ?? date('Y-m-d') }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="qualification">Qualification</label>
                                    <input type="text" class="form-control" id="qualification" name="qualification" value="{{ old('qualification') }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="experience">Expérience (années)</label>
                                    <input type="number" class="form-control" id="experience" name="experience" value="{{ old('experience') }}" min="0">
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Statut</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Informations du compte -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="font-weight-bold text-primary mb-0">Informations du compte</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="password">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <small class="form-text text-muted">Minimum 8 caractères</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password_confirmation">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Validation côté client (facultatif)
        $('form').submit(function(e) {
            var isValid = true;
            
            // Vérifier que les champs obligatoires sont remplis
            $('input[required], select[required]').each(function() {
                if ($(this).val() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Vérifier que les mots de passe correspondent
            if ($('#password').val() !== $('#password_confirmation').val()) {
                isValid = false;
                $('#password, #password_confirmation').addClass('is-invalid');
                alert('Les mots de passe ne correspondent pas.');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection 