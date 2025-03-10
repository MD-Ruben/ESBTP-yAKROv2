@extends('layouts.app')

@section('title', 'Résultats de la classe ' . $classe->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Résultats de la classe {{ $classe->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.resultats.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($etudiants as $etudiant)
                                    <tr>
                                        <td>{{ $etudiant->matricule }}</td>
                                        <td>{{ $etudiant->nom }}</td>
                                        <td>{{ $etudiant->prenom }}</td>
                                        <td>
                                            <a href="{{ route('esbtp.resultats.etudiant', [$classe->id, $etudiant->id]) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> Voir les résultats
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Aucun étudiant trouvé dans cette classe</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
