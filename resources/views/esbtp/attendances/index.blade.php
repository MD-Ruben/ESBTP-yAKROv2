@extends('layouts.app')

@section('title', 'Gestion des présences')

@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-calendar-check"></i>
            </span> Gestion des présences
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Présences</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Liste des présences</h4>
                    
                    <div class="mb-4">
                        <a href="{{ route('esbtp.attendances.create') }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Marquer des présences
                        </a>
                        <a href="{{ route('esbtp.attendances.rapport-form') }}" class="btn btn-gradient-info btn-sm">
                            <i class="mdi mdi-file-chart"></i> Générer un rapport
                        </a>
                    </div>
                    
                    <!-- Filtres -->
                    <div class="mb-4">
                        <form action="{{ route('esbtp.attendances.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="classe_id" class="form-label">Classe</label>
                                <select name="classe_id" id="classe_id" class="form-control form-control-sm">
                                    <option value="">Toutes les classes</option>
                                    @foreach($classes as $classe)
                                        <option value="{{ $classe->id }}" {{ request('classe_id') == $classe->id ? 'selected' : '' }}>
                                            {{ $classe->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="etudiant_id" class="form-label">Étudiant</label>
                                <select name="etudiant_id" id="etudiant_id" class="form-control form-control-sm">
                                    <option value="">Tous les étudiants</option>
                                    @foreach($etudiants as $etudiant)
                                        <option value="{{ $etudiant->id }}" {{ request('etudiant_id') == $etudiant->id ? 'selected' : '' }}>
                                            {{ $etudiant->nom_complet }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" name="date" id="date" class="form-control form-control-sm" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="statut" class="form-label">Statut</label>
                                <select name="statut" id="statut" class="form-control form-control-sm">
                                    <option value="">Tous les statuts</option>
                                    <option value="present" {{ request('statut') == 'present' ? 'selected' : '' }}>Présent</option>
                                    <option value="absent" {{ request('statut') == 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="retard" {{ request('statut') == 'retard' ? 'selected' : '' }}>En retard</option>
                                    <option value="excuse" {{ request('statut') == 'excuse' ? 'selected' : '' }}>Excusé</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-gradient-primary btn-sm form-control">
                                    <i class="mdi mdi-filter"></i> Filtrer
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Tableau des présences -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Classe</th>
                                    <th>Matière/Séance</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Enregistré par</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->etudiant->nom_complet }}</td>
                                        <td>{{ $attendance->seanceCours->emploiTemps->classe->name }}</td>
                                        <td>{{ $attendance->seanceCours->matiere->nom }}</td>
                                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($attendance->statut == 'present')
                                                <span class="badge badge-success">Présent</span>
                                            @elseif($attendance->statut == 'absent')
                                                <span class="badge badge-danger">Absent</span>
                                            @elseif($attendance->statut == 'retard')
                                                <span class="badge badge-warning">En retard</span>
                                            @elseif($attendance->statut == 'excuse')
                                                <span class="badge badge-info">Excusé</span>
                                            @endif
                                        </td>
                                        <td>{{ $attendance->createdBy->name }}</td>
                                        <td>
                                            <a href="{{ route('esbtp.attendances.edit', $attendance) }}" class="btn btn-gradient-info btn-sm">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <a href="{{ route('esbtp.attendances.show', $attendance) }}" class="btn btn-gradient-primary btn-sm">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <form action="{{ route('esbtp.attendances.destroy', $attendance) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-gradient-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette présence ?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune présence enregistrée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $attendances->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 