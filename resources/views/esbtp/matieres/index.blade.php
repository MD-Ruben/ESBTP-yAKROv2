@extends('layouts.app')

@section('title', 'Liste des matières')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des matières</h3>
                    <div class="card-tools">
                        <button id="btn-attach-selected" class="btn btn-success mr-2 d-none">
                            <i class="fas fa-link"></i> Attacher
                        </button>
                        <button id="btn-edit-selected" class="btn btn-warning mr-2 d-none">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button id="btn-delete-selected" class="btn btn-danger mr-2 d-none">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                        <a href="{{ route('esbtp.matieres.attach-to-classes') }}" class="btn btn-success mr-2">
                            <i class="fas fa-link"></i> Attacher aux classes
                        </a>
                        <a href="{{ route('esbtp.matieres.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter une matière
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all">
                                            <label class="form-check-label" for="select-all"></label>
                                        </div>
                                    </th>
                                    <th>Code</th>
                                    <th>Nom</th>
                                    <th>Unité d'enseignement</th>
                                    <th>Coefficient</th>
                                    <th>Total heures</th>
                                    <th>Filières</th>
                                    <th>Niveaux</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($matieres as $matiere)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input matiere-checkbox" type="checkbox" id="matiere-{{ $matiere->id }}" value="{{ $matiere->id }}">
                                                <label class="form-check-label" for="matiere-{{ $matiere->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $matiere->code }}</td>
                                        <td>{{ $matiere->name }}</td>
                                        <td>{{ $matiere->uniteEnseignement ? $matiere->uniteEnseignement->name : 'N/A' }}</td>
                                        <td>{{ $matiere->coefficient_default }}</td>
                                        <td>{{ $matiere->total_heures_default }}</td>
                                        <td>
                                            @if($matiere->filieres->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach($matiere->filieres as $filiere)
                                                        <li>{{ $filiere->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="badge badge-secondary">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($matiere->niveaux->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach($matiere->niveaux as $niveau)
                                                        <li>{{ $niveau->name }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="badge badge-secondary">Aucun</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($matiere->is_active)
                                                <span class="badge badge-success">Actif</span>
                                            @else
                                                <span class="badge badge-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('esbtp.matieres.show', $matiere->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.matieres.edit', $matiere->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $matiere->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Modal de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $matiere->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $matiere->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $matiere->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la matière <strong>{{ $matiere->name }}</strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.matieres.destroy', $matiere->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de DataTables
        var table = $('.datatable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            }
        });

        // Gestion de la sélection de toutes les cases à cocher
        $('#select-all').on('change', function() {
            $('.matiere-checkbox').prop('checked', $(this).prop('checked'));
            updateActionButtons();
        });

        // Gestion de la sélection individuelle
        $(document).on('change', '.matiere-checkbox', function() {
            updateActionButtons();

            // Si toutes les cases sont cochées, cocher "Sélectionner tout"
            if ($('.matiere-checkbox:checked').length === $('.matiere-checkbox').length) {
                $('#select-all').prop('checked', true);
            } else {
                $('#select-all').prop('checked', false);
            }
        });

        // Mise à jour de l'affichage des boutons d'action
        function updateActionButtons() {
            var selectedCount = $('.matiere-checkbox:checked').length;

            if (selectedCount > 0) {
                $('#btn-attach-selected').removeClass('d-none');
                $('#btn-delete-selected').removeClass('d-none');

                // Le bouton Modifier n'est visible que si une seule matière est sélectionnée
                if (selectedCount === 1) {
                    $('#btn-edit-selected').removeClass('d-none');
                } else {
                    $('#btn-edit-selected').addClass('d-none');
                }
            } else {
                $('#btn-attach-selected').addClass('d-none');
                $('#btn-edit-selected').addClass('d-none');
                $('#btn-delete-selected').addClass('d-none');
            }
        }

        // Action du bouton Attacher
        $('#btn-attach-selected').on('click', function() {
            var selectedIds = [];
            $('.matiere-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length > 0) {
                // Rediriger vers la page d'attachement avec les IDs sélectionnés
                window.location.href = "{{ route('esbtp.matieres.attach-to-classes') }}?matieres=" + selectedIds.join(',');
            }
        });

        // Action du bouton Modifier
        $('#btn-edit-selected').on('click', function() {
            var selectedId = $('.matiere-checkbox:checked').first().val();
            if (selectedId) {
                window.location.href = "{{ url('esbtp/matieres') }}/" + selectedId + "/edit";
            }
        });

        // Action du bouton Supprimer
        $('#btn-delete-selected').on('click', function() {
            var selectedIds = [];
            $('.matiere-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length > 0 && confirm('Êtes-vous sûr de vouloir supprimer les matières sélectionnées ?')) {
                // Créer un formulaire pour soumettre la suppression
                var form = $('<form>', {
                    'method': 'POST',
                    'action': "{{ route('esbtp.matieres.bulk-delete') }}"
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': "{{ csrf_token() }}"
                }));

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_method',
                    'value': 'DELETE'
                }));

                // Ajouter les IDs des matières sélectionnées
                selectedIds.forEach(function(id) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'matieres[]',
                        'value': id
                    }));
                });

                // Ajouter le formulaire au document et le soumettre
                $('body').append(form);
                form.submit();
            }
        });
    });
</script>
@endsection
