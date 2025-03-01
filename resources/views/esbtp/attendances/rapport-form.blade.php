@extends('layouts.app')

@section('title', 'Générer un rapport de présence')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-file-chart"></i>
            </span> Rapport de présence
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('esbtp.attendances.index') }}">Présences</a></li>
                <li class="breadcrumb-item active" aria-current="page">Rapport</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Générer un rapport de présence</h4>
                    <p class="card-description">
                        Sélectionnez une classe et une période pour générer un rapport de présence.
                    </p>
                    
                    <form action="{{ route('esbtp.attendances.rapport') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="classe_id">Classe</label>
                            <select name="classe_id" id="classe_id" class="form-control" required>
                                <option value="">Sélectionner une classe</option>
                                @foreach($classes as $classe)
                                    <option value="{{ $classe->id }}">{{ $classe->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_debut">Date de début</label>
                                    <input type="date" name="date_debut" id="date_debut" class="form-control" required value="{{ date('Y-m-01') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_fin">Date de fin</label>
                                    <input type="date" name="date_fin" id="date_fin" class="form-control" required value="{{ date('Y-m-t') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="mdi mdi-file-chart"></i> Générer le rapport
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
                    <h4 class="card-title">Informations</h4>
                    <p class="card-description">
                        Le rapport de présence vous permettra de visualiser :
                    </p>
                    <ul class="list-star">
                        <li>Le taux de présence de chaque étudiant</li>
                        <li>Le nombre de présences, d'absences, de retards et d'absences excusées</li>
                        <li>Des statistiques globales par classe</li>
                    </ul>
                    <div class="mt-4">
                        <p>Vous pourrez également :</p>
                        <ul class="list-arrow">
                            <li>Exporter le rapport au format PDF</li>
                            <li>Envoyer le rapport par e-mail</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier que la date de fin est postérieure à la date de début
        document.getElementById('date_fin').addEventListener('change', function() {
            const dateDebut = new Date(document.getElementById('date_debut').value);
            const dateFin = new Date(this.value);
            
            if (dateFin < dateDebut) {
                alert('La date de fin doit être postérieure à la date de début.');
                this.value = document.getElementById('date_debut').value;
            }
        });
        
        document.getElementById('date_debut').addEventListener('change', function() {
            const dateDebut = new Date(this.value);
            const dateFin = new Date(document.getElementById('date_fin').value);
            
            if (dateFin < dateDebut) {
                document.getElementById('date_fin').value = this.value;
            }
        });
    });
</script>
@endsection 