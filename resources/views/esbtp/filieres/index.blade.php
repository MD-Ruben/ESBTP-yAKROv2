@extends('layouts.app')

@section('title', 'Liste des filières ESBTP')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filières ESBTP</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.filieres.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Nouvelle filière
                        </a>
                    </div>
                </div>
                <div class="card-body">
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
                    
                    @if($filieres->isEmpty())
                        <div class="alert alert-info">
                            Aucune filière n'a été créée. <a href="{{ route('esbtp.filieres.create') }}">Créer une filière</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Options</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filieres as $filiere)
                                        <tr>
                                            <td>{{ $filiere->code }}</td>
                                            <td>{{ $filiere->name }}</td>
                                            <td>{{ Str::limit($filiere->description, 100) }}</td>
                                            <td>
                                                @if($filiere->options->count() > 0)
                                                    <span class="badge badge-info">{{ $filiere->options->count() }} option(s)</span>
                                                    <ul class="mt-2">
                                                        @foreach($filiere->options as $option)
                                                            <li>{{ $option->name }} ({{ $option->code }})</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="badge badge-secondary">Aucune option</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($filiere->is_active)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('esbtp.filieres.show', $filiere) }}" class="btn btn-info btn-sm" title="Voir les détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('esbtp.filieres.edit', $filiere) }}" class="btn btn-primary btn-sm" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal{{ $filiere->id }}" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Modal de confirmation de suppression -->
                                                <div class="modal fade" id="deleteModal{{ $filiere->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $filiere->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel{{ $filiere->id }}">Confirmation de suppression</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Êtes-vous sûr de vouloir supprimer la filière <strong>{{ $filiere->name }}</strong> ?
                                                                <br><br>
                                                                <div class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle"></i> Cette action est irréversible.
                                                                    <ul class="mt-2">
                                                                        <li>Si cette filière a des options, vous ne pourrez pas la supprimer.</li>
                                                                        <li>Si des étudiants sont inscrits dans cette filière, vous ne pourrez pas la supprimer.</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                                <form action="{{ route('esbtp.filieres.destroy', $filiere) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 