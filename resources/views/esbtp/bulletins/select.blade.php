@extends('layouts.app')

@section('title', 'Sélection des bulletins - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sélection des bulletins</h5>
                    <a href="{{ route('esbtp.bulletins.index') }}" class="btn btn-secondary">
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Consulter des bulletins</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('esbtp.resultats.index') }}" method="GET">
                                        <div class="mb-3">
                                            <label for="annee_universitaire_id" class="form-label">Année Universitaire</label>
                                            <select class="form-select" id="annee_universitaire_id" name="annee_universitaire_id" required>
                                                <option value="">Sélectionnez une année universitaire</option>
                                                @foreach($annees as $annee)
                                                    <option value="{{ $annee->id }}">{{ $annee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="classe_id" class="form-label">Classe</label>
                                            <select class="form-select" id="classe_id" name="classe_id" required>
                                                <option value="">Sélectionnez une classe</option>
                                                @foreach($classes as $classe)
                                                    <option value="{{ $classe->id }}">
                                                        {{ $classe->name }} ({{ $classe->filiere->name }} - {{ $classe->niveau->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="periode" class="form-label">Période</label>
                                            <select class="form-select" id="periode" name="periode" required>
                                                <option value="">Sélectionnez une période</option>
                                                <option value="semestre1">Semestre 1</option>
                                                <option value="semestre2">Semestre 2</option>
                                                <option value="annuel">Annuel</option>
                                            </select>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-1"></i>Consulter les bulletins
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">Générer des bulletins</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('esbtp.bulletins.generer-classe') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="gen_annee_universitaire_id" class="form-label">Année Universitaire</label>
                                            <select class="form-select" id="gen_annee_universitaire_id" name="annee_universitaire_id" required>
                                                <option value="">Sélectionnez une année universitaire</option>
                                                @foreach($annees as $annee)
                                                    <option value="{{ $annee->id }}">{{ $annee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gen_classe_id" class="form-label">Classe</label>
                                            <select class="form-select" id="gen_classe_id" name="classe_id" required>
                                                <option value="">Sélectionnez une classe</option>
                                                @foreach($classes as $classe)
                                                    <option value="{{ $classe->id }}">
                                                        {{ $classe->name }} ({{ $classe->filiere->name }} - {{ $classe->niveau->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gen_periode" class="form-label">Période</label>
                                            <select class="form-select" id="gen_periode" name="periode" required>
                                                <option value="">Sélectionnez une période</option>
                                                <option value="semestre1">Semestre 1</option>
                                                <option value="semestre2">Semestre 2</option>
                                                <option value="annuel">Annuel</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="force_regenerate" name="force_regenerate" value="1">
                                            <label class="form-check-label" for="force_regenerate">Forcer la régénération des bulletins existants</label>
                                            <small class="form-text text-muted d-block">Cochez cette case pour recalculer les bulletins déjà générés.</small>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-file-pdf me-1"></i>Générer les bulletins
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Améliorer les listes déroulantes avec Select2
        $('#annee_universitaire_id, #classe_id, #periode, #gen_annee_universitaire_id, #gen_classe_id, #gen_periode').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endsection 