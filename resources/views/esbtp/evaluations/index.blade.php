@extends('layouts.app')

@section('title', 'Liste des évaluations - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des évaluations</h5>
                    <a href="{{ route('esbtp.evaluations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle évaluation
                    </a>
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

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Classe</th>
                                    <th>Matière</th>
                                    <th>Statut</th>
                                    <th>Publication</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->titre }}</td>
                                        <td>{{ ucfirst($evaluation->type) }}</td>
                                        <td>{{ $evaluation->date_evaluation->format('d/m/Y') }}</td>
                                        <td>{{ $evaluation->classe->nom }}</td>
                                        <td>{{ $evaluation->matiere->nom }}</td>
                                        <td>
                                            <form action="{{ route('esbtp.evaluations.update-status', $evaluation) }}" method="POST" class="d-inline status-form">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm status-select">
                                                    <option value="draft" {{ $evaluation->status === 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                    <option value="scheduled" {{ $evaluation->status === 'scheduled' ? 'selected' : '' }}>Planifiée</option>
                                                    <option value="in_progress" {{ $evaluation->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                                                    <option value="completed" {{ $evaluation->status === 'completed' ? 'selected' : '' }}>Terminée</option>
                                                    <option value="cancelled" {{ $evaluation->status === 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                                </select>
                                                <button type="submit" style="display: none;"></button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('esbtp.evaluations.toggle-published', $evaluation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $evaluation->is_published ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $evaluation->is_published ? 'Publiée' : 'Non publiée' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('esbtp.evaluations.toggle-notes-published', $evaluation) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $evaluation->notes_published ? 'btn-success' : 'btn-secondary' }}" {{ !$evaluation->canPublishNotes() && !$evaluation->notes_published ? 'disabled' : '' }}>
                                                    {{ $evaluation->notes_published ? 'Notes publiées' : 'Notes non publiées' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('esbtp.evaluations.show', $evaluation) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($evaluation->isEditable())
                                                <a href="{{ route('esbtp.evaluations.edit', $evaluation) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($evaluation->isDeletable())
                                                <form action="{{ route('esbtp.evaluations.destroy', $evaluation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucune évaluation trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $evaluations->links() }}
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
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        // Gestionnaire pour la soumission du formulaire de statut
        $('.status-select').on('change', function(e) {
            e.preventDefault();
            const select = $(this);
            const form = select.closest('.status-form');
            const url = form.attr('action');
            const status = select.val();
            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mettre à jour le select avec la nouvelle valeur
                    select.val(data.evaluation.status);
                    // Afficher un message de succès temporaire
                    const alertDiv = $('<div class="alert alert-success alert-dismissible fade show" role="alert">')
                        .text(data.message)
                        .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');
                    form.before(alertDiv);
                    setTimeout(() => alertDiv.alert('close'), 3000);
                } else {
                    console.error('Update failed:', data);
                    alert('La mise à jour du statut a échoué');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la mise à jour du statut');
                // En cas d'erreur, remettre le select à sa valeur précédente
                select.val(select.data('previous-value'));
            });

            // Sauvegarder la valeur précédente pour pouvoir revenir en arrière en cas d'erreur
            select.data('previous-value', status);
        });

        // Sauvegarder la valeur initiale de chaque select
        $('.status-select').each(function() {
            $(this).data('previous-value', $(this).val());
        });

        $('.datatable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            },
            "paging": false,
            "searching": false,
            "info": false
        });
    });
</script>
@endsection
