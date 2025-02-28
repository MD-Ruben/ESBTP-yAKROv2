@extends('layouts.app')

@section('title', 'Détails de la filière : ' . $filiere->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails de la filière</h3>
                    <div class="card-tools">
                        <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
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
                    
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="m-0">{{ $filiere->name }} <small class="text-muted">({{ $filiere->code }})</small></h1>
                                <div>
                                    <a href="{{ route('esbtp.filieres.edit', $filiere) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-info-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Statut</span>
                                    <span class="info-box-number">
                                        @if($filiere->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-sitemap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Type</span>
                                    <span class="info-box-number">
                                        @if($filiere->parent)
                                            <span class="badge badge-info">Option de {{ $filiere->parent->name }}</span>
                                        @else
                                            <span class="badge badge-primary">Filière principale</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Description</h3>
                                </div>
                                <div class="card-body">
                                    @if($filiere->description)
                                        {{ $filiere->description }}
                                    @else
                                        <em>Aucune description disponible.</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(!$filiere->parent)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Options de la filière</h3>
                                        <div class="card-tools">
                                            <a href="{{ route('esbtp.filieres.create', ['parent_id' => $filiere->id]) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> Ajouter une option
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($filiere->options->isEmpty())
                                            <div class="alert alert-info">
                                                Cette filière n'a pas d'options.
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Nom</th>
                                                            <th>Description</th>
                                                            <th>Statut</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($filiere->options as $option)
                                                            <tr>
                                                                <td>{{ $option->code }}</td>
                                                                <td>{{ $option->name }}</td>
                                                                <td>{{ Str::limit($option->description, 100) }}</td>
                                                                <td>
                                                                    @if($option->is_active)
                                                                        <span class="badge badge-success">Active</span>
                                                                    @else
                                                                        <span class="badge badge-danger">Inactive</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <a href="{{ route('esbtp.filieres.show', $option) }}" class="btn btn-info btn-sm">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        <a href="{{ route('esbtp.filieres.edit', $option) }}" class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
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
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
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
@endsection 