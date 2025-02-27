@extends('layouts.app')

@section('title', 'Gestion des Spécialités')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Alertes de session -->
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

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des Spécialités</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.specialties.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nouvelle Spécialité
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <ul class="nav nav-tabs" id="specialtyTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="true">
                                Actives <span class="badge badge-success">{{ $activeSpecialties->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="inactive-tab" data-toggle="tab" href="#inactive" role="tab" aria-controls="inactive" aria-selected="false">
                                Inactives <span class="badge badge-warning">{{ $inactiveSpecialties->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="archived-tab" data-toggle="tab" href="#archived" role="tab" aria-controls="archived" aria-selected="false">
                                Archivées <span class="badge badge-danger">{{ $archivedSpecialties->count() }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="specialtyTabsContent">
                        <!-- Spécialités actives -->
                        <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Code</th>
                                            <th>Cycle</th>
                                            <th>Département</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeSpecialties as $specialty)
                                            <tr>
                                                <td>{{ $specialty->id }}</td>
                                                <td>{{ $specialty->name }}</td>
                                                <td>{{ $specialty->code }}</td>
                                                <td>{{ $specialty->cycle->name ?? 'Non défini' }}</td>
                                                <td>{{ $specialty->department->name ?? 'Non défini' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('esbtp.specialties.show', $specialty->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <a href="{{ route('esbtp.specialties.edit', $specialty->id) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('esbtp.specialties.destroy', $specialty->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver cette spécialité?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i> Archiver
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Spécialités inactives -->
                        <div class="tab-pane fade" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Code</th>
                                            <th>Cycle</th>
                                            <th>Département</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inactiveSpecialties as $specialty)
                                            <tr>
                                                <td>{{ $specialty->id }}</td>
                                                <td>{{ $specialty->name }}</td>
                                                <td>{{ $specialty->code }}</td>
                                                <td>{{ $specialty->cycle->name ?? 'Non défini' }}</td>
                                                <td>{{ $specialty->department->name ?? 'Non défini' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('esbtp.specialties.show', $specialty->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <a href="{{ route('esbtp.specialties.edit', $specialty->id) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('esbtp.specialties.destroy', $specialty->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver cette spécialité?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash"></i> Archiver
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Spécialités archivées -->
                        <div class="tab-pane fade" id="archived" role="tabpanel" aria-labelledby="archived-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Code</th>
                                            <th>Cycle</th>
                                            <th>Département</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($archivedSpecialties as $specialty)
                                            <tr>
                                                <td>{{ $specialty->id }}</td>
                                                <td>{{ $specialty->name }}</td>
                                                <td>{{ $specialty->code }}</td>
                                                <td>{{ $specialty->cycle->name ?? 'Non défini' }}</td>
                                                <td>{{ $specialty->department->name ?? 'Non défini' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('esbtp.specialties.show', $specialty->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <form action="{{ route('esbtp.specialties.restore', $specialty->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-trash-restore"></i> Restaurer
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('esbtp.specialties.force-delete', $specialty->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette spécialité? Cette action est irréversible.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-times-circle"></i> Supprimer
                                                            </button>
                                                        </form>
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
                <!-- /.card-body -->
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
        // Initialiser DataTables pour chaque tableau
        $('.datatable').DataTable({
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
        
        // Conserver l'onglet actif après rechargement de la page
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('activeSpecialtyTab', $(e.target).attr('href'));
        });
        
        var activeTab = localStorage.getItem('activeSpecialtyTab');
        
        if (activeTab) {
            $('#specialtyTabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
@endsection 