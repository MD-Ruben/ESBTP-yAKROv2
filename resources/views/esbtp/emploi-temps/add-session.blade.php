@extends('layouts.app')

@section('title', 'Ajouter une séance')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Ajouter une séance</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('esbtp.emploi-temps.index') }}">Emplois du temps</a></li>
        <li class="breadcrumb-item"><a href="{{ route('esbtp.emploi-temps.show', $emploi_temp) }}">{{ $emploi_temp->titre }}</a></li>
        <li class="breadcrumb-item active">Ajouter une séance</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Nouvelle séance pour l'emploi du temps de {{ $emploi_temp->classe->name ?? 'Classe non définie' }}
        </div>
        <div class="card-body">
            <form action="{{ route('esbtp.emploi-temps.store-session', $emploi_temp) }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="matiere_id" class="form-label">Matière <span class="text-danger">*</span></label>
                            <select name="matiere_id" id="matiere_id" class="form-select @error('matiere_id') is-invalid @enderror" required>
                                <option value="">Sélectionner une matière</option>
                                @foreach($matieres as $matiere)
                                    <option value="{{ $matiere->id }}" {{ old('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                        {{ $matiere->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('matiere_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="enseignant_id" class="form-label">Enseignant</label>
                            <select name="enseignant_id" id="enseignant_id" class="form-select @error('enseignant_id') is-invalid @enderror">
                                <option value="">Sélectionner un enseignant</option>
                                @foreach($enseignants as $enseignant)
                                    <option value="{{ $enseignant->id }}" {{ old('enseignant_id') == $enseignant->id ? 'selected' : '' }}>
                                        {{ $enseignant->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('enseignant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="jour" class="form-label">Jour <span class="text-danger">*</span></label>
                            <select name="jour" id="jour" class="form-select @error('jour') is-invalid @enderror" required>
                                <option value="">Sélectionner un jour</option>
                                <option value="Lundi" {{ old('jour') == 'Lundi' ? 'selected' : '' }}>Lundi</option>
                                <option value="Mardi" {{ old('jour') == 'Mardi' ? 'selected' : '' }}>Mardi</option>
                                <option value="Mercredi" {{ old('jour') == 'Mercredi' ? 'selected' : '' }}>Mercredi</option>
                                <option value="Jeudi" {{ old('jour') == 'Jeudi' ? 'selected' : '' }}>Jeudi</option>
                                <option value="Vendredi" {{ old('jour') == 'Vendredi' ? 'selected' : '' }}>Vendredi</option>
                                <option value="Samedi" {{ old('jour') == 'Samedi' ? 'selected' : '' }}>Samedi</option>
                            </select>
                            @error('jour')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="heure_debut" class="form-label">Heure de début <span class="text-danger">*</span></label>
                            <input type="time" name="heure_debut" id="heure_debut" class="form-control @error('heure_debut') is-invalid @enderror" value="{{ old('heure_debut') }}" required>
                            @error('heure_debut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="heure_fin" class="form-label">Heure de fin <span class="text-danger">*</span></label>
                            <input type="time" name="heure_fin" id="heure_fin" class="form-control @error('heure_fin') is-invalid @enderror" value="{{ old('heure_fin') }}" required>
                            @error('heure_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="salle" class="form-label">Salle</label>
                            <input type="text" name="salle" id="salle" class="form-control @error('salle') is-invalid @enderror" value="{{ old('salle') }}">
                            @error('salle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Détails</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('esbtp.emploi-temps.show', $emploi_temp) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Ajouter la séance</button>
                </div>

                <input type="hidden" name="classe_id" value="{{ $emploi_temp->classe_id }}">
                <input type="hidden" name="annee_universitaire_id" value="{{ $emploi_temp->annee_universitaire_id }}">
            </form>
        </div>
    </div>
</div>
@endsection
