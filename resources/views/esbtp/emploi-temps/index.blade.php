@extends('layouts.app')

@section('title', 'Emplois du temps - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <!-- Header and summary stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">Gestion des emplois du temps</h1>
                <div class="d-flex gap-2">
                    @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire') || auth()->user()->can('create_timetable'))
                        <a href="{{ route('esbtp.emploi-temps.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="fas fa-plus-circle me-2"></i>Nouveau
                        </a>
                    @endif
                    @if(auth()->user()->hasRole('superAdmin'))
                    <form action="{{ url('esbtp/activate-all-timetables') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success d-flex align-items-center" title="Active uniquement l'emploi du temps le plus récent pour chaque classe">
                            <i class="fas fa-check-circle me-2"></i>Activer récents
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total emplois du temps</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmploisTemps }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Emplois du temps actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $emploisTempsActifs }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total séances de cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSeances }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Année en cours</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $emploisTempsAnneeEnCours }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main content -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table me-2"></i>Liste des emplois du temps
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable" id="emploiTempsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Classe</th>
                                    <th>Filière</th>
                                    <th>Niveau</th>
                                    <th>Année</th>
                                    <th>Période</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emploisTemps as $emploiTemps)
                                    <tr>
                                        <td>{{ $emploiTemps->classe->name ?? 'Non définie' }}</td>
                                        <td>{{ $emploiTemps->classe->filiere->name ?? 'Non définie' }}</td>
                                        <td>{{ $emploiTemps->classe->niveau->name ?? 'Non défini' }}</td>
                                        <td>{{ $emploiTemps->annee->name ?? 'Non définie' }}</td>
                                        <td>
                                            @if($emploiTemps->semestre == 'Semestre 1')
                                                <span class="badge bg-primary">Semestre 1</span>
                                            @elseif($emploiTemps->semestre == 'Semestre 2')
                                                <span class="badge bg-primary">Semestre 2</span>
                                            @else
                                                <span class="badge bg-primary">Année complète</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($emploiTemps->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                            @if($emploiTemps->is_current)
                                                <span class="badge bg-info ms-1">Courant</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.emploi-temps.show', ['emploi_temp' => $emploiTemps->id]) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->hasRole('superAdmin') || auth()->user()->hasRole('secretaire') || auth()->user()->can('edit_timetables'))
                                                <a href="{{ route('esbtp.emploi-temps.edit', ['emploi_temp' => $emploiTemps->id]) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                                @if(auth()->user()->hasRole('superAdmin') && auth()->user()->can('delete_timetables'))
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $emploiTemps->id }}" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>

                                            @if(auth()->user()->hasRole('superAdmin') && auth()->user()->can('delete_timetables'))
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" id="deleteModal{{ $emploiTemps->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $emploiTemps->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel{{ $emploiTemps->id }}">Confirmation de suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                                <strong>Attention :</strong> Cette action est irréversible.
                                                            </div>
                                                            <p>Êtes-vous sûr de vouloir supprimer cet emploi du temps ?</p>
                                                            <p><strong>Classe :</strong> {{ $emploiTemps->classe->name ?? 'Non définie' }}</p>
                                                            <p><strong>Année universitaire :</strong> {{ $emploiTemps->annee->name ?? 'Non définie' }}</p>
                                                            <p class="text-danger"><strong>Attention :</strong> Cette action supprimera également toutes les séances de cours associées à cet emploi du temps.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="{{ route('esbtp.emploi-temps.destroy', ['emploi_temp' => $emploiTemps->id]) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fas fa-trash me-2"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <p class="mb-0">Aucun emploi du temps n'a été créé.</p>
                                                <a href="{{ route('esbtp.emploi-temps.create') }}" class="btn btn-primary mt-3">
                                                    <i class="fas fa-plus-circle me-1"></i>Créer un emploi du temps
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with filters -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer les emplois du temps
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('esbtp.emploi-temps.index') }}" method="GET" id="filterForm">
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

                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs uniquement</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs uniquement</option>
                                <option value="current" {{ request('status') == 'current' ? 'selected' : '' }}>Courants uniquement</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Appliquer les filtres
                            </button>
                            <a href="{{ route('esbtp.emploi-temps.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo-alt me-2"></i>Réinitialiser les filtres
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick actions card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('esbtp.emploi-temps.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i>Créer un emploi du temps
                        </a>
                        <a href="{{ route('esbtp.timetables.today') }}" class="btn btn-info">
                            <i class="fas fa-calendar-day me-2"></i>Voir l'emploi du temps du jour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        const table = $('#emploiTempsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            pageLength: 10,
            responsive: true,
            order: [[3, 'desc'], [1, 'asc']]
        });

        // Initialize Select2 for enhanced selects
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Submit filter form when selects change
        $('#filiere_id, #niveau_id, #annee_id, #status').change(function() {
            $('#filterForm').submit();
        });
    });
</script>
@endsection
