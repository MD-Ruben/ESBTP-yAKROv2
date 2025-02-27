@extends('layouts.app')

@section('title', 'Détails du Cycle de Formation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Carte d'informations du cycle -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">{{ $cycle->name }}</h3>
                    <p class="text-muted text-center">{{ $cycle->code }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Durée</b> <a class="float-right">{{ $cycle->duration_years }} an(s)</a>
                        </li>
                        <li class="list-group-item">
                            <b>Diplôme délivré</b> <a class="float-right">{{ $cycle->diploma_awarded }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Spécialités</b> <a class="float-right">{{ $cycle->specialties->count() }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Statut</b> 
                            <a class="float-right">
                                @if($cycle->trashed())
                                    <span class="badge badge-danger">Archivé</span>
                                @elseif($cycle->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-warning">Inactif</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    <div class="btn-group w-100">
                        @if(!$cycle->trashed())
                            <a href="{{ route('esbtp.cycles.edit', $cycle->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <form action="{{ route('esbtp.cycles.destroy', $cycle->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce cycle?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Archiver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('esbtp.cycles.restore', $cycle->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-trash-restore"></i> Restaurer
                                </button>
                            </form>
                            <form action="{{ route('esbtp.cycles.force-delete', $cycle->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce cycle? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times-circle"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#description" data-toggle="tab">Description</a></li>
                        <li class="nav-item"><a class="nav-link" href="#specialties" data-toggle="tab">Spécialités</a></li>
                        <li class="nav-item"><a class="nav-link" href="#study-years" data-toggle="tab">Années d'études</a></li>
                    </ul>
                </div><!-- /.card-header -->
                
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Onglet Description -->
                        <div class="active tab-pane" id="description">
                            <div class="post">
                                <div>
                                    {!! $cycle->description ?? 'Aucune description disponible.' !!}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Onglet Spécialités -->
                        <div class="tab-pane" id="specialties">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            <!-- Bouton pour ajouter une spécialité -->
                            <div class="mb-3">
                                <a href="{{ route('esbtp.specialties.create', ['cycle_id' => $cycle->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Ajouter une spécialité
                                </a>
                            </div>
                            
                            <!-- Liste des spécialités -->
                            @if($cycle->specialties->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Code</th>
                                                <th>Description</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cycle->specialties as $specialty)
                                                <tr>
                                                    <td>{{ $specialty->name }}</td>
                                                    <td>{{ $specialty->code }}</td>
                                                    <td>{{ Str::limit(strip_tags($specialty->description), 100) }}</td>
                                                    <td>
                                                        @if($specialty->is_active)
                                                            <span class="badge badge-success">Actif</span>
                                                        @else
                                                            <span class="badge badge-warning">Inactif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('esbtp.specialties.show', $specialty->id) }}" class="btn btn-info btn-sm">
                                                                <i class="fas fa-eye"></i> Voir
                                                            </a>
                                                            <a href="{{ route('esbtp.specialties.edit', $specialty->id) }}" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Modifier
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Aucune spécialité associée à ce cycle.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Onglet Années d'études -->
                        <div class="tab-pane" id="study-years">
                            <!-- Bouton pour ajouter une année d'études -->
                            <div class="mb-3">
                                <a href="{{ route('esbtp.study-years.create', ['cycle_id' => $cycle->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Ajouter une année d'études
                                </a>
                            </div>
                            
                            <!-- Liste des années d'études -->
                            @if($cycle->studyYears->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Niveau</th>
                                                <th>Nom</th>
                                                <th>Semestres</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cycle->studyYears as $studyYear)
                                                <tr>
                                                    <td>{{ $studyYear->level }}</td>
                                                    <td>{{ $studyYear->name }}</td>
                                                    <td>{{ $studyYear->semesters->count() }}</td>
                                                    <td>
                                                        @if($studyYear->is_active)
                                                            <span class="badge badge-success">Actif</span>
                                                        @else
                                                            <span class="badge badge-warning">Inactif</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('esbtp.study-years.show', $studyYear->id) }}" class="btn btn-info btn-sm">
                                                                <i class="fas fa-eye"></i> Voir
                                                            </a>
                                                            <a href="{{ route('esbtp.study-years.edit', $studyYear->id) }}" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Modifier
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Aucune année d'études associée à ce cycle.
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialiser DataTables
        $('.table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            }
        });
    });
</script>
@endsection 