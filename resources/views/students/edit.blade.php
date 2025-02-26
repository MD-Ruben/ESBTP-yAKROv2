@extends('layouts.app')

@section('title', 'Modifier un Étudiant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0">Modifier l'Étudiant: {{ $student->user->name ?? 'N/A' }}</h5>
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

                    <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Formulaire en onglets -->
                        <ul class="nav nav-tabs mb-4" id="studentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                    <i class="fas fa-user me-2"></i>Informations Personnelles
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab" aria-controls="academic" aria-selected="false">
                                    <i class="fas fa-graduation-cap me-2"></i>Informations Académiques
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="parent-tab" data-bs-toggle="tab" data-bs-target="#parent" type="button" role="tab" aria-controls="parent" aria-selected="false">
                                    <i class="fas fa-users me-2"></i>Informations du Parent
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="studentTabsContent">
                            <!-- Onglet Informations Personnelles -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nom Complet <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $student->user->name ?? '') }}" required>
                                        <small class="form-text text-muted">Entrez le nom complet de l'étudiant</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->user->email ?? '') }}" required>
                                        <small class="form-text text-muted">Cet email sera utilisé pour la connexion</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <small class="form-text text-muted">Laissez vide pour conserver le mot de passe actuel</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth ?? '') }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Genre</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="">Sélectionner</option>
                                            <option value="M" {{ (old('gender', $student->gender) == 'M') ? 'selected' : '' }}>Masculin</option>
                                            <option value="F" {{ (old('gender', $student->gender) == 'F') ? 'selected' : '' }}>Féminin</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $student->phone ?? '') }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $student->address ?? '') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="profile_image" class="form-label">Photo de profil</label>
                                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                                        <small class="form-text text-muted">Format JPG ou PNG, max 2MB</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ (old('is_active', $student->user->is_active ?? 1) == 1) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Compte actif</label>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($student->profile_image)
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <p class="mb-2">Photo actuelle:</p>
                                                <img src="{{ asset('storage/' . $student->profile_image) }}" alt="Photo de profil actuelle" class="img-thumbnail" style="max-height: 150px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Onglet Informations Académiques -->
                            <div class="tab-pane fade" id="academic" role="tabpanel" aria-labelledby="academic-tab">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="registration_number" class="form-label">Numéro de matricule <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ old('registration_number', $student->registration_number ?? '') }}" required>
                                        <small class="form-text text-muted">Format: ESBTP/ANNÉE/NUMÉRO</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="admission_date" class="form-label">Date d'admission</label>
                                        <input type="date" class="form-control" id="admission_date" name="admission_date" value="{{ old('admission_date', $student->admission_date ?? '') }}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="class_id" class="form-label">Classe <span class="text-danger">*</span></label>
                                        <select class="form-select" id="class_id" name="class_id" required>
                                            <option value="">Sélectionner une classe</option>
                                            @foreach($classes ?? [] as $class)
                                                <option value="{{ $class->id }}" {{ (old('class_id', $student->class_id) == $class->id) ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="section_id" class="form-label">Section</label>
                                        <select class="form-select" id="section_id" name="section_id">
                                            <option value="">Sélectionner une section</option>
                                            @foreach($sections ?? [] as $section)
                                                <option value="{{ $section->id }}" {{ (old('section_id', $student->section_id) == $section->id) ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="previous_school" class="form-label">École précédente</label>
                                        <input type="text" class="form-control" id="previous_school" name="previous_school" value="{{ old('previous_school', $student->previous_school ?? '') }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="blood_group" class="form-label">Groupe sanguin</label>
                                        <select class="form-select" id="blood_group" name="blood_group">
                                            <option value="">Sélectionner</option>
                                            <option value="A+" {{ (old('blood_group', $student->blood_group) == 'A+') ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ (old('blood_group', $student->blood_group) == 'A-') ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ (old('blood_group', $student->blood_group) == 'B+') ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ (old('blood_group', $student->blood_group) == 'B-') ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ (old('blood_group', $student->blood_group) == 'AB+') ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ (old('blood_group', $student->blood_group) == 'AB-') ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ (old('blood_group', $student->blood_group) == 'O+') ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ (old('blood_group', $student->blood_group) == 'O-') ? 'selected' : '' }}>O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Onglet Informations du Parent -->
                            <div class="tab-pane fade" id="parent" role="tabpanel" aria-labelledby="parent-tab">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Vous pouvez associer un parent existant ou créer un nouveau compte parent.
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="parent_option" id="existing_parent" value="existing" {{ !isset($new_parent) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="existing_parent">Parent existant</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="parent_option" id="new_parent" value="new" {{ isset($new_parent) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="new_parent">Nouveau parent</label>
                                    </div>
                                </div>
                                
                                <!-- Sélection d'un parent existant -->
                                <div id="existing_parent_section" style="{{ isset($new_parent) ? 'display: none;' : '' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_id" class="form-label">Sélectionner un parent</label>
                                            <select class="form-select" id="parent_id" name="parent_id">
                                                <option value="">Sélectionner un parent</option>
                                                @foreach($parents ?? [] as $parent)
                                                    <option value="{{ $parent->id }}" {{ (old('parent_id', $student->parent_id) == $parent->id) ? 'selected' : '' }}>
                                                        {{ $parent->user->name ?? 'N/A' }} ({{ $parent->user->email ?? 'N/A' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Création d'un nouveau parent -->
                                <div id="new_parent_section" style="{{ isset($new_parent) ? '' : 'display: none;' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_name" class="form-label">Nom du parent</label>
                                            <input type="text" class="form-control" id="parent_name" name="parent_name" value="{{ old('parent_name') }}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_email" class="form-label">Email du parent</label>
                                            <input type="email" class="form-control" id="parent_email" name="parent_email" value="{{ old('parent_email') }}">
                                            <small class="form-text text-muted">Cet email sera utilisé pour la connexion du parent</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_password" class="form-label">Mot de passe du parent</label>
                                            <input type="password" class="form-control" id="parent_password" name="parent_password">
                                            <small class="form-text text-muted">Minimum 8 caractères</small>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                            <input type="password" class="form-control" id="parent_password_confirmation" name="parent_password_confirmation">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_phone" class="form-label">Téléphone du parent</label>
                                            <input type="text" class="form-control" id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="parent_occupation" class="form-label">Profession du parent</label>
                                            <input type="text" class="form-control" id="parent_occupation" name="parent_occupation" value="{{ old('parent_occupation') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="parent_address" class="form-label">Adresse du parent</label>
                                            <textarea class="form-control" id="parent_address" name="parent_address" rows="3">{{ old('parent_address') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
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
    // Script pour basculer entre parent existant et nouveau parent
    document.addEventListener('DOMContentLoaded', function() {
        const existingParentRadio = document.getElementById('existing_parent');
        const newParentRadio = document.getElementById('new_parent');
        const existingParentSection = document.getElementById('existing_parent_section');
        const newParentSection = document.getElementById('new_parent_section');
        
        existingParentRadio.addEventListener('change', function() {
            if (this.checked) {
                existingParentSection.style.display = 'block';
                newParentSection.style.display = 'none';
            }
        });
        
        newParentRadio.addEventListener('change', function() {
            if (this.checked) {
                existingParentSection.style.display = 'none';
                newParentSection.style.display = 'block';
            }
        });
    });
</script>
@endpush