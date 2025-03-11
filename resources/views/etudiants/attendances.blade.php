@extends('layouts.app')

@section('title', 'Mes Absences')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mes Absences</h3>
                </div>
                <div class="card-body">
                    @if($absences->isEmpty())
                        <div class="alert alert-info">
                            Aucune absence enregistrée.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Matière</th>
                                        <th>Type de Séance</th>
                                        <th>Statut</th>
                                        <th>Justification</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($absences as $absence)
                                        <tr>
                                            <td>{{ $absence->date->format('d/m/Y') }}</td>
                                            <td>{{ $absence->matiere->name ?? 'N/A' }}</td>
                                            <td>{{ $absence->type_seance ?? 'N/A' }}</td>
                                            <td>
                                                @if($absence->status === 'absent')
                                                    <span class="badge badge-danger">Absent</span>
                                                @elseif($absence->status === 'retard')
                                                    <span class="badge badge-warning">Retard</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($absence->is_justified)
                                                    <span class="badge badge-success">Justifiée</span>
                                                @else
                                                    <span class="badge badge-secondary">Non justifiée</span>
                                                @endif
                                            </td>
                                            <td>{{ $absence->commentaire ?? 'Aucun commentaire' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h4>Statistiques</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Absences</span>
                                            <span class="info-box-number">{{ $absences->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Absences Justifiées</span>
                                            <span class="info-box-number">{{ $absences->where('is_justified', true)->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Retards</span>
                                            <span class="info-box-number">{{ $absences->where('status', 'retard')->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
