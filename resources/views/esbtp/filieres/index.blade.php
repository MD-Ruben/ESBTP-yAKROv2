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
                                    <th>Formations associées</th>
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
                                            @forelse($filiere->formations as $formation)
                                                <span class="badge bg-secondary">{{ $formation->name }}</span>
                                            @empty
                                                <span class="text-muted">Aucune formation associée</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @php
                                                $niveaux = [];
                                                foreach($filiere->formations as $formation) {
                                                    foreach($formation->niveauxEtudes as $niveau) {
                                                        $niveaux[$niveau->id] = $niveau->name;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if(count($niveaux) > 0)
                                                @foreach($niveaux as $id => $name)
                                                    <span class="badge bg-success">{{ $name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Aucun niveau associé</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $filiere->classes->count() }} classe(s)</span>
                                        </td>
                                        <td>
                                            @if($filiere->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.filieres.show', $filiere) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.filieres.edit', $filiere) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $filiere->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $filiere->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $filiere->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $filiere->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Êtes-vous sûr de vouloir supprimer cette filière ?</p>
                                                            <p><strong>Nom :</strong> {{ $filiere->name }}</p>
                                                            
                                                            @if($filiere->classes->count() > 0 || $filiere->options->count() > 0)
                                                                <div class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                                    <strong>Attention :</strong> Cette filière est liée à :
                                                                    <ul class="mb-0 mt-1">
                                                                        @if($filiere->classes->count() > 0)
                                                                            <li>{{ $filiere->classes->count() }} classe(s)</li>
                                                                        @endif
                                                                        @if($filiere->options->count() > 0)
                                                                            <li>{{ $filiere->options->count() }} option(s)</li>
                                                                        @endif
                                                                    </ul>
                                                                    La suppression de cette filière pourrait avoir des conséquences importantes.
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.filieres.destroy', $filiere) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucune filière trouvée.</td>
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