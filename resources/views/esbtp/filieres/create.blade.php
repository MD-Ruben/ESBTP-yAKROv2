@extends('layouts.app')

@section('title', 'Ajouter une filière - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une nouvelle filière</h5>
                    <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('esbtp.filieres.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom de la filière *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ex: Génie Civil, Mine - Géologie - Pétrole, etc.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code de la filière *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ex: GC, MGP, etc.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="parent_id" class="form-label">Filière parente (pour une option)</label>
                                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                        <option value="">Aucune (filière principale)</option>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" {{ old('parent_id') == $filiere->id ? 'selected' : '' }}>
                                                {{ $filiere->name }} ({{ $filiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez une filière parente si celle-ci est une option (ex: BATIMENT est une option de Génie Civil)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Type de formation *</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="technique" {{ old('type') == 'technique' ? 'selected' : '' }}>Technique</option>
                                        <option value="professionnelle" {{ old('type') == 'professionnelle' ? 'selected' : '' }}>Professionnelle</option>
                                        <option value="universitaire" {{ old('type') == 'universitaire' ? 'selected' : '' }}>Universitaire</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Une brève description de la filière.</small>
                        </div>
                        
                        <div class="row mb-3">
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
                                    <small class="form-text text-muted">Sélectionnez les formations associées à cette filière (Formation générale, Formation technologique, etc.)</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau_etude_ids" class="form-label">Niveaux d'études associés</label>
                                    <select class="form-select @error('niveau_etude_ids') is-invalid @enderror" id="niveau_etude_ids" name="niveau_etude_ids[]" multiple>
                                        @foreach($niveauxEtudes as $niveau)
                                            <option value="{{ $niveau->id }}" {{ in_array($niveau->id, old('niveau_etude_ids', [])) ? 'selected' : '' }}>
                                                {{ $niveau->name }} ({{ $niveau->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_etude_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les niveaux d'études associés à cette filière (BTS 1ère année, BTS 2ème année, etc.)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Filière active
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Une filière inactive ne sera pas disponible lors de la création de classes.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Annuler</button>
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
        $('#parent_id, #type, #formation_ids, #niveau_etude_ids').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Génération automatique du code basé sur le nom
        $('#name').on('blur', function() {
            if ($('#code').val() === '') {
                // Prendre les premières lettres de chaque mot
                let words = $(this).val().split(' ');
                let code = '';
                
                words.forEach(function(word) {
                    if (word.length > 0) {
                        code += word.charAt(0).toUpperCase();
                    }
                });
                
                // Si le code est trop court, ajouter d'autres lettres
                if (code.length < 2 && words[0] && words[0].length > 1) {
                    code += words[0].charAt(1).toUpperCase();
                }
                
                $('#code').val(code);
            }
        });
    });
</script>
@endsection 