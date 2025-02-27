@extends('layouts.app')

@section('title', 'Gestion des Partenariats')

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
                    <h3 class="card-title">Liste des Partenariats</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.partnerships.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Nouveau Partenariat
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <ul class="nav nav-tabs" id="partnershipTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab" aria-controls="active" aria-selected="true">
                                Actifs <span class="badge badge-success">{{ $activePartnerships->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="inactive-tab" data-toggle="tab" href="#inactive" role="tab" aria-controls="inactive" aria-selected="false">
                                Inactifs <span class="badge badge-warning">{{ $inactivePartnerships->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="archived-tab" data-toggle="tab" href="#archived" role="tab" aria-controls="archived" aria-selected="false">
                                Archivés <span class="badge badge-danger">{{ $archivedPartnerships->count() }}</span>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="partnershipTabsContent">
                        <!-- Partenariats actifs -->
                        <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Logo</th>
                                            <th>Nom</th>
                                            <th>Type</th>
                                            <th>Contact</th>
                                            <th>Départements</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activePartnerships as $partnership)
                                            <tr>
                                                <td>{{ $partnership->id }}</td>
                                                <td class="text-center">
                                                    @if($partnership->logo)
                                                        <img src="{{ asset('storage/' . $partnership->logo) }}" alt="{{ $partnership->name }}" class="img-thumbnail" style="max-height: 50px;">
                                                    @else
                                                        <i class="fas fa-handshake fa-2x text-muted"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $partnership->name }}</td>
                                                <td>{{ $partnership->type }}</td>
                                                <td>
                                                    @if($partnership->email)
                                                        <a href="mailto:{{ $partnership->email }}">{{ $partnership->email }}</a><br>
                                                    @endif
                                                    {{ $partnership->phone ?? 'Non défini' }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $partnership->departments->count() }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('esbtp.partnerships.show', $partnership->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <a href="{{ route('esbtp.partnerships.edit', $partnership->id) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('esbtp.partnerships.destroy', $partnership->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce partenariat?');">
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
                        
                        <!-- Partenariats inactifs -->
                        <div class="tab-pane fade" id="inactive" role="tabpanel" aria-labelledby="inactive-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Logo</th>
                                            <th>Nom</th>
                                            <th>Type</th>
                                            <th>Contact</th>
                                            <th>Départements</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inactivePartnerships as $partnership)
                                            <tr>
                                                <td>{{ $partnership->id }}</td>
                                                <td class="text-center">
                                                    @if($partnership->logo)
                                                        <img src="{{ asset('storage/' . $partnership->logo) }}" alt="{{ $partnership->name }}" class="img-thumbnail" style="max-height: 50px;">
                                                    @else
                                                        <i class="fas fa-handshake fa-2x text-muted"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $partnership->name }}</td>
                                                <td>{{ $partnership->type }}</td>
                                                <td>
                                                    @if($partnership->email)
                                                        <a href="mailto:{{ $partnership->email }}">{{ $partnership->email }}</a><br>
                                                    @endif
                                                    {{ $partnership->phone ?? 'Non défini' }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $partnership->departments->count() }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('esbtp.partnerships.show', $partnership->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <a href="{{ route('esbtp.partnerships.edit', $partnership->id) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form action="{{ route('esbtp.partnerships.destroy', $partnership->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce partenariat?');">
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
                        
                        <!-- Partenariats archivés -->
                        <div class="tab-pane fade" id="archived" role="tabpanel" aria-labelledby="archived-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Logo</th>
                                            <th>Nom</th>
                                            <th>Type</th>
                                            <th>Contact</th>
                                            <th>Départements</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($archivedPartnerships as $partnership)
                                            <tr>
                                                <td>{{ $partnership->id }}</td>
                                                <td class="text-center">
                                                    @if($partnership->logo)
                                                        <img src="{{ asset('storage/' . $partnership->logo) }}" alt="{{ $partnership->name }}" class="img-thumbnail" style="max-height: 50px;">
                                                    @else
                                                        <i class="fas fa-handshake fa-2x text-muted"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $partnership->name }}</td>
                                                <td>{{ $partnership->type }}</td>
                                                <td>
                                                    @if($partnership->email)
                                                        <a href="mailto:{{ $partnership->email }}">{{ $partnership->email }}</a><br>
                                                    @endif
                                                    {{ $partnership->phone ?? 'Non défini' }}
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $partnership->departments->count() }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('esbtp.partnerships.show', $partnership->id) }}" class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <form action="{{ route('esbtp.partnerships.restore', $partnership->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-trash-restore"></i> Restaurer
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('esbtp.partnerships.force-delete', $partnership->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce partenariat? Cette action est irréversible.');">
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
            localStorage.setItem('activePartnershipTab', $(e.target).attr('href'));
        });
        
        var activeTab = localStorage.getItem('activePartnershipTab');
        
        if (activeTab) {
            $('#partnershipTabs a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
@endsection 