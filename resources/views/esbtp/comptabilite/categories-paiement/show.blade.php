@extends('layouts.app')

@section('title', 'Détails de la catégorie : ' . $categorie->nom)

@section('styles')
<style>
    .category-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .category-header {
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
    }
    
    .category-icon {
        font-size: 2.5rem;
        margin-right: 20px;
    }
    
    .category-content {
        padding: 20px;
        background-color: white;
    }
    
    .category-detail {
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .category-detail:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: 500;
        color: #555;
        font-size: 0.9rem;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    
    .detail-value {
        font-size: 1.1rem;
    }
    
    .badge-mandatory {
        background-color: #f39c12;
        color: white;
    }
    
    .badge-active {
        background-color: #2ecc71;
        color: white;
    }
    
    .badge-inactive {
        background-color: #e74c3c;
        color: white;
    }
    
    .related-category {
        display: inline-block;
        padding: 5px 10px;
        margin: 5px;
        border-radius: 15px;
        background-color: #f8f9fa;
        color: #495057;
        transition: all 0.2s ease;
    }
    
    .related-category:hover {
        background-color: #e9ecef;
        text-decoration: none;
    }
    
    .action-btn {
        margin-right: 10px;
        display: inline-flex;
        align-items: center;
    }
    
    .action-btn i {
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <!-- Catégorie principale -->
            <div class="category-card">
                <div class="category-header" style="background-color: {{ $categorie->couleur ?? '#3498db' }}">
                    <i class="{{ $categorie->icone ?? 'fas fa-money-bill' }} category-icon"></i>
                    <div>
                        <h2 class="mb-1">{{ $categorie->nom }}</h2>
                        <div>
                            <span class="badge bg-light text-dark">{{ $categorie->code }}</span>
                            @if($categorie->est_obligatoire)
                            <span class="badge badge-mandatory"><i class="fas fa-exclamation-circle me-1"></i> Obligatoire</span>
                            @endif
                            @if($categorie->est_actif)
                            <span class="badge badge-active"><i class="fas fa-check-circle me-1"></i> Actif</span>
                            @else
                            <span class="badge badge-inactive"><i class="fas fa-times-circle me-1"></i> Inactif</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="category-content">
                    <div class="category-detail">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">{{ $categorie->description ?: 'Aucune description fournie' }}</div>
                    </div>
                    
                    <div class="category-detail">
                        <div class="detail-label">Catégorie parente</div>
                        <div class="detail-value">
                            @if($categorie->parent)
                            <a href="{{ route('esbtp.comptabilite.categories-paiement.show', $categorie->parent->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="{{ $categorie->parent->icone ?? 'fas fa-folder' }} me-1"></i>
                                {{ $categorie->parent->nom }}
                            </a>
                            @else
                            <span class="text-muted">Catégorie principale (aucun parent)</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($categorie->enfants && $categorie->enfants->count() > 0)
                    <div class="category-detail">
                        <div class="detail-label">Sous-catégories</div>
                        <div class="detail-value">
                            @foreach($categorie->enfants as $enfant)
                            <a href="{{ route('esbtp.comptabilite.categories-paiement.show', $enfant->id) }}" class="related-category">
                                <i class="{{ $enfant->icone ?? 'fas fa-folder' }} me-1"></i>
                                {{ $enfant->nom }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <div class="category-detail">
                        <div class="detail-label">Détails techniques</div>
                        <div class="detail-value">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> {{ $categorie->id }}</p>
                                    <p><strong>Code:</strong> {{ $categorie->code }}</p>
                                    <p><strong>Ordre d'affichage:</strong> {{ $categorie->ordre ?? '1' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Créée le:</strong> {{ $categorie->created_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Mise à jour le:</strong> {{ $categorie->updated_at->format('d/m/Y H:i') }}</p>
                                    <p><strong>Statut:</strong> 
                                        @if($categorie->est_actif)
                                        <span class="text-success">Actif</span>
                                        @else
                                        <span class="text-danger">Inactif</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('esbtp.comptabilite.categories-paiement.index') }}" class="btn btn-outline-secondary action-btn">
                                    <i class="fas fa-arrow-left"></i> Retour à la liste
                                </a>
                            </div>
                            <div>
                                <a href="{{ route('esbtp.comptabilite.categories-paiement.edit', $categorie->id) }}" class="btn btn-primary action-btn">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                
                                @if($categorie->est_actif)
                                <button type="button" class="btn btn-warning action-btn" data-bs-toggle="modal" data-bs-target="#toggleStatusModal">
                                    <i class="fas fa-ban"></i> Désactiver
                                </button>
                                @else
                                <button type="button" class="btn btn-success action-btn" data-bs-toggle="modal" data-bs-target="#toggleStatusModal">
                                    <i class="fas fa-check"></i> Activer
                                </button>
                                @endif
                                
                                <button type="button" class="btn btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Paiements liés -->
            <div class="category-card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Paiements associés</h5>
                </div>
                <div class="category-content">
                    @if($paiements && $paiements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Montant</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paiements as $paiement)
                                <tr>
                                    <td>#{{ $paiement->id }}</td>
                                    <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('esbtp.comptabilite.paiements.show', $paiement->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($paiements->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('esbtp.comptabilite.paiements.index', ['categorie' => $categorie->id]) }}" class="btn btn-outline-primary btn-sm">
                            Voir tous les paiements
                        </a>
                    </div>
                    @endif
                    
                    @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> Aucun paiement associé à cette catégorie pour le moment.
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Informations -->
            <div class="category-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations</h5>
                </div>
                <div class="category-content">
                    <div class="alert alert-light mb-0">
                        <h6><i class="fas fa-lightbulb me-2"></i> À savoir</h6>
                        <ul class="mb-0">
                            <li>Les catégories obligatoires sont automatiquement ajoutées à tous les étudiants.</li>
                            <li>Les catégories inactives ne peuvent pas être utilisées pour de nouveaux paiements.</li>
                            <li>Une catégorie avec des sous-catégories ne peut pas être supprimée.</li>
                            <li>Les catégories avec des paiements associés ne peuvent être supprimées que si tous les paiements sont supprimés au préalable.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de statut -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toggleStatusModalLabel">
                    @if($categorie->est_actif)
                    Désactiver la catégorie
                    @else
                    Activer la catégorie
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($categorie->est_actif)
                <p>Êtes-vous sûr de vouloir désactiver la catégorie <strong>{{ $categorie->nom }}</strong> ?</p>
                <p>Les conséquences seront les suivantes :</p>
                <ul>
                    <li>La catégorie ne sera plus disponible pour de nouveaux paiements</li>
                    <li>Les paiements existants ne seront pas affectés</li>
                    <li>La catégorie restera visible dans les rapports et l'historique</li>
                </ul>
                @else
                <p>Êtes-vous sûr de vouloir activer la catégorie <strong>{{ $categorie->nom }}</strong> ?</p>
                <p>Cette catégorie sera à nouveau disponible pour tous les paiements.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.comptabilite.categories-paiement.toggle-status', $categorie->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @if($categorie->est_actif)
                    <button type="submit" class="btn btn-warning">Confirmer la désactivation</button>
                    @else
                    <button type="submit" class="btn btn-success">Confirmer l'activation</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Supprimer la catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Attention ! Cette action est irréversible.
                </div>
                <p>Êtes-vous sûr de vouloir supprimer définitivement la catégorie <strong>{{ $categorie->nom }}</strong> ?</p>
                
                @if($categorie->enfants && $categorie->enfants->count() > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i> Cette catégorie possède des sous-catégories. Vous devez d'abord supprimer ou réaffecter toutes les sous-catégories.
                </div>
                @endif
                
                @if($paiements && $paiements->count() > 0)
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle me-2"></i> Cette catégorie est associée à des paiements. Vous devez d'abord supprimer ou réaffecter tous les paiements.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.comptabilite.categories-paiement.destroy', $categorie->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                        {{ ($categorie->enfants && $categorie->enfants->count() > 0) || ($paiements && $paiements->count() > 0) ? 'disabled' : '' }}>
                        Confirmer la suppression
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 