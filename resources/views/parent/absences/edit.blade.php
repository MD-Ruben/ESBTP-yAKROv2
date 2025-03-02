@extends('layouts.app')

@section('title', 'Justifier une absence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Justifier une absence</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent.student.show', $etudiant->id) }}">{{ $etudiant->nom }} {{ $etudiant->prenoms }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent.absences.index', $etudiant->id) }}">Absences</a></li>
                        <li class="breadcrumb-item active">Justifier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Formulaire de justification</h5>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('parent.absences.update', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <label class="col-md-3 col-form-label">Date de l'absence</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="{{ $absence->date->format('d/m/Y') }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-md-3 col-form-label">Cours</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="{{ $absence->cours ? $absence->cours->matiere->nom : 'Non spécifié' }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label class="col-md-3 col-form-label">Horaire</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="{{ $absence->heure_debut && $absence->heure_fin ? $absence->heure_debut->format('H:i') . ' - ' . $absence->heure_fin->format('H:i') : 'Journée complète' }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="motif" class="col-md-3 col-form-label">Motif de l'absence <span class="text-danger">*</span></label>
                            <div class="col-md-9">
                                <select class="form-select @error('motif') is-invalid @enderror" id="motif" name="motif" required>
                                    <option value="">Sélectionner un motif</option>
                                    <option value="Maladie" {{ old('motif') == 'Maladie' ? 'selected' : '' }}>Maladie</option>
                                    <option value="Rendez-vous médical" {{ old('motif') == 'Rendez-vous médical' ? 'selected' : '' }}>Rendez-vous médical</option>
                                    <option value="Problème familial" {{ old('motif') == 'Problème familial' ? 'selected' : '' }}>Problème familial</option>
                                    <option value="Problème de transport" {{ old('motif') == 'Problème de transport' ? 'selected' : '' }}>Problème de transport</option>
                                    <option value="Autre" {{ old('motif') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('motif')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="commentaire" class="col-md-3 col-form-label">Commentaire</label>
                            <div class="col-md-9">
                                <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="3" placeholder="Fournir des détails supplémentaires sur l'absence">{{ old('commentaire') }}</textarea>
                                @error('commentaire')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="document" class="col-md-3 col-form-label">Document justificatif</label>
                            <div class="col-md-9">
                                <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document">
                                <div class="form-text">Formats acceptés : PDF, JPG, JPEG, PNG (max 2 Mo)</div>
                                @error('document')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-9 offset-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmation" required>
                                    <label class="form-check-label" for="confirmation">
                                        Je confirme que les informations fournies sont exactes et véridiques.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">Soumettre la justification</button>
                                <a href="{{ route('parent.absences.show', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" class="btn btn-secondary ms-2">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informations importantes</h5>
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">Processus de justification</h5>
                        <p>La justification soumise sera examinée par l'administration de l'école. Une notification vous sera envoyée une fois qu'elle aura été traitée.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Documents acceptés :</h5>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="mdi mdi-file-document-outline me-2"></i> Certificat médical
                            </li>
                            <li class="list-group-item">
                                <i class="mdi mdi-file-document-outline me-2"></i> Convocation officielle
                            </li>
                            <li class="list-group-item">
                                <i class="mdi mdi-file-document-outline me-2"></i> Attestation d'événement familial
                            </li>
                            <li class="list-group-item">
                                <i class="mdi mdi-file-document-outline me-2"></i> Autre document officiel
                            </li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">Attention</h5>
                        <p class="mb-0">Une justification sans document valable pourrait ne pas être acceptée par l'administration.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Validation supplémentaire du formulaire
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const motifSelect = document.getElementById('motif');
        const commentaireTextarea = document.getElementById('commentaire');
        
        form.addEventListener('submit', function(event) {
            if (motifSelect.value === 'Autre' && commentaireTextarea.value.trim() === '') {
                event.preventDefault();
                alert('Veuillez fournir un commentaire pour préciser le motif "Autre".');
                commentaireTextarea.focus();
            }
        });
    });
</script>
@endsection 