@extends('layouts.app')

@section('title', 'Gestion des annonces - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Statistiques -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des annonces
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Annonces publiées
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['published'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Annonces en attente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Urgentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['urgent'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des annonces -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des annonces</h6>
                    <div>
                        <a href="{{ route('esbtp.annonces.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle me-1"></i>Créer une nouvelle annonce
                        </a>
                    </div>
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

                    <!-- Filtres -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Filtres</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('esbtp.annonces.index') }}" method="GET" id="filter-form">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="type" class="form-label">Type d'annonce</label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="">Tous les types</option>
                                            <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>Générale</option>
                                            <option value="classe" {{ request('type') == 'classe' ? 'selected' : '' }}>Classe</option>
                                            <option value="filiere" {{ request('type') == 'filiere' ? 'selected' : '' }}>Filière</option>
                                            <option value="niveau" {{ request('type') == 'niveau' ? 'selected' : '' }}>Niveau d'étude</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select" id="status" name="is_published">
                                            <option value="">Tous les statuts</option>
                                            <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>Publiée</option>
                                            <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>Non publiée</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="urgent" class="form-label">Priorité</label>
                                        <select class="form-select" id="urgent" name="urgent">
                                            <option value="">Toutes les priorités</option>
                                            <option value="1" {{ request('urgent') == '1' ? 'selected' : '' }}>Urgente</option>
                                            <option value="0" {{ request('urgent') == '0' ? 'selected' : '' }}>Normale</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label">Recherche</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Titre, contenu...">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('esbtp.annonces.index') }}" class="btn btn-secondary me-2">Réinitialiser</a>
                                    <button type="submit" class="btn btn-primary">Filtrer</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des annonces -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="annonces-table">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="25%">Titre</th>
                                    <th width="10%">Type</th>
                                    <th width="15%">Destinataires</th>
                                    <th width="10%">Date de publication</th>
                                    <th width="10%">Statut</th>
                                    <th width="15%">Auteur</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($annonces as $annonce)
                                    <tr>
                                        <td>{{ $annonce->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($annonce->priorite == 2)
                                                    <span class="badge bg-danger me-2" title="Urgente">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </span>
                                                @endif
                                                <div>
                                                    <strong>{{ $annonce->titre }}</strong>
                                                    <small class="d-block text-muted">{{ Str::limit($annonce->contenu, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($annonce->type == 'general')
                                                <span class="badge bg-primary">Générale</span>
                                            @elseif($annonce->type == 'classe')
                                                <span class="badge bg-success">Classe</span>
                                            @elseif($annonce->type == 'filiere')
                                                <span class="badge bg-info">Filière</span>
                                            @elseif($annonce->type == 'niveau')
                                                <span class="badge bg-warning">Niveau</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($annonce->type == 'general')
                                                <span class="badge bg-secondary">Tous</span>
                                            @elseif($annonce->type == 'classe')
                                                @if($annonce->classes->count() > 0)
                                                    <span class="badge bg-info">{{ $annonce->classes->count() }} classe(s)</span>
                                                @else
                                                    <span class="badge bg-secondary">Aucune classe</span>
                                                @endif
                                            @elseif($annonce->type == 'etudiant')
                                                @if($annonce->etudiants->count() > 0)
                                                    <span class="badge bg-info">{{ $annonce->etudiants->count() }} étudiant(s)</span>
                                                @else
                                                    <span class="badge bg-secondary">Aucun étudiant</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($annonce->date_publication)
                                                {{ date('d/m/Y H:i', strtotime($annonce->date_publication)) }}
                                            @else
                                                <span class="text-muted">Non publiée</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($annonce->is_published)
                                                <span class="badge bg-success">Publiée</span>
                                            @else
                                                <span class="badge bg-secondary">Non publiée</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $annonce->createdBy ? $annonce->createdBy->name : 'Système' }}
                                            <small class="d-block text-muted">{{ date('d/m/Y', strtotime($annonce->created_at)) }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('esbtp.annonces.show', $annonce) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.annonces.edit', $annonce) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) document.getElementById('delete-form-{{ $annonce->id }}').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $annonce->id }}" action="{{ route('esbtp.annonces.destroy', $annonce) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucune annonce trouvée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $annonces->appends(request()->query())->links() }}
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
        // Soumission automatique du formulaire lors du changement d'un filtre
        $('#type, #status, #urgent').change(function() {
            $('#filter-form').submit();
        });
    });
</script>
@endsection
