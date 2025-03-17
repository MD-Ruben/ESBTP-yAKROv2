@extends('layouts.app')

@section('title', 'Détails de la note - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la note</h5>
                    <div>
                        <a href="{{ route('esbtp.evaluations.show', $note->evaluation) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux détails de l'évaluation
                        </a>
                        <a href="{{ route('esbtp.notes.edit', $note) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Modifier cette note
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations sur l'évaluation</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Titre :</th>
                                                <td>{{ $note->evaluation->titre }}</td>
                                            </tr>
                                            <tr>
                                                <th>Type :</th>
                                                <td>{{ ucfirst($note->evaluation->type) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date :</th>
                                                <td>{{ date('d/m/Y', strtotime($note->evaluation->date_evaluation)) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Classe :</th>
                                                <td>{{ $note->evaluation->classe ? $note->evaluation->classe->name : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Matière :</th>
                                                <td>{{ $note->evaluation->matiere ? $note->evaluation->matiere->name : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Barème :</th>
                                                <td>{{ $note->evaluation->bareme }} points</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations sur l'étudiant</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Matricule :</th>
                                                <td>{{ $note->etudiant->matricule }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nom :</th>
                                                <td>{{ $note->etudiant->nom }}</td>
                                            </tr>
                                            <tr>
                                                <th>Prénom :</th>
                                                <td>{{ $note->etudiant->prenom }}</td>
                                            </tr>
                                            <tr>
                                                <th>Classe :</th>
                                                <td>{{ $note->etudiant->classe ? $note->etudiant->classe->name : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Statut :</th>
                                                <td>
                                                    @if($note->etudiant->active)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informations de la note</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Note :</label>
                                        <div>
                                            @if($note->is_absent)
                                                <span class="badge bg-danger">Absent</span>
                                            @else
                                                <span class="fs-5">{{ $note->note }}/{{ $note->evaluation->bareme }}</span>
                                                <div class="text-muted">Note équivalente sur 20 : {{ number_format(($note->valeur * 20) / $note->evaluation->bareme, 2) }}/20</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Date de saisie :</label>
                                        <div>{{ $note->created_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                    @if($note->updated_at && $note->updated_at != $note->created_at)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Dernière modification :</label>
                                        <div>{{ $note->updated_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            @if($note->commentaire)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Commentaire :</label>
                                <div class="p-3 bg-light rounded">{{ $note->commentaire }}</div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold">Créé par :</label>
                                <div>{{ $note->createdBy ? $note->createdBy->name : 'N/A' }}</div>
                            </div>

                            @if($note->updatedBy)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mis à jour par :</label>
                                <div>{{ $note->updatedBy->name }}</div>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i>Supprimer la note
                            </button>
                            <a href="{{ route('esbtp.notes.edit', $note) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>Modifier cette note
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette note ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et pourrait affecter les calculs de moyennes et les bulletins.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.notes.destroy', $note) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des tooltips si nécessaire
        if (typeof $().tooltip === 'function') {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
</script>
@endsection
