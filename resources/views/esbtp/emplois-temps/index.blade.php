@extends('layouts.app')

@section('title', 'Emplois du temps - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion des emplois du temps</h5>
                    <a href="{{ route('esbtp.emplois-temps.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Ajouter un emploi du temps
                    </a>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Filière</th>
                                    <th>Niveau</th>
                                    <th>Année universitaire</th>
                                    <th>Période</th>
                                    <th>Statut</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emploisTemps as $emploiTemps)
                                    <tr>
                                        <td>{{ $emploiTemps->classe->name }}</td>
                                        <td>{{ $emploiTemps->classe->filiere->name }}</td>
                                        <td>{{ $emploiTemps->classe->niveau->name }}</td>
                                        <td>{{ $emploiTemps->annee->name }}</td>
                                        <td>
                                            @if($emploiTemps->periode == 'semestre1')
                                                Semestre 1
                                            @elseif($emploiTemps->periode == 'semestre2')
                                                Semestre 2
                                            @else
                                                Année complète
                                            @endif
                                        </td>
                                        <td>
                                            @if($emploiTemps->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </td>
                                        <td>{{ $emploiTemps->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="Actions">
                                                <a href="{{ route('esbtp.emploi-temps.show', $emploiTemps->id) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.emplois-temps.edit', $emploiTemps->id) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $emploiTemps->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $emploiTemps->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $emploiTemps->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $emploiTemps->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Êtes-vous sûr de vouloir supprimer cet emploi du temps ?</p>
                                                            <p><strong>Classe :</strong> {{ $emploiTemps->classe->name }}</p>
                                                            <p><strong>Année universitaire :</strong> {{ $emploiTemps->annee->name }}</p>
                                                            <p class="text-danger"><strong>Attention :</strong> Cette action supprimera également toutes les séances de cours associées à cet emploi du temps.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.emplois-temps.destroy', $emploiTemps->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun emploi du temps n'a été créé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Recherche par classe</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('esbtp.emplois-temps.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="filiere_id" class="form-label">Filière</label>
                            <select class="form-select select2" id="filiere_id" name="filiere_id">
                                <option value="">Toutes les filières</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="niveau_id" class="form-label">Niveau d'études</label>
                            <select class="form-select select2" id="niveau_id" name="niveau_id">
                                <option value="">Tous les niveaux</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                        {{ $niveau->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="annee_id" class="form-label">Année universitaire</label>
                            <select class="form-select select2" id="annee_id" name="annee_id">
                                <option value="">Toutes les années</option>
                                @foreach($annees as $annee)
                                    <option value="{{ $annee->id }}" {{ request('annee_id') == $annee->id ? 'selected' : '' }}>
                                        {{ $annee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nombre total d'emplois du temps
                            <span class="badge bg-primary rounded-pill">{{ $totalEmploisTemps }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Emplois du temps actifs
                            <span class="badge bg-success rounded-pill">{{ $emploisTempsActifs }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Emplois du temps inactifs
                            <span class="badge bg-secondary rounded-pill">{{ $totalEmploisTemps - $emploisTempsActifs }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total des séances de cours
                            <span class="badge bg-info rounded-pill">{{ $totalSeances }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Emplois du temps de l'année en cours
                            <span class="badge bg-warning rounded-pill">{{ $emploisTempsAnneeEnCours }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "responsive": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
            },
            "pageLength": 25,
            "order": [[6, 'desc']] // Trier par date de création par défaut
        });
        
        // Amélioration des listes déroulantes avec Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });
</script>
@endsection 