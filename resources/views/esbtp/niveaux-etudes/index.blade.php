@extends('layouts.app')

@section('title', 'Niveaux d\'études - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion des niveaux d'études</h5>
                    <a href="{{ route('esbtp.niveaux-etudes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Ajouter un niveau
                    </a>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-hover" id="niveaux-etudes-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Code</th>
                                    <th>Niveau</th>
                                    <th>Filières associées</th>
                                    <th>Matières associées</th>
                                    <th>Classes</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($niveauxEtudes as $niveauEtude)
                                    <tr>
                                        <td>{{ $niveauEtude->id }}</td>
                                        <td>{{ $niveauEtude->name }}</td>
                                        <td>{{ $niveauEtude->code }}</td>
                                        <td>
                                            @if($niveauEtude->niveau == 1)
                                                <span class="badge bg-secondary">1ère année</span>
                                            @elseif($niveauEtude->niveau == 2)
                                                <span class="badge bg-info">2ème année</span>
                                            @elseif($niveauEtude->niveau == 3)
                                                <span class="badge bg-primary">3ème année</span>
                                            @else
                                                <span class="badge bg-dark">{{ $niveauEtude->niveau }}ème année</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $niveauEtude->filieres->count() }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $niveauEtude->matieres->count() }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $niveauEtude->classes->count() }}</span>
                                        </td>
                                        <td>
                                            @if($niveauEtude->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.niveaux-etudes.show', $niveauEtude) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.niveaux-etudes.edit', $niveauEtude) }}" class="btn btn-sm btn-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $niveauEtude->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Modal de confirmation de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $niveauEtude->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $niveauEtude->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $niveauEtude->id }}">Confirmation de suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Êtes-vous sûr de vouloir supprimer ce niveau d'étude ?</p>
                                                    <p><strong>Nom :</strong> {{ $niveauEtude->name }}</p>
                                                    
                                                    @if($niveauEtude->filieres->count() > 0 || $niveauEtude->matieres->count() > 0 || $niveauEtude->classes->count() > 0)
                                                        <div class="alert alert-danger">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            <strong>Attention :</strong> Ce niveau d'étude est lié à :
                                                            <ul class="mb-0 mt-1">
                                                                @if($niveauEtude->filieres->count() > 0)
                                                                    <li>{{ $niveauEtude->filieres->count() }} filière(s)</li>
                                                                @endif
                                                                @if($niveauEtude->matieres->count() > 0)
                                                                    <li>{{ $niveauEtude->matieres->count() }} matière(s)</li>
                                                                @endif
                                                                @if($niveauEtude->classes->count() > 0)
                                                                    <li>{{ $niveauEtude->classes->count() }} classe(s)</li>
                                                                @endif
                                                            </ul>
                                                            La suppression de ce niveau d'étude pourrait causer des erreurs dans le système. Assurez-vous de supprimer ou de réaffecter ces éléments avant de continuer.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('esbtp.niveaux-etudes.destroy', $niveauEtude) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-light shadow-sm">
                                <span class="info-box-icon bg-primary"><i class="fas fa-layer-group"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total des niveaux</span>
                                    <span class="info-box-number">{{ $niveauxEtudes->count() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-light shadow-sm">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Niveaux actifs</span>
                                    <span class="info-box-number">{{ $niveauxEtudes->where('is_active', true)->count() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="info-box bg-light shadow-sm">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Niveaux inactifs</span>
                                    <span class="info-box-number">{{ $niveauxEtudes->where('is_active', false)->count() }}</span>
                                </div>
                            </div>
                        </div>
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
        $('#niveaux-etudes-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            responsive: true,
            order: [[3, 'asc'], [1, 'asc']]
        });
    });
</script>
@endsection 