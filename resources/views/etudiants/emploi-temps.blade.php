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

    @if($emploiTemps)
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar me-1"></i>
                Emploi du temps - {{ $etudiant->classe->name }}
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
                                    @foreach(range(1, 6) as $jour)
                                        <td class="align-middle">
                                            @if(isset($seances[$jour]))
                                                @foreach($seances[$jour] as $seance)
                                                    @php
                                                        $heureDebut = substr($seance->heure_debut, 0, 5);
                                                        $heureFin = substr($seance->heure_fin, 0, 5);
                                                        $creneauSeance = $heureDebut.'-'.$heureFin;
                                                    @endphp
                                                    @if($creneauSeance === $horaire)
                                                        <div class="p-2 bg-light border rounded">
                                                            <strong>{{ $seance->matiere->name }}</strong><br>
                                                            @if($seance->salle)
                                                                Salle: {{ $seance->salle }}<br>
                                                            @endif
                                                            @if($seance->professeur)
                                                                Prof: {{ $seance->professeur }}
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
            Aucun emploi du temps n'est actuellement disponible pour votre classe.
        </div>
    @endif
</div>
@endsection
