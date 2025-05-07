@extends('layouts.app')

@section('title', 'Détails de l\'Enseignant')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Détails de l'Enseignant</h1>
    <p class="mb-4">Informations complètes sur l'enseignant.</p>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Tableau de bord</a></li>
            <li class="breadcrumb-item"><a href="{{ route('superadmin.teachers.index') }}">Enseignants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails</li>
        </ol>
    </nav>

    <!-- Message de succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4">
            <!-- Informations personnelles -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($teacher->profile_picture)
                            <img src="{{ asset('storage/' . $teacher->profile_picture) }}" class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="{{ asset('img/undraw_profile.svg') }}" class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                        <h5 class="mt-3 font-weight-bold">{{ $teacher->user->name }}</h5>
                        <p class="text-muted">{{ $teacher->designation->name ?? 'Non défini' }}</p>
                    </div>

                    <div class="profile-info">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-weight-bold">ID Employé:</span>
                            <span>{{ $teacher->employee_id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-weight-bold">Email:</span>
                            <span>{{ $teacher->user->email }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-weight-bold">Téléphone:</span>
                            <span>{{ $teacher->phone }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-weight-bold">Genre:</span>
                            <span>
                                @if($teacher->gender == 'male')
                                    Masculin
                                @elseif($teacher->gender == 'female')
                                    Féminin
                                @elseif($teacher->gender == 'other')
                                    Autre
                                @else
                                    Non défini
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-weight-bold">Date de naissance:</span>
                            <span>{{ $teacher->date_of_birth ? date('d/m/Y', strtotime($teacher->date_of_birth)) : 'Non défini' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="font-weight-bold">Adresse:</span>
                            <span>{{ $teacher->address ?: 'Non défini' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Informations professionnelles -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations professionnelles</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Département:</span>
                                <span>{{ $teacher->department->name ?? 'Non défini' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Fonction:</span>
                                <span>{{ $teacher->designation->name ?? 'Non défini' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Date d'entrée en fonction:</span>
                                <span>{{ $teacher->join_date ? date('d/m/Y', strtotime($teacher->join_date)) : 'Non défini' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Qualification:</span>
                                <span>{{ $teacher->qualification ?: 'Non défini' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Expérience:</span>
                                <span>{{ $teacher->experience ? $teacher->experience . ' ans' : 'Non défini' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="font-weight-bold">Statut:</span>
                                <span>
                                    @if($teacher->status == 'active')
                                        <span class="badge badge-success">Actif</span>
                                    @else
                                        <span class="badge badge-danger">Inactif</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes et matières enseignées -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Classes et matières enseignées</h6>
                </div>
                <div class="card-body">
                    @if($teacher->subjects && $teacher->subjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Classe</th>
                                        <th>Matière</th>
                                        <th>Année scolaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teacher->subjects as $subject)
                                        <tr>
                                            <td>{{ $subject->class->name ?? 'Non défini' }}</td>
                                            <td>{{ $subject->name }}</td>
                                            <td>{{ $subject->school_year ?? date('Y') . '-' . (date('Y') + 1) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">Aucune matière assignée à cet enseignant pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Laboratoires et projets de recherche -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Laboratoires et projets de recherche</h6>
                </div>
                <div class="card-body">
                    @if($teacher->laboratories && $teacher->laboratories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Laboratoire</th>
                                        <th>Position</th>
                                        <th>Date de début</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teacher->laboratories as $laboratory)
                                        <tr>
                                            <td>{{ $laboratory->name }}</td>
                                            <td>{{ $laboratory->pivot->position ?? 'Chercheur' }}</td>
                                            <td>{{ $laboratory->pivot->joined_at ? date('d/m/Y', strtotime($laboratory->pivot->joined_at)) : 'Non défini' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">Aucun laboratoire ou projet de recherche associé à cet enseignant.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between">
                <a href="{{ route('superadmin.teachers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <div>
                    <a href="{{ route('superadmin.teachers.edit', $teacher->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteTeacherModal">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteTeacherModal" tabindex="-1" role="dialog" aria-labelledby="deleteTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTeacherModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet enseignant? Cette action est irréversible.</p>
                <p class="font-weight-bold">{{ $teacher->user->name }} ({{ $teacher->employee_id }})</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('superadmin.teachers.destroy', $teacher->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 