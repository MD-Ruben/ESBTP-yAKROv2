@extends('layouts.app')

@section('title', 'Mon Emploi du Temps')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mon Emploi du Temps</h1>

    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if(isset($inscription) && $inscription)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-graduation-cap me-1"></i>
                Informations sur ma classe
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Classe:</strong> {{ $inscription->classe->name ?? 'Non définie' }}</p>
                        <p><strong>Filière:</strong> {{ $inscription->classe->filiere->name ?? 'Non définie' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Niveau d'études:</strong> {{ $inscription->classe->niveauEtude->name ?? 'Non défini' }}</p>
                        <p><strong>Année universitaire:</strong> {{ $inscription->anneeUniversitaire->libelle ?? 'Non définie' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($emploiTemps)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar me-1"></i>
                Emploi du temps - {{ $inscription->classe->name ?? 'Classe non définie' }}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Horaire</th>
                                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $index => $jour)
                                    <th>{{ $jour }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['08:00-10:00', '10:00-12:00', '13:00-15:00', '15:00-17:00', '17:00-19:00'] as $horaire)
                                <tr>
                                    <td class="align-middle">{{ $horaire }}</td>
                                    @foreach(range(0, 5) as $jour)
                                        <td class="align-middle">
                                            @if(isset($seancesGroupees[$jour]))
                                                @foreach($seancesGroupees[$jour] as $seance)
                                                    @php
                                                        $heureDebut = substr($seance->heure_debut, 0, 5);
                                                        $heureFin = substr($seance->heure_fin, 0, 5);
                                                        $creneauSeance = $heureDebut.'-'.$heureFin;
                                                    @endphp
                                                    @if($creneauSeance === $horaire)
                                                        <div class="p-2 bg-light border rounded">
                                                            <strong>{{ $seance->matiere->name ?? 'Matière non définie' }}</strong><br>
                                                            @if($seance->salle)
                                                                Salle: {{ $seance->salle }}<br>
                                                            @endif
                                                            @if($seance->enseignantName)
                                                                Prof: {{ $seance->enseignantName }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <small class="text-muted">
                    Période: {{ \Carbon\Carbon::parse($emploiTemps->date_debut)->format('d/m/Y') }} -
                    {{ \Carbon\Carbon::parse($emploiTemps->date_fin)->format('d/m/Y') }}
                </small>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucun emploi du temps n'est actuellement disponible pour votre classe.
            @if(isset($inscription) && $inscription)
                <p class="mt-2 mb-0">
                    Vous êtes inscrit dans la classe: <strong>{{ $inscription->classe->name ?? 'Non définie' }}</strong>
                </p>
            @endif
        </div>
    @endif
</div>
@endsection
