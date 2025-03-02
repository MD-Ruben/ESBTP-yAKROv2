@extends('layouts.app')

@section('title', 'Nouveau message')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nouveau message</h5>
                    <a href="{{ route('parent.messages.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour aux messages
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('parent.messages.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="receiver_id" class="form-label">Destinataire <span class="text-danger">*</span></label>
                            <select name="receiver_id" id="receiver_id" class="form-select select2" required>
                                <option value="">Sélectionnez un destinataire</option>
                                
                                @if($admins->count() > 0)
                                    <optgroup label="Administration">
                                        @foreach($admins as $admin)
                                            <option value="{{ $admin->id }}">{{ $admin->name }} - Administrateur</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if($enseignants->count() > 0)
                                    <optgroup label="Enseignants">
                                        @foreach($enseignants as $enseignant)
                                            <option value="{{ $enseignant->user_id }}">{{ $enseignant->nom_complet }} - Enseignant</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                            <small class="form-text text-muted">Sélectionnez le destinataire de votre message.</small>
                        </div>
                        
                        @if($etudiants->count() > 0)
                            <div class="mb-3">
                                <label for="etudiant_id" class="form-label">Concerne l'étudiant</label>
                                <select name="etudiant_id" id="etudiant_id" class="form-select select2">
                                    <option value="">Aucun étudiant spécifique</option>
                                    @foreach($etudiants as $etudiant)
                                        <option value="{{ $etudiant->id }}">{{ $etudiant->prenoms }} {{ $etudiant->nom }} - {{ $etudiant->matricule }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Si votre message concerne un étudiant spécifique, veuillez le sélectionner.</small>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" required>
                            <small class="form-text text-muted">Indiquez le sujet de votre message.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                            <small class="form-text text-muted">Rédigez votre message. Soyez clair et concis.</small>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endsection 