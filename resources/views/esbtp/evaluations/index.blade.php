@extends('layouts.app')

@section('title', 'Liste des évaluations - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des évaluations</h5>
                    <a href="{{ route('esbtp.evaluations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Ajouter une évaluation
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

                    <!-- Formulaire de filtrage -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Filtrer les évaluations</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('esbtp.evaluations.index') }}" method="GET" class="row g-3">
                                        <div class="col-md-3">
                                            <label for="classe_id" class="form-label">Classe</label>
                                            <select name="classe_id" id="classe_id" class="form-select select2">
                                                <option value="">Toutes les classes</option>
                                                @foreach($classes as $classe)
                                                    <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                                        {{ $classe->name }} ({{ $classe->filiere->name }} - {{ $classe->niveau->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="matiere_id" class="form-label">Matière</label>
                                            <select name="matiere_id" id="matiere_id" class="form-select select2">
                                                <option value="">Toutes les matières</option>
                                                @foreach($matieres as $matiere)
                                                    <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                                        {{ $matiere->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="type" class="form-label">Type d'évaluation</label>
                                            <select name="type" id="type" class="form-select select2">
                                                <option value="">Tous les types</option>
                                                <option value="examen" {{ request('type') == 'examen' ? 'selected' : '' }}>Examen</option>
                                                <option value="devoir" {{ request('type') == 'devoir' ? 'selected' : '' }}>Devoir</option>
                                                <option value="quiz" {{ request('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                                <option value="tp" {{ request('type') == 'tp' ? 'selected' : '' }}>TP</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="is_published" class="form-label">Statut</label>
                                            <select name="is_published" id="is_published" class="form-select select2">
                                                <option value="">Tous</option>
                                                <option value="1" {{ request('is_published') == '1' ? 'selected' : '' }}>Publié</option>
                                                <option value="0" {{ request('is_published') == '0' ? 'selected' : '' }}>Non publié</option>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="date_debut" class="form-label">Date début</label>
                                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                                        </div>

                                        <div class="col-md-3">
                                            <label for="date_fin" class="form-label">Date fin</label>
                                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                                        </div>

                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary me-2">
                                                <i class="fas fa-filter me-1"></i>Filtrer
                                            </button>
                                            <a href="{{ route('esbtp.evaluations.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-redo me-1"></i>Réinitialiser
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Classe</th>
                                    <th>Matière</th>
                                    <th>Date</th>
                                    <th>Coefficient</th>
                                    <th>Barème</th>
                                    <th>Notes</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluations as $evaluation)
                                    <tr>
                                        <td>{{ $evaluation->id }}</td>
                                        <td>{{ $evaluation->titre }}</td>
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
                                        <td>{{ $evaluation->classe->name }}</td>
                                        <td>{{ $evaluation->matiere->name }}</td>
                                        <td>{{ $evaluation->date_evaluation ? date('d/m/Y', strtotime($evaluation->date_evaluation)) : 'Non définie' }}</td>
                                        <td>{{ $evaluation->coefficient }}</td>
                                        <td>{{ $evaluation->bareme }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ $evaluation->notes->count() }} / {{ $evaluation->classe->nombre_etudiants }}</span>
                                        </td>
                                        <td>
                                            @if($evaluation->is_published)
                                                <span class="badge bg-success">Publié</span>
                                            @else
                                                <span class="badge bg-warning">Non publié</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('esbtp.evaluations.show', $evaluation) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.evaluations.edit', $evaluation) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if(auth()->user()->hasRole('secretaire'))
                                                <a href="{{ route('esbtp.notes.saisie-rapide', $evaluation) }}" class="btn btn-sm btn-primary" title="Saisie des notes">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                @endif
                                                <form action="{{ route('esbtp.evaluations.destroy', $evaluation) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette évaluation?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">Aucune évaluation trouvée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $evaluations->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des évaluations -->
    <div class="row mt-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total des évaluations</h5>
                    <p class="card-text fs-4">{{ $totalEvaluations }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Évaluations publiées</h5>
                    <p class="card-text fs-4">{{ $evaluationsPubliees }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Examens</h5>
                    <p class="card-text fs-4">{{ $examens }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Devoirs</h5>
                    <p class="card-text fs-4">{{ $devoirs }}</p>
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
