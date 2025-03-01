@extends('layouts.app')

@section('title', 'Gestion des matières - ' . $classe->name . ' - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion des matières pour la classe: {{ $classe->name }}</h5>
                    <div>
                        <a href="{{ route('esbtp.classes.show', $classe) }}" class="btn btn-info me-2">
                            <i class="fas fa-eye me-1"></i>Voir les détails
                        </a>
                        <a href="{{ route('esbtp.classes.index') }}" class="btn btn-secondary">
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

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Gérez les matières de la classe <strong>{{ $classe->name }}</strong> en ajustant leurs coefficients et le nombre d'heures. La modification des coefficients affectera le calcul des moyennes dans les bulletins.
                    </div>

                    <form action="{{ route('esbtp.classes.updateMatieres', $classe) }}" method="POST">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">Sélection</th>
                                        <th style="width: 10%">Code</th>
                                        <th style="width: 25%">Nom</th>
                                        <th style="width: 20%">Unité d'enseignement</th>
                                        <th style="width: 10%">Coefficient</th>
                                        <th style="width: 10%">Heures totales</th>
                                        <th style="width: 10%">Statut</th>
                                        <th style="width: 10%">Formation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($matieres as $matiere)
                                        @php
                                            $selected = $classe->matieres->contains($matiere->id);
                                            $matiereClasse = $selected ? $classe->matieres->find($matiere->id) : null;
                                            $coefficient = $matiereClasse ? $matiereClasse->pivot->coefficient : $matiere->coefficient_default;
                                            $totalHeures = $matiereClasse ? $matiereClasse->pivot->total_heures : $matiere->total_heures_default;
                                            $isActive = $matiereClasse ? $matiereClasse->pivot->is_active : true;
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input matiere-checkbox" type="checkbox" name="matiere_ids[]" value="{{ $matiere->id }}" id="matiere{{ $matiere->id }}" {{ $selected ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="matiere{{ $matiere->id }}"></label>
                                                </div>
                                            </td>
                                            <td>{{ $matiere->code }}</td>
                                            <td>{{ $matiere->name }}</td>
                                            <td>{{ $matiere->uniteEnseignement ? $matiere->uniteEnseignement->name : 'Non définie' }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" class="form-control form-control-sm coefficient-input" name="coefficients[{{ $matiere->id }}]" value="{{ $coefficient }}" {{ $selected ? '' : 'disabled' }}>
                                            </td>
                                            <td>
                                                <input type="number" min="1" class="form-control form-control-sm heures-input" name="heures[{{ $matiere->id }}]" value="{{ $totalHeures }}" {{ $selected ? '' : 'disabled' }}>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-switch" type="checkbox" name="active[{{ $matiere->id }}]" value="1" id="active{{ $matiere->id }}" {{ $isActive ? 'checked' : '' }} {{ $selected ? '' : 'disabled' }}>
                                                    <label class="form-check-label" for="active{{ $matiere->id }}">
                                                        <span class="badge {{ $isActive ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $isActive ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach($matiere->formations as $formation)
                                                    <span class="badge bg-info">{{ $formation->code }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Aucune matière disponible pour cette classe.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Actions groupées</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary" id="select-all">
                                                <i class="fas fa-check-square me-1"></i>Tout sélectionner
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="deselect-all">
                                                <i class="fas fa-square me-1"></i>Tout désélectionner
                                            </button>
                                            <button type="button" class="btn btn-outline-info" id="select-formations">
                                                <i class="fas fa-filter me-1"></i>Sélectionner par formation
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-1"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sélection par formation -->
<div class="modal fade" id="formationModal" tabindex="-1" aria-labelledby="formationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formationModalLabel">Sélectionner par formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach($formations as $formation)
                        <button type="button" class="list-group-item list-group-item-action formation-item" data-formation-id="{{ $formation->id }}">
                            {{ $formation->name }} ({{ $formation->code }})
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activer/désactiver les champs en fonction de la sélection de la matière
        $('.matiere-checkbox').on('change', function() {
            const row = $(this).closest('tr');
            const inputs = row.find('input:not(.matiere-checkbox)');
            
            if ($(this).is(':checked')) {
                inputs.prop('disabled', false);
            } else {
                inputs.prop('disabled', true);
            }
        });
        
        // Mettre à jour l'étiquette du statut lorsque le switch change
        $('.status-switch').on('change', function() {
            const label = $(this).siblings('label').find('.badge');
            
            if ($(this).is(':checked')) {
                label.removeClass('bg-danger').addClass('bg-success');
                label.text('Active');
            } else {
                label.removeClass('bg-success').addClass('bg-danger');
                label.text('Inactive');
            }
        });
        
        // Tout sélectionner
        $('#select-all').on('click', function() {
            $('.matiere-checkbox').prop('checked', true).trigger('change');
        });
        
        // Tout désélectionner
        $('#deselect-all').on('click', function() {
            $('.matiere-checkbox').prop('checked', false).trigger('change');
        });
        
        // Ouvrir le modal de sélection par formation
        $('#select-formations').on('click', function() {
            $('#formationModal').modal('show');
        });
        
        // Sélectionner les matières par formation
        $('.formation-item').on('click', function() {
            const formationId = $(this).data('formation-id');
            
            // Désélectionner toutes les matières d'abord
            $('.matiere-checkbox').prop('checked', false).trigger('change');
            
            // Sélectionner les matières de la formation choisie
            // Remarque: Ceci nécessite que les données de formation soient disponibles côté client
            // Vous devrez adapter cette partie en fonction de votre structure de données
            
            // Fermer le modal
            $('#formationModal').modal('hide');
        });
    });
</script>
@endsection