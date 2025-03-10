@extends('layouts.app')

@section('title', 'Liste des filières - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des filières</h5>
                    <a href="{{ route('esbtp.filieres.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Ajouter une filière
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
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Niveaux d'études</th>
                                    <th>Classes</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filieres as $filiere)
                                    <tr>
                                        <td>{{ $filiere->id }}</td>
                                        <td>{{ $filiere->name }}</td>
                                        <td>{{ $filiere->code }}</td>
                                        <td>
                                            @if($filiere->parent)
                                                <span class="badge bg-info">Option de {{ $filiere->parent->name }}</span>
                                            @else
                                                <span class="badge bg-primary">Filière principale</span>
                                            @endif
                                        </td>
                                        <td>
                                            @forelse($filiere->niveauxEtudes as $niveau)
                                                <span class="badge bg-success">{{ $niveau->name }}</span>
                                            @empty
                                                <span class="text-muted">Aucun niveau associé</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $filiere->classes ? $filiere->classes->count() : 0 }} classe(s)</span>
                                        </td>
                                        <td>
                                            @if($filiere->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('esbtp.filieres.show', $filiere->id) }}"><i class="fas fa-eye me-2"></i>Voir</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('esbtp.filieres.edit', $filiere->id) }}"><i class="fas fa-edit me-2"></i>Modifier</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    @if(($filiere->classes ? $filiere->classes->count() : 0) > 0 || ($filiere->options ? $filiere->options->count() : 0) > 0)
                                                        <li><span class="dropdown-item text-muted"><i class="fas fa-lock me-2"></i>Suppression impossible</span></li>
                                                        <li>
                                                            <a class="dropdown-item text-info" href="#" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="Cette filière ne peut pas être supprimée car elle a des dépendances.">
                                                                <i class="fas fa-info-circle me-2"></i>Pourquoi ?
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <ul class="ps-3 mb-0 small text-muted">
                                                                @if($filiere->classes ? $filiere->classes->count() : 0 > 0)
                                                                    <li>{{ ($filiere->classes ? $filiere->classes->count() : 0) }} classe(s)</li>
                                                                @endif
                                                                @if($filiere->options ? $filiere->options->count() : 0 > 0)
                                                                    <li>{{ ($filiere->options ? $filiere->options->count() : 0) }} option(s)</li>
                                                                @endif
                                                            </ul>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form action="{{ route('esbtp.filieres.destroy', $filiere->id) }}" method="POST" class="delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ?')">
                                                                    <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucune filière trouvée.</td>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            }
        });
    });
</script>
@endsection
