@extends('layouts.app')

@section('title', 'Modifier la présence')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-calendar-check"></i>
            </span> Modifier la présence
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esbtp.attendances.index') }}">Présences</a></li>
                <li class="breadcrumb-item active" aria-current="page">Modifier</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Modifier la présence de {{ $attendance->etudiant->nom_complet }}</h4>
                    
                    <form action="{{ route('esbtp.attendances.update', $attendance) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Étudiant</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $attendance->etudiant->nom_complet }}" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Classe</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $attendance->seanceCours->emploiTemps->classe->name }}" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Matière</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $attendance->seanceCours->matiere->nom }}" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Séance</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $attendance->seanceCours->jour_semaine_texte }} - {{ $attendance->seanceCours->plage_horaire }}" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Date</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ $attendance->date->format('d/m/Y') }}" disabled>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Statut</label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="present" value="present" {{ $attendance->statut == 'present' ? 'checked' : '' }}>
                                    <label class="form-check-label text-success" for="present">Présent</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="absent" value="absent" {{ $attendance->statut == 'absent' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger" for="absent">Absent</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="retard" value="retard" {{ $attendance->statut == 'retard' ? 'checked' : '' }}>
                                    <label class="form-check-label text-warning" for="retard">En retard</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="statut" id="excuse" value="excuse" {{ $attendance->statut == 'excuse' ? 'checked' : '' }}>
                                    <label class="form-check-label text-info" for="excuse">Excusé</label>
                                </div>
                                @error('statut')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="commentaire" class="col-sm-3 col-form-label">Commentaire</label>
                            <div class="col-sm-9">
                                <textarea name="commentaire" id="commentaire" class="form-control" rows="3" placeholder="Commentaire (optionnel)">{{ $attendance->commentaire }}</textarea>
                                @error('commentaire')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="mdi mdi-content-save"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('esbtp.attendances.index') }}" class="btn btn-light">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Informations sur l'étudiant</h4>
                    
                    <div class="text-center mb-4">
                        @if($attendance->etudiant->photo)
                            <img src="{{ asset('storage/' . $attendance->etudiant->photo) }}" alt="Photo de l'étudiant" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('assets/images/avatar.jpg') }}" alt="Photo par défaut" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                    </div>
                    
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Matricule</span>
                            <span class="badge badge-primary">{{ $attendance->etudiant->matricule }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Téléphone</span>
                            <span>{{ $attendance->etudiant->telephone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Email</span>
                            <span>{{ $attendance->etudiant->email_personnel }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 