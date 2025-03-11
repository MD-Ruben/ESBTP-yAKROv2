@extends('layouts.app')

@section('title', 'Mon Bulletin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mes Bulletins</h3>
                </div>
                <div class="card-body">
                    @if($bulletins->isEmpty())
                        <div class="alert alert-info">
                            Aucun bulletin n'est disponible pour le moment.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Année Universitaire</th>
                                        <th>Semestre</th>
                                        <th>Classe</th>
                                        <th>Moyenne Générale</th>
                                        <th>Rang</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bulletins as $bulletin)
                                        <tr>
                                            <td>{{ $bulletin->annee_universitaire }}</td>
                                            <td>{{ $bulletin->semestre }}</td>
                                            <td>{{ $bulletin->classe->name }}</td>
                                            <td>{{ number_format($bulletin->moyenne_generale, 2) }}/20</td>
                                            <td>{{ $bulletin->rang }}/{{ $bulletin->effectif }}</td>
                                            <td>
                                                <a href="{{ route('esbtp.bulletins.show', $bulletin->id) }}"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                <a href="{{ route('esbtp.bulletins.download', $bulletin->id) }}"
                                                   class="btn btn-success btn-sm">
                                                    <i class="fas fa-download"></i> Télécharger
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
