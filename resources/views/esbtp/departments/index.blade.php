@extends('layouts.app')

@section('title', 'Départements ESBTP')
@section('page_title', 'Gestion des Départements')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Liste des Départements</h5>
        <a href="{{ route('esbtp.departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nouveau Département
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Responsable</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr class="{{ $department->trashed() ? 'table-danger' : '' }}">
                            <td>
                                @if($department->logo)
                                    <img src="{{ asset('storage/' . $department->logo) }}" alt="{{ $department->name }}" class="img-thumbnail" style="max-height: 50px;">
                                @else
                                    <div class="bg-light text-center p-2 rounded">
                                        <i class="fas fa-building text-secondary"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->code }}</td>
                            <td>{{ $department->head_name ?? 'Non défini' }}</td>
                            <td>
                                @if($department->trashed())
                                    <span class="badge bg-danger">Supprimé</span>
                                @else
                                    <span class="badge bg-{{ $department->is_active ? 'success' : 'warning' }}">
                                        {{ $department->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if($department->trashed())
                                        <form action="{{ route('esbtp.departments.restore', $department->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                                                <i class="fas fa-trash-restore"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('esbtp.departments.force-delete', $department->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer définitivement" onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce département ?')">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('esbtp.departments.show', $department) }}" class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('esbtp.departments.edit', $department) }}" class="btn btn-sm btn-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('esbtp.departments.destroy', $department) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-warning" title="Archiver" onclick="return confirm('Êtes-vous sûr de vouloir archiver ce département ?')">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun département trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 