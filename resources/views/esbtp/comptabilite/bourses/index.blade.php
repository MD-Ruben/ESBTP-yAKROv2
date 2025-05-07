@extends('layouts.app')

@section('title', 'Gestion des bourses')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des bourses</h5>
            <a href="{{ route('esbtp.comptabilite.bourses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nouvelle bourse
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
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i> Filtres
                </div>
                <div class="card-body">
                    <form action="{{ route('esbtp.comptabilite.bourses') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="etudiant" class="form-label">Étudiant</label>
                            <input type="text" class="form-control" id="etudiant" name="etudiant" value="{{ request('etudiant') }}" placeholder="Nom de l'étudiant">
                        </div>
                        <div class="col-md-3">
                            <label for="type_bourse" class="form-label">Type de bourse</label>
                            <select class="form-select" id="type_bourse" name="type_bourse">
                                <option value="">Tous les types</option>
                                <option value="mérite" {{ request('type_bourse') == 'mérite' ? 'selected' : '' }}>Bourse au mérite</option>
                                <option value="sociale" {{ request('type_bourse') == 'sociale' ? 'selected' : '' }}>Bourse sociale</option>
                                <option value="excellence" {{ request('type_bourse') == 'excellence' ? 'selected' : '' }}>Bourse d'excellence</option>
                                <option value="partielle" {{ request('type_bourse') == 'partielle' ? 'selected' : '' }}>Bourse partielle</option>
                                <option value="complète" {{ request('type_bourse') == 'complète' ? 'selected' : '' }}>Bourse complète</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="annee" class="form-label">Année universitaire</label>
                            <select class="form-select" id="annee" name="annee">
                                <option value="">Toutes les années</option>
                                @foreach($annees ?? [] as $annee)
                                <option value="{{ $annee->id }}" {{ request('annee') == $annee->id ? 'selected' : '' }}>
                                    {{ $annee->nom }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('statut') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                                <option value="terminée" {{ request('statut') == 'terminée' ? 'selected' : '' }}>Terminée</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Rechercher
                            </button>
                            <a href="{{ route('esbtp.comptabilite.bourses') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tableau des bourses -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Étudiant</th>
                            <th>Type</th>
                            <th>Montant/Pourcentage</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                            <th>Organisme financeur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bourses as $bourse)
                        <tr>
                            <td>{{ $bourse->id }}</td>
                            <td>{{ $bourse->etudiant->nom_complet ?? $bourse->etudiant->user->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($bourse->type_bourse) }}</td>
                            <td>
                                @if($bourse->montant)
                                    {{ number_format($bourse->montant, 0, ',', ' ') }} FCFA
                                @elseif($bourse->pourcentage)
                                    {{ $bourse->pourcentage }}%
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $bourse->date_debut ? $bourse->date_debut->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $bourse->date_fin ? $bourse->date_fin->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                @if($bourse->statut == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($bourse->statut == 'suspendue')
                                    <span class="badge bg-warning">Suspendue</span>
                                @elseif($bourse->statut == 'terminée')
                                    <span class="badge bg-secondary">Terminée</span>
                                @else
                                    <span class="badge bg-info">{{ $bourse->statut }}</span>
                                @endif
                            </td>
                            <td>{{ $bourse->organisme_financeur ?? 'N/A' }}</td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('esbtp.comptabilite.bourses.show', $bourse->id) }}" class="btn btn-sm btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <a href="{{ route('esbtp.comptabilite.bourses.edit', $bourse->id) }}" class="btn btn-sm btn-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('esbtp.comptabilite.bourses.destroy', $bourse->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette bourse?')">
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
                            <td colspan="9" class="text-center">Aucune bourse trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $bourses->withQueryString()->links() }}
            </div>
            
            <!-- Informations complémentaires -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i> Informations sur les bourses
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-lightbulb me-2"></i>Guide d'utilisation</h5>
                        <ul class="mb-0">
                            <li>Les bourses peuvent être attribuées à des étudiants selon différents critères (mérite, sociale, etc.).</li>
                            <li>Une bourse peut être définie par un montant fixe ou un pourcentage des frais de scolarité.</li>
                            <li>Chaque bourse est associée à une année universitaire et a une période de validité définie.</li>
                            <li>Le statut d'une bourse peut être "active", "suspendue" ou "terminée".</li>
                            <li>L'organisme financeur est l'entité qui finance la bourse (école, gouvernement, entreprise, etc.).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 