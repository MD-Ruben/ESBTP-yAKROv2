@extends('layouts.app')

@section('title', 'Évaluation : ' . $evaluation->titre . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'évaluation : {{ $evaluation->titre }}</h5>
                    <div>
                        <a href="{{ route('esbtp.notes.saisie-rapide', $evaluation) }}" class="btn btn-primary me-2">
                            <i class="fas fa-pen me-1"></i>Saisir les notes
                        </a>
                        <a href="{{ route('esbtp.evaluations.edit', $evaluation) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la liste
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
                    
                    <div class="row">
                        <!-- Informations sur l'évaluation -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations générales</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th style="width: 30%">Titre</th>
                                                <td>{{ $evaluation->titre }}</td>
                                            </tr>
                                            <tr>
                                                <th>Type</th>
                                                <td>
                                                    @if($evaluation->type == 'examen')
                                                        <span class="badge bg-danger">Examen</span>
                                                    @elseif($evaluation->type == 'devoir')
                                                        <span class="badge bg-primary">Devoir</span>
                                                    @elseif($evaluation->type == 'quiz')
                                                        <span class="badge bg-info">Quiz</span>
                                                    @elseif($evaluation->type == 'tp')
                                                        <span class="badge bg-warning">TP</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $evaluation->type }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <td>{{ $evaluation->date_evaluation ? date('d/m/Y', strtotime($evaluation->date_evaluation)) : 'Non définie' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Durée</th>
                                                <td>{{ $evaluation->duree_minutes ? $evaluation->duree_minutes . ' minutes' : 'Non définie' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Classe</th>
                                                <td>
                                                    <a href="{{ route('esbtp.classes.show', $evaluation->classe) }}">
                                                        {{ $evaluation->classe->name }}
                                                    </a> 
                                                    ({{ $evaluation->classe->filiere->name }} - {{ $evaluation->classe->niveau->name }})
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Matière</th>
                                                <td>
                                                    <a href="{{ route('esbtp.matieres.show', $evaluation->matiere) }}">
                                                        {{ $evaluation->matiere->name }} ({{ $evaluation->matiere->code }})
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Coefficient</th>
                                                <td>{{ $evaluation->coefficient }}</td>
                                            </tr>
                                            <tr>
                                                <th>Barème</th>
                                                <td>{{ $evaluation->bareme }} points</td>
                                            </tr>
                                            <tr>
                                                <th>Statut</th>
                                                <td>
                                                    @if($evaluation->is_published)
                                                        <span class="badge bg-success">Publié</span>
                                                    @else
                                                        <span class="badge bg-warning">Non publié</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Créée le</th>
                                                <td>{{ date('d/m/Y à H:i', strtotime($evaluation->created_at)) }} par {{ $evaluation->createdBy ? $evaluation->createdBy->name : 'Système' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Dernière modification</th>
                                                <td>{{ date('d/m/Y à H:i', strtotime($evaluation->updated_at)) }} par {{ $evaluation->updatedBy ? $evaluation->updatedBy->name : 'Système' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistiques et description -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Description</h6>
                                </div>
                                <div class="card-body">
                                    @if($evaluation->description)
                                        <p>{{ $evaluation->description }}</p>
                                    @else
                                        <p class="text-muted">Aucune description fournie.</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Statistiques des notes</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center p-3">
                                                    <h2 class="mb-0">{{ $evaluation->notes->count() }} / {{ $evaluation->classe->nombre_etudiants }}</h2>
                                                    <div>Notes saisies</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center p-3">
                                                    <h2 class="mb-0">{{ $evaluation->notes->count() > 0 ? number_format($evaluation->moyenne, 2) : 'N/A' }}</h2>
                                                    <div>Moyenne générale</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center p-3">
                                                    <h2 class="mb-0">{{ $evaluation->notes->count() > 0 ? number_format($evaluation->notes->max('note'), 2) : 'N/A' }}</h2>
                                                    <div>Note la plus élevée</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body text-center p-3">
                                                    <h2 class="mb-0">{{ $evaluation->notes->count() > 0 ? number_format($evaluation->notes->min('note'), 2) : 'N/A' }}</h2>
                                                    <div>Note la plus basse</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Liste des notes -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                    <h6 class="mb-0">Liste des notes</h6>
                                    <a href="{{ route('esbtp.notes.saisie-rapide', $evaluation) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-pen me-1"></i>Saisie rapide
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped datatable">
                                            <thead>
                                                <tr>
                                                    <th>Matricule</th>
                                                    <th>Étudiant</th>
                                                    <th>Note</th>
                                                    <th>Note/20</th>
                                                    <th>Mention</th>
                                                    <th>Absent</th>
                                                    <th>Commentaire</th>
                                                    <th>Dernière modification</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($evaluation->classe->etudiants as $etudiant)
                                                    @php
                                                        $note = $evaluation->notes->firstWhere('etudiant_id', $etudiant->id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $etudiant->matricule }}</td>
                                                        <td>
                                                            <a href="{{ route('esbtp.etudiants.show', $etudiant) }}">
                                                                {{ $etudiant->nom }} {{ $etudiant->prenoms }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            @if($note)
                                                                {{ $note->is_absent ? 'ABS' : number_format($note->note, 2) }}
                                                            @else
                                                                <span class="text-muted">Non noté</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($note && !$note->is_absent)
                                                                {{ number_format($note->note_vingt, 2) }}/20
                                                            @elseif($note && $note->is_absent)
                                                                <span class="badge bg-secondary">Absent</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($note && !$note->is_absent)
                                                                @if($note->note_vingt >= 16)
                                                                    <span class="badge bg-success">Très Bien</span>
                                                                @elseif($note->note_vingt >= 14)
                                                                    <span class="badge bg-info">Bien</span>
                                                                @elseif($note->note_vingt >= 12)
                                                                    <span class="badge bg-primary">Assez Bien</span>
                                                                @elseif($note->note_vingt >= 10)
                                                                    <span class="badge bg-warning">Passable</span>
                                                                @else
                                                                    <span class="badge bg-danger">Insuffisant</span>
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($note && $note->is_absent)
                                                                <span class="badge bg-danger">Oui</span>
                                                            @else
                                                                <span class="badge bg-success">Non</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($note && $note->commentaire)
                                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $note->commentaire }}">
                                                                    <i class="fas fa-comment"></i>
                                                                </button>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($note)
                                                                {{ date('d/m/Y', strtotime($note->updated_at)) }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($note)
                                                                <div class="btn-group" role="group">
                                                                    <a href="{{ route('esbtp.notes.edit', $note) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form action="{{ route('esbtp.notes.destroy', $note) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette note?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @else
                                                                <a href="{{ route('esbtp.notes.create', ['evaluation_id' => $evaluation->id, 'etudiant_id' => $etudiant->id]) }}" class="btn btn-sm btn-primary" title="Ajouter une note">
                                                                    <i class="fas fa-plus"></i>
                                                                </a>
                                                            @endif
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
                <p>Êtes-vous sûr de vouloir supprimer cette évaluation ?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les notes associées à cette évaluation.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('esbtp.evaluations.destroy', $evaluation) }}" method="POST">
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
        // Initialisation des tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Initialisation de DataTable
        $('.datatable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            },
            "order": [[2, 'desc']]
        });
    });
</script>
@endsection 