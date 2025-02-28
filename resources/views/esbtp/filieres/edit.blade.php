@extends('layouts.app')

@section('title', 'Modifier la filière : ' . $filiere->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier la filière</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Afficher les erreurs de validation -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('esbtp.filieres.update', $filiere) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Nom de la filière <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $filiere->name) }}" required>
                            <small class="form-text text-muted">Le nom complet de la filière (ex: Génie Civil, Informatique, etc.)</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="code">Code de la filière <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $filiere->code) }}" required>
                            <small class="form-text text-muted">Un code court et unique pour la filière (ex: GC, INFO, etc.)</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="parent_id">Filière parente (pour une option)</label>
                            <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                <option value="">Aucune (filière principale)</option>
                                @foreach($parentFilieres as $parentFiliere)
                                    <option value="{{ $parentFiliere->id }}" {{ old('parent_id', $filiere->parent_id) == $parentFiliere->id ? 'selected' : '' }}>
                                        {{ $parentFiliere->name }} ({{ $parentFiliere->code }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Sélectionnez une filière parente si celle-ci est une option d'une filière principale.</small>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $filiere->description) }}</textarea>
                            <small class="form-text text-muted">Une description détaillée de la filière (optionnel).</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $filiere->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Filière active</label>
                            </div>
                            <small class="form-text text-muted">Une filière inactive ne pourra pas être sélectionnée pour de nouvelles inscriptions.</small>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('esbtp.filieres.show', $filiere) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 