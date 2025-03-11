@extends('layouts.app')

@section('title', 'Évaluation : ' . $evaluation->titre . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de l'évaluation</h5>
                    <div>
                        @if($evaluation->isEditable())
                            <a href="{{ route('esbtp.evaluations.edit', $evaluation) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        @endif
                        <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Informations générales</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Titre</dt>
                                <dd class="col-sm-8">{{ $evaluation->titre }}</dd>

                                <dt class="col-sm-4">Type</dt>
                                <dd class="col-sm-8">{{ ucfirst($evaluation->type) }}</dd>

                                <dt class="col-sm-4">Date</dt>
                                <dd class="col-sm-8">{{ $evaluation->date_evaluation->format('d/m/Y') }}</dd>

                                <dt class="col-sm-4">Durée</dt>
                                <dd class="col-sm-8">{{ $evaluation->duree_minutes }} minutes</dd>

                                <dt class="col-sm-4">Coefficient</dt>
                                <dd class="col-sm-8">{{ $evaluation->coefficient }}</dd>

                                <dt class="col-sm-4">Barème</dt>
                                <dd class="col-sm-8">{{ $evaluation->bareme }} points</dd>
                            </dl>
                        </div>

                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Classe et matière</h6>
                            <dl class="row">
                                <dt class="col-sm-4">Classe</dt>
                                <dd class="col-sm-8">{{ $evaluation->classe->nom }}</dd>

                                <dt class="col-sm-4">Matière</dt>
                                <dd class="col-sm-8">{{ $evaluation->matiere->nom }}</dd>

                                <dt class="col-sm-4">Statut</dt>
                                <dd class="col-sm-8">
                                    @switch($evaluation->status)
                                        @case('draft')
                                            <span class="badge bg-secondary">Brouillon</span>
                                            @break
                                        @case('scheduled')
                                            <span class="badge bg-info">Planifiée</span>
                                            @break
                                        @case('in_progress')
                                            <span class="badge bg-warning">En cours</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Terminée</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Annulée</span>
                                            @break
                                    @endswitch
                                </dd>

                                <dt class="col-sm-4">Publication</dt>
                                <dd class="col-sm-8">
                                    <span class="badge {{ $evaluation->is_published ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $evaluation->is_published ? 'Publiée' : 'Non publiée' }}
                                    </span>
                                </dd>

                                <dt class="col-sm-4">Notes</dt>
                                <dd class="col-sm-8">
                                    <span class="badge {{ $evaluation->notes_published ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $evaluation->notes_published ? 'Publiées' : 'Non publiées' }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                    </div>

                    @if($evaluation->description)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="border-bottom pb-2">Description</h6>
                                <p class="text-muted">{{ $evaluation->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Notes des étudiants</h6>
                            @if($evaluation->notes->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Étudiant</th>
                                                <th>Note</th>
                                                <th>Sur {{ $evaluation->bareme }}</th>
                                                <th>Coefficient</th>
                                                <th>Note finale</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($evaluation->notes as $note)
                                                <tr>
                                                    <td>{{ $note->etudiant->nom_complet }}</td>
                                                    <td>{{ $note->note }}</td>
                                                    <td>{{ $evaluation->bareme }}</td>
                                                    <td>{{ $evaluation->coefficient }}</td>
                                                    <td>{{ $note->note * $evaluation->coefficient }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Aucune note n'a encore été saisie pour cette évaluation.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Informations système</h6>
                            <dl class="row">
                                <dt class="col-sm-3">Créé par</dt>
                                <dd class="col-sm-9">{{ $evaluation->createdBy->name }} le {{ $evaluation->created_at->format('d/m/Y H:i') }}</dd>

                                <dt class="col-sm-3">Dernière modification</dt>
                                <dd class="col-sm-9">{{ $evaluation->updatedBy->name }} le {{ $evaluation->updated_at->format('d/m/Y H:i') }}</dd>
                            </dl>
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
