@extends('layouts.app')

@section('title', 'Ajouter une formation - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une nouvelle formation</h5>
                    <a href="{{ route('esbtp.formations.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('esbtp.formations.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom de la formation *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Exemple : Formation générale, Formation technologique et professionnelle</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code de la formation *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Exemple : FG, FTP</small>
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
                                    <select class="form-select select2 @error('filiere_ids') is-invalid @enderror" id="filiere_ids" name="filiere_ids[]" multiple>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" {{ in_array($filiere->id, old('filiere_ids', [])) ? 'selected' : '' }}>
                                                {{ $filiere->name }} ({{ $filiere->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('filiere_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les filières auxquelles cette formation est rattachée</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau_ids" class="form-label">Niveaux d'études associés</label>
                                    <select class="form-select select2 @error('niveau_ids') is-invalid @enderror" id="niveau_ids" name="niveau_ids[]" multiple>
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}" {{ in_array($niveau->id, old('niveau_ids', [])) ? 'selected' : '' }}>
                                                {{ $niveau->name }} ({{ $niveau->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les niveaux d'études auxquels cette formation est rattachée</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Formation active
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span> 
                                Une formation inactive ne sera pas disponible lors de la création de classes.
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
        // Initialiser Select2 pour les listes déroulantes multiples
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Auto-générer le code à partir du nom
        $('#name').on('blur', function() {
            if ($('#code').val() === '') {
                let code = $(this).val()
                    .replace(/[^a-zA-Z0-9]/g, ' ') // Remplacer les caractères spéciaux par des espaces
                    .replace(/\s+/g, ' ')        // Remplacer les multiples espaces par un seul
                    .trim()                       // Supprimer les espaces au début et à la fin
                    .split(' ')                   // Diviser en mots
                    .map(word => word.charAt(0).toUpperCase())  // Prendre la première lettre de chaque mot
                    .join('');                    // Joindre les lettres
                
                $('#code').val(code);
            }
        });
    });
</script>
@endsection 