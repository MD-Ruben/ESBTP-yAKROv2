@extends('layouts.app')

@section('title', 'Liste des formations - ESBTP-yAKRO')

@section('page_title', 'Liste des formations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des formations</h5>
                    <a href="{{ route('esbtp.formations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Ajouter une formation
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Code</th>
                                    <th>Filières associées</th>
                                    <th>Niveaux d'études</th>
                                    <th>Matières</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formations as $formation)
                                    <tr>
                                        <td>{{ $formation->id }}</td>
                                        <td>{{ $formation->name }}</td>
                                        <td>{{ $formation->code }}</td>
                                        <td>
                                            @forelse($formation->filieres as $filiere)
                                                <span class="badge bg-info">{{ $filiere->name }}</span>
                                            @empty
                                                <span class="text-muted">Aucune filière associée</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @forelse($formation->niveauxEtudes as $niveau)
                                                <span class="badge bg-primary">{{ $niveau->name }}</span>
                                            @empty
                                                <span class="text-muted">Aucun niveau associé</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $formation->matieres->count() }} matière(s)</span>
                                        </td>
                                        <td>
                                            @if($formation->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.formations.show', $formation) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.formations.edit', $formation) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('esbtp.formations.destroy', $formation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette formation?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucune formation trouvée.</td>
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