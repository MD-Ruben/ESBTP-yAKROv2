@extends('layouts.app')

@section('title', 'Gestion des Secrétaires')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Gestion des Secrétaires</h1>
        <a href="{{ route('esbtp.secretaires.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter un secrétaire
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Liste des secrétaires
        </div>
        <div class="card-body">
            @if($secretaires->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="secretairesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Nom d'utilisateur</th>
                                <th>Téléphone</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secretaires as $secretaire)
                                <tr>
                                    <td>{{ $secretaire->last_name ?? $secretaire->name }}</td>
                                    <td>{{ $secretaire->first_name ?? '' }}</td>
                                    <td>{{ $secretaire->email }}</td>
                                    <td>{{ $secretaire->username }}</td>
                                    <td>{{ $secretaire->phone ?? 'Non renseigné' }}</td>
                                    <td>
                                        @if($secretaire->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>{{ $secretaire->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('esbtp.secretaires.show', $secretaire->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('esbtp.secretaires.edit', $secretaire->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $secretaire->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteModal{{ $secretaire->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $secretaire->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $secretaire->id }}">Confirmation de suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer le secrétaire <strong>{{ $secretaire->name }}</strong> ?
                                                        <p class="text-danger mt-2">Cette action est irréversible.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('esbtp.secretaires.destroy', $secretaire->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $secretaires->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Aucun secrétaire n'a été trouvé.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#secretairesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            "paging": false,
            "info": false
        });
    });
</script>
@endsection
