@extends('layouts.app')

@section('title', 'Liste des matières')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des matières</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.matieres.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter une matière
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
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Unité d'enseignement</th>
                                    <th>Coefficient</th>
                                    <th>Total heures</th>
                                    <th>Filières</th>
                                    <th>Niveaux</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matieres as $matiere)
                                    <tr>
                                        <td>{{ $matiere->code }}</td>
                                        <td>{{ $matiere->name }}</td>
                                        <td>{{ $matiere->uniteEnseignement ? $matiere->uniteEnseignement->name : 'N/A' }}</td>
                                        <td>{{ $matiere->coefficient_default }}</td>
                                        <td>{{ $matiere->total_heures_default }}</td>
                                        <td>
                                            @if($matiere->filieres->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach($matiere->filieres as $filiere)
                                                        <li>{{ $filiere->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="badge badge-secondary">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($matiere->niveaux->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach($matiere->niveaux as $niveau)
                                                        <li>{{ $niveau->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="badge badge-secondary">Aucun</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($matiere->is_active)
                                                <span class="badge badge-success">Actif</span>
                                            @else
                                                <span class="badge badge-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('esbtp.matieres.show', $matiere->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.matieres.edit', $matiere->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $matiere->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $matiere->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $matiere->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $matiere->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la matière <strong>{{ $matiere->name }}</strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.matieres.destroy', $matiere->id) }}" method="POST">
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