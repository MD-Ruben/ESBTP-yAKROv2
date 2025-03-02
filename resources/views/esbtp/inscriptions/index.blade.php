@extends('layouts.app')

@section('title', 'Gestion des Inscriptions')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Inscriptions</h1>
        @can('inscriptions.create')
        <a href="{{ route('esbtp.inscriptions.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Nouvelle Inscription
        </a>
        @endcan
    </div>

    <!-- Filtres de recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrer les inscriptions</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('esbtp.inscriptions.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search">Recherche par nom ou matricule</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="filiere">Filière</label>
                        <select class="form-control" id="filiere" name="filiere">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $fil)
                                <option value="{{ $fil->id }}" {{ request('filiere') == $fil->id ? 'selected' : '' }}>
                                    {{ $fil->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="niveau">Niveau d'études</label>
                        <select class="form-control" id="niveau" name="niveau">
                            <option value="">Tous les niveaux</option>
                            @foreach($niveaux as $niv)
                                <option value="{{ $niv->id }}" {{ request('niveau') == $niv->id ? 'selected' : '' }}>
                                    {{ $niv->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="annee">Année universitaire</label>
                        <select class="form-control" id="annee" name="annee">
                            <option value="">Toutes les années</option>
                            @foreach($annees as $an)
                                <option value="{{ $an->id }}" {{ request('annee') == $an->id ? 'selected' : '' }}>
                                    {{ $an->annee_scolaire }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status">Statut</label>
                        <select class="form-control" id="status" name="status">
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Toutes</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validées</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulées</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des inscriptions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des inscriptions</h6>
        </div>
        <div class="card-body">
            @if($inscriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>N° Inscription</th>
                            <th>Matricule</th>
                            <th>Étudiant</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Année Universitaire</th>
                            <th>Statut</th>
                            <th>Date d'inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inscriptions as $inscription)
                        <tr>
                            <td>{{ $inscription->numero_inscription }}</td>
                            <td>{{ $inscription->etudiant->matricule ?? 'N/A' }}</td>
                            <td>{{ $inscription->etudiant->nom ?? '' }} {{ $inscription->etudiant->prenoms ?? '' }}</td>
                            <td>{{ $inscription->filiere->nom ?? 'N/A' }}</td>
                            <td>{{ $inscription->niveau->nom ?? 'N/A' }}</td>
                            <td>{{ $inscription->anneeUniversitaire->annee_scolaire ?? 'N/A' }}</td>
                            <td>
                                @if($inscription->status == 'pending')
                                    <span class="badge badge-warning">En attente</span>
                                @elseif($inscription->status == 'validated')
                                    <span class="badge badge-success">Validée</span>
                                @elseif($inscription->status == 'cancelled')
                                    <span class="badge badge-danger">Annulée</span>
                                @else
                                    <span class="badge badge-secondary">{{ $inscription->status }}</span>
                                @endif
                            </td>
                            <td>{{ $inscription->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('inscriptions.view')
                                    <a href="{{ route('esbtp.inscriptions.show', $inscription->id) }}" class="btn btn-info btn-sm" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('edit inscriptions')
                                    @if($inscription->status == 'pending')
                                    <a href="{{ route('esbtp.inscriptions.edit', $inscription->id) }}" class="btn btn-primary btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @endcan

                                    @if($inscription->status == 'pending')
                                        @can('valider inscriptions')
                                        <button type="button" class="btn btn-success btn-sm valider-btn" 
                                                data-id="{{ $inscription->id }}" title="Valider l'inscription">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <form id="valider-form-{{ $inscription->id }}" action="{{ route('esbtp.inscriptions.valider', $inscription->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        @endcan
                                    @endif

                                    @if($inscription->status == 'pending')
                                        @can('annuler inscriptions')
                                        <button type="button" class="btn btn-warning btn-sm annuler-btn" 
                                                data-id="{{ $inscription->id }}" data-toggle="modal" 
                                                data-target="#annulerModal{{ $inscription->id }}" title="Annuler l'inscription">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                        <!-- Modal d'annulation -->
                                        <div class="modal fade" id="annulerModal{{ $inscription->id }}" tabindex="-1" role="dialog" aria-labelledby="annulerModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="annulerModalLabel">Annulation d'inscription</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir annuler l'inscription de <strong>{{ $inscription->etudiant->nom }} {{ $inscription->etudiant->prenom }}</strong> ?</p>
                                                        <form action="{{ route('esbtp.inscriptions.annuler', $inscription->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="form-group">
                                                                <label for="motif">Motif d'annulation</label>
                                                                <textarea class="form-control" id="motif" name="motif" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-warning">Confirmer l'annulation</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endcan
                                    @endif

                                    @can('delete inscriptions')
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                            data-id="{{ $inscription->id }}" data-toggle="modal" 
                                            data-target="#deleteModal{{ $inscription->id }}" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $inscription->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer définitivement cette inscription ?</p>
                                                    <p class="text-danger"><strong>Attention:</strong> Cette action est irréversible et supprimera toutes les données associées.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('esbtp.inscriptions.destroy', $inscription->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $inscriptions->appends(request()->query())->links() }}
            </div>
            @else
            <div class="alert alert-info">
                Aucune inscription ne correspond à vos critères de recherche.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialiser les menus déroulants avec select2
        $('#filiere, #niveau, #annee, #status').select2({
            placeholder: 'Sélectionnez une option',
            allowClear: true
        });
    });
</script>
@endsection 