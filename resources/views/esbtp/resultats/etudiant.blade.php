@extends('layouts.app')

@section('title', 'Résultats de ' . $etudiant->nom . ' ' . $etudiant->prenom)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Résultats de {{ $etudiant->nom }} {{ $etudiant->prenom }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.resultats.classe', $classe->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la classe
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h5 class="card-title">Informations de l'étudiant</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Matricule:</strong> {{ $etudiant->matricule }}</p>
                                    <p><strong>Nom:</strong> {{ $etudiant->nom }}</p>
                                    <p><strong>Prénom:</strong> {{ $etudiant->prenom }}</p>
                                    <p><strong>Classe:</strong> {{ $classe->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Matière</th>
                                    <th>Évaluation</th>
                                    <th>Date</th>
                                    <th>Note</th>
                                    <th>Coefficient</th>
                                    <th>Note pondérée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notes as $note)
                                    <tr>
                                        <td>{{ $note->evaluation->matiere->name }}</td>
                                        <td>{{ $note->evaluation->title }}</td>
                                        <td>{{ $note->evaluation->date->format('d/m/Y') }}</td>
                                        <td>{{ $note->valeur }}/20</td>
                                        <td>{{ $note->evaluation->coefficient }}</td>
                                        <td>{{ $note->valeur * $note->evaluation->coefficient }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune note trouvée pour cet étudiant</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @if($notes->count() > 0)
                                    @php
                                        $totalCoefficients = $notes->sum(function($note) {
                                            return $note->evaluation->coefficient;
                                        });

                                        $totalPondere = $notes->sum(function($note) {
                                            return $note->valeur * $note->evaluation->coefficient;
                                        });

                                        $moyenne = $totalCoefficients > 0 ? $totalPondere / $totalCoefficients : 0;
                                    @endphp
                                    <tr class="bg-light">
                                        <th colspan="4" class="text-right">Total</th>
                                        <th>{{ $totalCoefficients }}</th>
                                        <th>{{ $totalPondere }}</th>
                                    </tr>
                                    <tr class="bg-info">
                                        <th colspan="5" class="text-right">Moyenne générale</th>
                                        <th>{{ number_format($moyenne, 2) }}/20</th>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
