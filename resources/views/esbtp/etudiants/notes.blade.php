@extends('layouts.app')

@section('title', 'Mes Notes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mes Notes</h3>
                </div>
                <div class="card-body">
                    @if($notes->isEmpty())
                        <div class="alert alert-info">
                            Aucune note disponible pour le moment.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Matière</th>
                                        <th>Type d'évaluation</th>
                                        <th>Date</th>
                                        <th>Note</th>
                                        <th>Coefficient</th>
                                        <th>Note pondérée</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $note)
                                        <tr>
                                            <td>{{ $note->evaluation->matiere->name }}</td>
                                            <td>{{ $note->evaluation->type }}</td>
                                            <td>{{ $note->evaluation->date_evaluation->format('d/m/Y') }}</td>
                                            <td>
                                                @if($note->is_absent)
                                                    <span class="badge badge-danger">Absent</span>
                                                @else
                                                    {{ $note->note }}/{{ $note->evaluation->bareme }}
                                                @endif
                                            </td>
                                            <td>{{ $note->evaluation->coefficient }}</td>
                                            <td>
                                                @if(!$note->is_absent)
                                                    {{ number_format(($note->note * $note->evaluation->coefficient), 2) }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>{{ $note->commentaire }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><strong>Moyenne générale:</strong></td>
                                        <td colspan="2">
                                            @php
                                                $totalPoints = 0;
                                                $totalCoeff = 0;
                                                foreach($notes as $note) {
                                                    if(!$note->is_absent) {
                                                        $totalPoints += ($note->note * $note->evaluation->coefficient);
                                                        $totalCoeff += $note->evaluation->coefficient;
                                                    }
                                                }
                                                $moyenne = $totalCoeff > 0 ? $totalPoints / $totalCoeff : 0;
                                            @endphp
                                            {{ number_format($moyenne, 2) }}/20
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
