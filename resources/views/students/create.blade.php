@extends('layouts.app')

@section('title', 'Ajouter un Étudiant')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-8 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="fw-bold mb-0">Ajouter un nouvel étudiant</h2>
                                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                                </a>
                            </div>
                            <p class="text-muted mb-4">Remplissez le formulaire ci-dessous pour ajouter un nouvel étudiant à l'établissement. Tous les champs marqués d'un astérisque (*) sont obligatoires.</p>
                            
                            <div class="d-flex gap-3 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary-light rounded-circle p-2 me-2">
                                        <i class="fas fa-user-graduate text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Informations personnelles</h6>
                                        <small class="text-muted">Identité et contact</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success-light rounded-circle p-2 me-2">
                                        <i class="fas fa-graduation-cap text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Informations académiques</h6>
                                        <small class="text-muted">Classe et parcours</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-none d-md-block" style="background: linear-gradient(135deg, var(--esbtp-green-light), var(--esbtp-green)); min-height: 220px;">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                <img src="https://img.freepik.com/free-vector/add-user-concept-illustration_114360-557.jpg" alt="Add Student" class="img-fluid" style="max-height: 200px; opacity: 0.9;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Veuillez corriger les erreurs suivantes :</strong>
            </div>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Formulaire en onglets -->
                        <ul class="nav nav-tabs nav-fill mb-4" id="studentTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-3" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                    <i class="fas fa-user me-2"></i>Informations Personnelles
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab" aria-controls="academic" aria-selected="false">
                                    <i class="fas fa-graduation-cap me-2"></i>Informations Académiques
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-3" id="parent-tab" data-bs-toggle="tab" data-bs-target="#parent" type="button" role="tab" aria-controls="parent" aria-selected="false">
                                    <i class="fas fa-users me-2"></i>Informations du Parent
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content p-3" id="studentTabsContent">
                            <!-- Onglet Informations Personnelles -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                        </div>
                                        @error('name')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" class="form-control border-0 bg-light @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                        </div>
                                        @error('email')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                            <input type="password" class="form-control border-0 bg-light @error('password') is-invalid @enderror" id="password" name="password" required>
                                        </div>
                                        @error('password')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                            <input type="password" class="form-control border-0 bg-light" id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                        </div>
                                        @error('phone')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date de naissance</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                            <input type="date" class="form-control border-0 bg-light @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                        </div>
                                        @error('date_of_birth')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Genre</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-venus-mars text-muted"></i></span>
                                            <select class="form-select border-0 bg-light @error('gender') is-invalid @enderror" id="gender" name="gender">
                                                <option value="">Sélectionner</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculin</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Féminin</option>
                                            </select>
                                        </div>
                                        @error('gender')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}">
                                        </div>
                                        @error('address')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="profile_image" class="form-label">Photo de profil</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-image text-muted"></i></span>
                                            <input type="file" class="form-control border-0 bg-light @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image">
                                        </div>
                                        <div class="form-text">Formats acceptés : JPG, PNG. Taille max : 2MB</div>
                                        @error('profile_image')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Onglet Informations Académiques -->
                            <div class="tab-pane fade" id="academic" role="tabpanel" aria-labelledby="academic-tab">
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="registration_number" class="form-label">Numéro d'inscription <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-id-card text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('registration_number') is-invalid @enderror" id="registration_number" name="registration_number" value="{{ old('registration_number') }}" required>
                                        </div>
                                        @error('registration_number')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="class_id" class="form-label">Classe <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-chalkboard text-muted"></i></span>
                                            <select class="form-select border-0 bg-light @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                                                <option value="">Sélectionner une classe</option>
                                                @foreach($classes ?? [] as $class)
                                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('class_id')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="section_id" class="form-label">Section</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-layer-group text-muted"></i></span>
                                            <select class="form-select border-0 bg-light @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
                                                <option value="">Sélectionner une section</option>
                                                @foreach($sections ?? [] as $section)
                                                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                                        {{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('section_id')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="admission_date" class="form-label">Date d'admission</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-check text-muted"></i></span>
                                            <input type="date" class="form-control border-0 bg-light @error('admission_date') is-invalid @enderror" id="admission_date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}">
                                        </div>
                                        @error('admission_date')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="previous_school" class="form-label">École précédente</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-school text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('previous_school') is-invalid @enderror" id="previous_school" name="previous_school" value="{{ old('previous_school') }}">
                                        </div>
                                        @error('previous_school')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Onglet Informations du Parent -->
                            <div class="tab-pane fade" id="parent" role="tabpanel" aria-labelledby="parent-tab">
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="parent_name" class="form-label">Nom du parent/tuteur</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-user text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('parent_name') is-invalid @enderror" id="parent_name" name="parent_name" value="{{ old('parent_name') }}">
                                        </div>
                                        @error('parent_name')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="parent_phone" class="form-label">Téléphone du parent/tuteur</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-phone text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('parent_phone') is-invalid @enderror" id="parent_phone" name="parent_phone" value="{{ old('parent_phone') }}">
                                        </div>
                                        @error('parent_phone')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="parent_email" class="form-label">Email du parent/tuteur</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-muted"></i></span>
                                            <input type="email" class="form-control border-0 bg-light @error('parent_email') is-invalid @enderror" id="parent_email" name="parent_email" value="{{ old('parent_email') }}">
                                        </div>
                                        @error('parent_email')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="relationship" class="form-label">Relation avec l'étudiant</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-user-friends text-muted"></i></span>
                                            <select class="form-select border-0 bg-light @error('relationship') is-invalid @enderror" id="relationship" name="relationship">
                                                <option value="">Sélectionner</option>
                                                <option value="father" {{ old('relationship') == 'father' ? 'selected' : '' }}>Père</option>
                                                <option value="mother" {{ old('relationship') == 'mother' ? 'selected' : '' }}>Mère</option>
                                                <option value="guardian" {{ old('relationship') == 'guardian' ? 'selected' : '' }}>Tuteur</option>
                                                <option value="other" {{ old('relationship') == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                        @error('relationship')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label for="parent_address" class="form-label">Adresse du parent/tuteur</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                            <input type="text" class="form-control border-0 bg-light @error('parent_address') is-invalid @enderror" id="parent_address" name="parent_address" value="{{ old('parent_address') }}">
                                        </div>
                                        @error('parent_address')
                                            <div class="text-danger mt-1 small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="window.history.back()">
                                <i class="fas fa-times me-2"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styles pour les badges et icônes */
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .icon-box {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Style pour les onglets */
    .nav-tabs {
        border-bottom: 1px solid #e0e0e0;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: var(--esbtp-green);
        border: none;
    }
    
    .nav-tabs .nav-link.active {
        color: var(--esbtp-green);
        background-color: transparent;
        border: none;
    }
    
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--esbtp-green);
        border-radius: 3px 3px 0 0;
    }
    
    /* Style pour les formulaires */
    .form-control:focus, .form-select:focus {
        border-color: var(--esbtp-green);
        box-shadow: 0 0 0 0.25rem rgba(var(--esbtp-green-rgb), 0.25);
    }
    
    .input-group-text {
        color: #6c757d;
    }
    
    /* Animation pour les transitions d'onglets */
    .tab-pane {
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Script pour charger les sections en fonction de la classe sélectionnée
    document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('class_id');
        const sectionSelect = document.getElementById('section_id');
        
        if (classSelect && sectionSelect) {
            classSelect.addEventListener('change', function() {
                const classId = this.value;
                
                // Réinitialiser le select des sections
                sectionSelect.innerHTML = '<option value="">Sélectionner une section</option>';
                
                if (classId) {
                    // Charger les sections pour cette classe
                    fetch(`/api/sections/by-class/${classId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.id;
                                option.textContent = section.name;
                                sectionSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Erreur:', error));
                }
            });
            
            // Déclencher l'événement au chargement si une classe est déjà sélectionnée
            if (classSelect.value) {
                classSelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endsection 