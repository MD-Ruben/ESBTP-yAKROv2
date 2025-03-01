@extends('layouts.app')

@section('title', 'Ajouter un niveau d\'étude - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter un nouveau niveau d'étude</h5>
                    <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
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

                    <form action="{{ route('esbtp.niveaux-etudes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom du niveau d'étude *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ex: Première année BTS, Deuxième année Bachelor, etc.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code du niveau d'étude *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ex: BTS1, BAC+3, etc.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau" class="form-label">Numéro d'année *</label>
                                    <select class="form-select @error('niveau') is-invalid @enderror" id="niveau" name="niveau" required>
                                        <option value="">Sélectionner un numéro d'année</option>
                                        @for($i = 1; $i <= 7; $i++)
                                            <option value="{{ $i }}" {{ old('niveau') == $i ? 'selected' : '' }}>
                                                {{ $i }}{{ $i == 1 ? 'ère' : 'ème' }} année
                                            </option>
                                        @endfor
                                    </select>
                                    @error('niveau')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Indiquez à quelle année ce niveau correspond</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="diplome" class="form-label">Diplôme associé</label>
                                    <input type="text" class="form-control @error('diplome') is-invalid @enderror" id="diplome" name="diplome" value="{{ old('diplome') }}">
                                    @error('diplome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Si ce niveau mène à un diplôme, précisez lequel (ex: BTS, Licence, etc.)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filiere_ids" class="form-label">Filières associées</label>
                                    <select class="form-select @error('filiere_ids') is-invalid @enderror" id="filiere_ids" name="filiere_ids[]" multiple>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" {{ in_array($filiere->id, old('filiere_ids', [])) ? 'selected' : '' }}>
                                                {{ $filiere->name }} ({{ $filiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filiere_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les filières auxquelles ce niveau d'étude est rattaché</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="formation_ids" class="form-label">Formations associées</label>
                                    <select class="form-select @error('formation_ids') is-invalid @enderror" id="formation_ids" name="formation_ids[]" multiple>
                                        @foreach($formations as $formation)
                                            <option value="{{ $formation->id }}" {{ in_array($formation->id, old('formation_ids', [])) ? 'selected' : '' }}>
                                                {{ $formation->name }} ({{ $formation->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('formation_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les formations associées à ce niveau d'étude</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Niveau d'étude actif
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Un niveau d'étude inactif ne sera pas disponible lors de la création de classes.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Réinitialiser</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Amélioration des listes déroulantes avec Select2
        $('#niveau, #filiere_ids, #formation_ids').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Génération automatique du code basé sur le nom
        $('#name').on('blur', function() {
            if ($('#code').val() == '') {
                // Générer un code à partir du nom
                let name = $(this).val();
                if (name) {
                    // Prendre les premières lettres de chaque mot et les mettre en majuscules
                    let code = name.split(' ')
                        .map(word => word.charAt(0).toUpperCase())
                        .join('');
                    
                    // Ajouter le numéro d'année si sélectionné
                    let niveau = $('#niveau').val();
                    if (niveau) {
                        code += niveau;
                    }
                    
                    $('#code').val(code);
                }
            }
        });
        
        // Mettre à jour le code quand le niveau change (si le code contient déjà un chiffre à la fin)
        $('#niveau').on('change', function() {
            let code = $('#code').val();
            let niveau = $(this).val();
            
            if (code && niveau) {
                // Si le code se termine par un chiffre, le remplacer par le nouveau niveau
                if (/\d+$/.test(code)) {
                    code = code.replace(/\d+$/, niveau);
                    $('#code').val(code);
                }
            }
        });
    });
</script>
@endsection 