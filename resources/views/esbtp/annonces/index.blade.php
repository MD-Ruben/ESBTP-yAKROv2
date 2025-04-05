@extends('layouts.app')

@section('title', 'Gestion des annonces - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <!-- Statistiques en cartes modernes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100 border-start border-5 border-primary">
                <div class="card-body position-relative overflow-hidden py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs fw-bold text-uppercase mb-1 text-primary">Total des annonces</p>
                            <h2 class="mb-0 fw-bold text-gray-800">{{ $stats['total'] ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-primary bg-opacity-10">
                            <i class="fas fa-bullhorn text-primary fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100 border-start border-5 border-success">
                <div class="card-body position-relative overflow-hidden py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs fw-bold text-uppercase mb-1 text-success">Annonces publiées</p>
                            <h2 class="mb-0 fw-bold text-gray-800">{{ $stats['published'] ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-success bg-opacity-10">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100 border-start border-5 border-warning">
                <div class="card-body position-relative overflow-hidden py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs fw-bold text-uppercase mb-1 text-warning">Annonces en attente</p>
                            <h2 class="mb-0 fw-bold text-gray-800">{{ $stats['pending'] ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-warning bg-opacity-10">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-lg h-100 border-start border-5 border-danger">
                <div class="card-body position-relative overflow-hidden py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-xs fw-bold text-uppercase mb-1 text-danger">Urgentes</p>
                            <h2 class="mb-0 fw-bold text-gray-800">{{ $stats['urgent'] ?? 0 }}</h2>
                        </div>
                        <div class="rounded-circle p-3 bg-danger bg-opacity-10">
                            <i class="fas fa-exclamation-circle text-danger fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section principale -->
    <div class="row">
        <div class="col-12">
            <!-- Carte principale -->
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header py-3 bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="fas fa-bullhorn me-2"></i>Gestion des annonces
                    </h5>
                    <a href="{{ route('esbtp.annonces.create') }}" class="btn btn-primary btn-sm rounded-pill">
                        <i class="fas fa-plus-circle me-1"></i>Créer une nouvelle annonce
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Filtres modernisés -->
                    <div class="card shadow-sm border-0 rounded-lg mb-4">
                        <div class="card-header bg-light p-3 border-0">
                            <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filtres</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('esbtp.annonces.index') }}" method="GET" id="filter-form">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="type" class="form-label small fw-bold">Type d'annonce</label>
                                        <select class="form-select form-select-sm shadow-none" id="type" name="type">
                                            <option value="">Tous les types</option>
                                            <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>Générale</option>
                                            <option value="classe" {{ request('type') == 'classe' ? 'selected' : '' }}>Classe</option>
                                            <option value="filiere" {{ request('type') == 'filiere' ? 'selected' : '' }}>Filière</option>
                                            <option value="niveau" {{ request('type') == 'niveau' ? 'selected' : '' }}>Niveau d'étude</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label small fw-bold">Statut</label>
                                        <select class="form-select form-select-sm shadow-none" id="status" name="is_published">
                                            <option value="">Tous les statuts</option>
                                            <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>Publiée</option>
                                            <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>Non publiée</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="urgent" class="form-label small fw-bold">Priorité</label>
                                        <select class="form-select form-select-sm shadow-none" id="urgent" name="urgent">
                                            <option value="">Toutes les priorités</option>
                                            <option value="1" {{ request('urgent') == '1' ? 'selected' : '' }}>Urgente</option>
                                            <option value="0" {{ request('urgent') == '0' ? 'selected' : '' }}>Normale</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="search" class="form-label small fw-bold">Recherche</label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control shadow-none" id="search" name="search" value="{{ request('search') }}" placeholder="Titre, contenu...">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <a href="{{ route('esbtp.annonces.index') }}" class="btn btn-sm btn-outline-secondary me-2 rounded-pill">
                                        <i class="fas fa-sync-alt me-1"></i>Réinitialiser
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill">
                                        <i class="fas fa-filter me-1"></i>Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Affichage des annonces en cartes -->
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-4">
                        @forelse($annonces as $annonce)
                            <div class="col">
                                <div class="card shadow-sm border-0 rounded-lg h-100 annonce-card {{ $annonce->priorite == 2 ? 'border-danger border' : '' }}">
                                    <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center py-3
                                        {{ $annonce->priorite == 2 ? 'bg-danger bg-opacity-10' : ($annonce->is_published ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10') }}">
                                        <h6 class="mb-0 fw-bold text-truncate" style="max-width: 70%;" title="{{ $annonce->titre }}">
                                            @if($annonce->priorite == 2)
                                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            @elseif($annonce->is_published)
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                            @else
                                                <i class="fas fa-clock text-warning me-2"></i>
                                            @endif
                                            {{ $annonce->titre }}
                                        </h6>
                                        <div class="badge {{ $annonce->type == 'general' ? 'bg-primary' : ($annonce->type == 'classe' ? 'bg-success' : ($annonce->type == 'filiere' ? 'bg-info' : 'bg-warning')) }} rounded-pill">
                                            {{ $annonce->type == 'general' ? 'Générale' : ($annonce->type == 'classe' ? 'Classe' : ($annonce->type == 'filiere' ? 'Filière' : 'Niveau')) }}
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="mb-3">
                                            <p class="card-text text-truncate-3 text-muted" style="min-height: 3rem">
                                                {{ Str::limit(strip_tags($annonce->contenu), 120) }}
                                            </p>
                                        </div>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    {{ date('d/m/Y', strtotime($annonce->created_at)) }}
                                                </small>
                                                <div>
                                                    @if($annonce->is_published)
                                                        <span class="badge bg-success">Publiée</span>
                                                    @else
                                                        <span class="badge bg-secondary">Non publiée</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <small class="text-muted">
                                                    <i class="far fa-user me-1"></i>{{ $annonce->createdBy ? $annonce->createdBy->name : 'Système' }}
                                                </small>
                                                <div class="btn-group">
                                                    <a href="{{ route('esbtp.annonces.show', $annonce) }}" class="btn btn-sm btn-outline-info rounded-start">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('esbtp.annonces.edit', $annonce) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-end"
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) document.getElementById('delete-form-{{ $annonce->id }}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-form-{{ $annonce->id }}" action="{{ route('esbtp.annonces.destroy', $annonce) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info rounded-3 shadow-sm">
                                    <i class="fas fa-info-circle me-2"></i>Aucune annonce trouvée.
                                </div>
                            </div>
                        @endforelse
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

@section('styles')
<style>
    /* Style pour les cartes */
    .annonce-card {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .annonce-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .card-header {
        transition: all 0.3s ease;
    }
    .annonce-card:hover .card-header {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .btn-group .btn {
        transition: all 0.2s ease;
    }
    .btn-group .btn:hover {
        z-index: 5;
        transform: translateY(-2px);
    }

    /* Style pour le texte limité à 3 lignes */
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Style pour les badges */
    .badge {
        font-weight: 500;
    }

    /* Style pour les icônes circulaires */
    .rounded-circle {
        transition: all 0.3s ease;
    }
    .card:hover .rounded-circle {
        transform: scale(1.1);
    }

    /* Style pour les boutons d'action */
    .btn-outline-info, .btn-outline-warning, .btn-outline-danger {
        border-width: 1px;
    }

    /* Style pour la pagination */
    .pagination {
        gap: 5px;
    }
    .page-item .page-link {
        border-radius: 50%;
        margin: 0 2px;
        border: none;
        color: #6c757d;
    }
    .page-item.active .page-link {
        background-color: var(--esbtp-green);
        color: white;
    }
    .page-link:focus {
        box-shadow: none;
    }

    /* Media queries pour la responsivité */
    @media (max-width: 767.98px) {
        .row-cols-md-2>* {
            flex: 0 0 auto;
            width: 100%;
        }
    }
</style>
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
