@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des Enseignants</h5>
            <a href="{{ route('esbtp.enseignants.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Ajouter un enseignant
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Nom d'utilisateur</th>
                            <th>Email</th>
                            <th>Spécialité</th>
                            <th>Rôles</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enseignants as $enseignant)
                        <tr>
                            <td>{{ $enseignant->id }}</td>
                            <td>{{ $enseignant->name }}</td>
                            <td>{{ $enseignant->username }}</td>
                            <td>{{ $enseignant->email }}</td>
                            <td>{{ $enseignant->specialite ?? 'Non défini' }}</td>
                            <td>
                                @foreach($enseignant->getRoleNames() as $role)
                                    <span class="badge bg-info">{{ $role }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($enseignant->is_active)
                                <span class="badge bg-success">Actif</span>
                                @else
                                <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('esbtp.enseignants.show', $enseignant->id) }}" class="btn btn-sm btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('esbtp.enseignants.edit', $enseignant->id) }}" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if($enseignant->hasRole('superAdmin'))
                                    <form action="{{ route('esbtp.enseignants.demote', $enseignant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir rétrograder cet enseignant du rang de Super Admin?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning" title="Rétrograder">
                                            <i class="fas fa-level-down-alt"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('esbtp.enseignants.promote', $enseignant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir promouvoir cet enseignant au rang de Super Admin?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Promouvoir">
                                            <i class="fas fa-level-up-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    <form action="{{ route('esbtp.enseignants.destroy', $enseignant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucun enseignant trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $enseignants->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 