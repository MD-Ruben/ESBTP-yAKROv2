@extends('layouts.app')

@section('title', 'Mes Évaluations')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mes Évaluations</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($evaluations->isEmpty())
        <div class="alert alert-info">
            Aucune évaluation n'est disponible pour le moment.
        </div>
    @else
        @foreach($evaluations as $type => $typeEvaluations)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tasks me-1"></i>
                    {{ ucfirst($type) }}s
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Matière</th>
                                    <th>Date</th>
                                    <th>Horaire</th>
                                    <th>Coefficient</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($typeEvaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->titre }}</td>
                                        <td>{{ $evaluation->matiere->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($evaluation->date)->format('d/m/Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($evaluation->heure_debut)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($evaluation->heure_fin)->format('H:i') }}
                                        </td>
                                        <td>{{ $evaluation->coefficient }}</td>
                                        <td>{{ $evaluation->description ?? 'Non spécifié' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
