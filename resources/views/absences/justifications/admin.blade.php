@extends('layouts.app')

@section('title', 'Gestion des justifications d\'absence')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gestion des justifications d'absence</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <ul class="nav nav-tabs mb-3" id="justificationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                                En attente <span class="badge bg-warning">{{ $justifications->where('status', 'pending')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab" aria-controls="approved" aria-selected="false">
                                Approuvées <span class="badge bg-success">{{ $justifications->where('status', 'approved')->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                Rejetées <span class="badge bg-danger">{{ $justifications->where('status', 'rejected')->count() }}</span>
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="justificationTabsContent">
                        <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Étudiant</th>
                                            <th>Date d'absence</th>
                                            <th>Classe</th>
                                            <th>Raison</th>
                                            <th>Document</th>
                                            <th>Date de soumission</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($justifications->where('status', 'pending') as $justification)
                                            <tr>
                                                <td>{{ $justification->student->user->name }}</td>
                                                <td>{{ $justification->attendance->date->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ $justification->attendance->class->name ?? 'N/A' }}
                                                    {{ $justification->attendance->section->name ?? '' }}
                                                </td>
                                                <td>{{ Str::limit($justification->reason, 30) }}</td>
                                                <td>
                                                    @if($justification->document_path)
                                                        <a href="{{ Storage::url($justification->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file"></i> Voir
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Aucun</span>
                                                    @endif
                                                </td>
                                                <td>{{ $justification->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('absences.justifications.show', $justification) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Aucune justification en attente</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="approved" role="tabpanel" aria-labelledby="approved-tab">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Étudiant</th>
                                            <th>Date d'absence</th>
                                            <th>Classe</th>
                                            <th>Raison</th>
                                            <th>Document</th>
                                            <th>Date d'approbation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($justifications->where('status', 'approved') as $justification)
                                            <tr>
                                                <td>{{ $justification->student->user->name }}</td>
                                                <td>{{ $justification->attendance->date->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ $justification->attendance->class->name ?? 'N/A' }}
                                                    {{ $justification->attendance->section->name ?? '' }}
                                                </td>
                                                <td>{{ Str::limit($justification->reason, 30) }}</td>
                                                <td>
                                                    @if($justification->document_path)
                                                        <a href="{{ Storage::url($justification->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file"></i> Voir
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Aucun</span>
                                                    @endif
                                                </td>
                                                <td>{{ $justification->updated_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('absences.justifications.show', $justification) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Aucune justification approuvée</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Étudiant</th>
                                            <th>Date d'absence</th>
                                            <th>Classe</th>
                                            <th>Raison</th>
                                            <th>Document</th>
                                            <th>Date de rejet</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($justifications->where('status', 'rejected') as $justification)
                                            <tr>
                                                <td>{{ $justification->student->user->name }}</td>
                                                <td>{{ $justification->attendance->date->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ $justification->attendance->class->name ?? 'N/A' }}
                                                    {{ $justification->attendance->section->name ?? '' }}
                                                </td>
                                                <td>{{ Str::limit($justification->reason, 30) }}</td>
                                                <td>
                                                    @if($justification->document_path)
                                                        <a href="{{ Storage::url($justification->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-file"></i> Voir
                                                        </a>
                                                    @else
                                                        <span class="badge bg-secondary">Aucun</span>
                                                    @endif
                                                </td>
                                                <td>{{ $justification->updated_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('absences.justifications.show', $justification) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Détails
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Aucune justification rejetée</td>
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
    </div>
</div>
@endsection 