@extends('layouts.app')

@section('title', 'Gestion des Notes | ESBTP-yAKRO')

@section('page_title', 'Gestion des Notes')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Liste des Notes</h3>
                    <div>
                        <a href="{{ route('esbtp.notes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Ajouter une note
                        </a>
                        <a href="{{ route('esbtp.notes.index') }}" class="btn btn-info ml-2">
                            <i class="fas fa-sync mr-1"></i> Actualiser
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

                    @if (session('info'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('esbtp.notes.index') }}" method="GET" class="row">
                                <div class="col-md-5 mb-2">
                                    <label for="classe_id">Classe :</label>
                                    <select class="form-control select2" id="classe_id" name="classe_id">
                                        <option value="">Toutes les classes</option>
                                        @foreach($classes as $classe)
                                            <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                                {{ $classe->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5 mb-2">
                                    <label for="matiere_id">Matière :</label>
                                    <select class="form-control select2" id="matiere_id" name="matiere_id">
                                        <option value="">Toutes les matières</option>
                                        @foreach($matieres as $matiere)
                                            <option value="{{ $matiere->id }}" {{ request('matiere_id') == $matiere->id ? 'selected' : '' }}>
                                                {{ $matiere->name ?? $matiere->nom ?? 'Matière sans nom' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter mr-1"></i> Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des notes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Classe</th>
                                    <th>Matière</th>
                                    <th>Évaluation</th>
                                    <th>Note</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notes as $note)
                                    <tr>
                                        <td>{{ $note->etudiant->nom }} {{ $note->etudiant->prenoms }}</td>
                                        <td>{{ $note->evaluation->classe->name }}</td>
                                        <td>{{ $note->evaluation->matiere->name }}</td>
                                        <td>{{ $note->evaluation->titre }}</td>
                                        <td>
                                            @if($note->is_absent)
                                                <span class="badge badge-danger">Absent</span>
                                            @else
                                                {{ $note->note }}/{{ $note->evaluation->bareme }}
                                            @endif
                                        </td>
                                        <td>{{ $note->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('esbtp.notes.show', $note->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('esbtp.notes.edit', $note->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $note->id }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune note trouvée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $('.dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "order": [[ 5, "desc" ]]
        });
    });
</script>
@endsection