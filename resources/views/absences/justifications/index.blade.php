@extends('layouts.app')

@section('title', 'Mes justifications d\'absence')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes justifications d'absence</h5>
                    <a href="{{ route('absences.justifications.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle justification
                    </a>
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
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date d'absence</th>
                                    <th>Raison</th>
                                    <th>Document</th>
                                    <th>Statut</th>
                                    <th>Commentaire admin</th>
                                    <th>Date de soumission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($justifications as $justification)
                                    <tr>
                                        <td>{{ $justification->attendance->date->format('d/m/Y') }}</td>
                                        <td>{{ Str::limit($justification->reason, 50) }}</td>
                                        <td>
                                            @if($justification->document_path)
                                                <a href="{{ Storage::url($justification->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file"></i> Voir
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">Aucun</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($justification->status === 'pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @elseif($justification->status === 'approved')
                                                <span class="badge bg-success">Approuvée</span>
                                            @elseif($justification->status === 'rejected')
                                                <span class="badge bg-danger">Rejetée</span>
                                            @endif
                                        </td>
                                        <td>{{ $justification->admin_comment ?? 'Aucun commentaire' }}</td>
                                        <td>{{ $justification->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('absences.justifications.show', $justification) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($justification->status === 'pending')
                                                <form action="{{ route('absences.justifications.destroy', $justification) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette justification ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucune justification d'absence trouvée</td>
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