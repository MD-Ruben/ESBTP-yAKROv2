@extends('layouts.app')

@section('title', 'Gestion des étudiants - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des étudiants</h5>
                    <a href="{{ route('esbtp.inscriptions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Ajouter un étudiant
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtres de recherche -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-filter me-2"></i>Filtres de recherche
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('esbtp.etudiants.index') }}" id="search-form">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="search" class="form-label">Recherche</label>
                                        <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Matricule, nom, prénom, téléphone...">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="filiere" class="form-label">Filière</label>
                                        <select class="form-select" id="filiere" name="filiere">
                                            <option value="">Toutes les filières</option>
                                            @foreach($filieres as $f)
                                                <option value="{{ $f->id }}" {{ isset($filiere) && $filiere == $f->id ? 'selected' : '' }}>
                                                    {{ $f->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="niveau" class="form-label">Niveau d'études</label>
                                        <select class="form-select" id="niveau" name="niveau">
                                            <option value="">Tous les niveaux</option>
                                            @foreach($niveaux as $n)
                                                <option value="{{ $n->id }}" {{ isset($niveau) && $niveau == $n->id ? 'selected' : '' }}>
                                                    {{ $n->name }} ({{ $n->type }} - Année {{ $n->year }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="annee" class="form-label">Année universitaire</label>
                                        <select class="form-select" id="annee" name="annee">
                                            <option value="">Toutes les années</option>
                                            @foreach($annees as $a)
                                                <option value="{{ $a->id }}" {{ isset($annee) && $annee == $a->id ? 'selected' : '' }}>
                                                    {{ $a->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="">Tous les statuts</option>
                                            <option value="actif" {{ isset($status) && $status == 'actif' ? 'selected' : '' }}>Actif</option>
                                            <option value="inactif" {{ isset($status) && $status == 'inactif' ? 'selected' : '' }}>Inactif</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end mb-3">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-search me-1"></i>Filtrer
                                        </button>
                                        <a href="{{ route('esbtp.etudiants.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo-alt me-1"></i>Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des étudiants -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Matricule</th>
                                    <th>Photo</th>
                                    <th>Nom complet</th>
                                    <th>Genre</th>
                                    <th>Contact</th>
                                    <th>Classe actuelle</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($etudiants as $etudiant)
                                    <tr>
                                        <td>{{ $etudiant->matricule }}</td>
                                        <td class="text-center">
                                            @if($etudiant->photo)
                                                <img src="{{ asset('storage/'.$etudiant->photo) }}" alt="Photo" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-secondary"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</td>
                                        <td>{{ $etudiant->genre == 'M' ? 'Masculin' : 'Féminin' }}</td>
                                        <td>
                                            {{ $etudiant->telephone }}<br>
                                            <small>{{ $etudiant->email }}</small>
                                        </td>
                                        <td>
                                            @if($etudiant->inscriptions->count() > 0)
                                                <?php $derniere = $etudiant->inscriptions->sortByDesc('created_at')->first(); ?>
                                                {{ $derniere->classe ? $derniere->classe->name : 'Non assigné' }}
                                                <br>
                                                <small>
                                                    {{ $derniere->filiere ? $derniere->filiere->name : '' }}
                                                    {{ $derniere->niveau ? ' - '.$derniere->niveau->name : '' }}
                                                </small>
                                            @else
                                                <span class="text-muted">Non inscrit</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($etudiant->statut == 'actif')
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.etudiants.show', $etudiant) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.etudiants.edit', $etudiant) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $etudiant->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $etudiant->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $etudiant->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $etudiant->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Êtes-vous sûr de vouloir supprimer l'étudiant <strong>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</strong> ({{ $etudiant->matricule }}) ?</p>
                                                            <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les inscriptions et données associées à cet étudiant.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.etudiants.destroy', $etudiant) }}" method="POST" class="d-inline">
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
                                        <td colspan="8" class="text-center">Aucun étudiant trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $etudiants->appends(request()->query())->links() }}
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
        // Initialisation de Select2 pour les filtres si disponible
        if (typeof $.fn.select2 !== 'undefined') {
            $('#filiere, #niveau, #annee, #status').select2({
                theme: 'bootstrap4',
                placeholder: 'Sélectionner une option',
                allowClear: true
            });
        }

        // Initialisation de DataTables avec pagination côté serveur désactivée
        $('.datatable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": false,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            }
        });
    });
</script>
@endsection 