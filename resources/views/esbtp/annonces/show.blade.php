@extends('layouts.app')

@section('title', 'Annonce : ' . $annonce->titre . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $annonce->titre }}</h5>
                    <div>
                        <a href="{{ route('esbtp.annonces.edit', $annonce) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <a href="{{ route('esbtp.annonces.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Informations générales -->
                        <div class="col-md-8">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Contenu de l'annonce</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        {!! nl2br(e($annonce->contenu)) !!}
                                    </div>
                                    
                                    @if($annonce->piece_jointe)
                                        <div class="mb-3">
                                            <h6>Pièce jointe:</h6>
                                            <a href="{{ asset('storage/' . $annonce->piece_jointe) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Télécharger la pièce jointe
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="col-md-4">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th style="width: 40%">Type:</th>
                                                <td>
                                                    @if($annonce->type == 'globale')
                                                        <span class="badge bg-primary">Tous les étudiants</span>
                                                    @elseif($annonce->type == 'classe')
                                                        <span class="badge bg-info">Classes spécifiques</span>
                                                    @elseif($annonce->type == 'etudiant')
                                                        <span class="badge bg-warning">Étudiants spécifiques</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Priorité:</th>
                                                <td>
                                                    @if($annonce->priorite == 0)
                                                        <span class="badge bg-secondary">Normale</span>
                                                    @elseif($annonce->priorite == 1)
                                                        <span class="badge bg-warning">Importante</span>
                                                    @elseif($annonce->priorite == 2)
                                                        <span class="badge bg-danger">Urgente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Statut:</th>
                                                <td>
                                                    @if($annonce->is_published)
                                                        <span class="badge bg-success">Publiée</span>
                                                    @else
                                                        <span class="badge bg-secondary">Brouillon</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date de publication:</th>
                                                <td>{{ $annonce->date_publication ? $annonce->date_publication->format('d/m/Y H:i') : 'Non publiée' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date d'expiration:</th>
                                                <td>{{ $annonce->date_expiration ? $annonce->date_expiration->format('d/m/Y H:i') : 'Aucune' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Créée par:</th>
                                                <td>{{ $annonce->createdBy ? $annonce->createdBy->name : 'Système' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date de création:</th>
                                                <td>{{ $annonce->created_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dernière modification:</th>
                                                <td>{{ $annonce->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Destinataires -->
                        @if($annonce->type != 'globale')
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Destinataires</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($annonce->type == 'classe')
                                            <h6>Classes concernées:</h6>
                                            <div class="mb-3">
                                                @forelse($annonce->classes as $classe)
                                                    <span class="badge bg-info me-1 mb-1">{{ $classe->name }}</span>
                                                @empty
                                                    <p class="text-muted">Aucune classe sélectionnée.</p>
                                                @endforelse
                                            </div>
                                        @elseif($annonce->type == 'etudiant')
                                            <h6>Étudiants concernés:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Matricule</th>
                                                            <th>Nom complet</th>
                                                            <th>Classe</th>
                                                            <th>Statut de lecture</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($annonce->etudiants as $etudiant)
                                                            <tr>
                                                                <td>{{ $etudiant->matricule }}</td>
                                                                <td>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</td>
                                                                <td>{{ $etudiant->classe ? $etudiant->classe->name : 'Non assigné' }}</td>
                                                                <td>
                                                                    @if($etudiant->pivot->is_read)
                                                                        <span class="badge bg-success">Lu le {{ \Carbon\Carbon::parse($etudiant->pivot->read_at)->format('d/m/Y H:i') }}</span>
                                                                    @else
                                                                        <span class="badge bg-danger">Non lu</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center">Aucun étudiant sélectionné.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Bouton de suppression -->
                    <form action="{{ route('esbtp.annonces.destroy', $annonce) }}" method="POST" class="mt-3" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDelete">
                            <i class="fas fa-trash me-1"></i>Supprimer cette annonce
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmDelete" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette annonce ?</p>
                <p><strong>Titre:</strong> {{ $annonce->titre }}</p>
                <p class="text-danger"><strong>Attention:</strong> Cette action est irréversible et supprimera également tous les liens avec les destinataires.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('delete-form').submit();">Supprimer définitivement</button>
            </div>
        </div>
    </div>
</div>
@endsection 