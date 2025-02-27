@extends('layouts.app')

@section('title', 'Détails de la justification d\'absence')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la justification d'absence</h5>
                    <a href="{{ route('absences.justifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Informations sur l'absence</h6>
                            <p><strong>Date de l'absence :</strong> {{ $justification->attendance->date->format('d/m/Y') }}</p>
                            <p><strong>Classe :</strong> {{ $justification->attendance->class->name ?? 'Non spécifiée' }}</p>
                            <p><strong>Section :</strong> {{ $justification->attendance->section->name ?? 'Non spécifiée' }}</p>
                            <p>
                                <strong>Statut actuel :</strong> 
                                @if($justification->attendance->status === 'absent')
                                    <span class="badge bg-danger">Absent</span>
                                @elseif($justification->attendance->status === 'justified')
                                    <span class="badge bg-success">Justifiée</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($justification->attendance->status) }}</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Informations sur la justification</h6>
                            <p>
                                <strong>Statut de la justification :</strong> 
                                @if($justification->status === 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($justification->status === 'approved')
                                    <span class="badge bg-success">Approuvée</span>
                                @elseif($justification->status === 'rejected')
                                    <span class="badge bg-danger">Rejetée</span>
                                @endif
                            </p>
                            <p><strong>Date de soumission :</strong> {{ $justification->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Dernière mise à jour :</strong> {{ $justification->updated_at->format('d/m/Y H:i') }}</p>
                            @if($justification->document_path)
                                <p>
                                    <strong>Document justificatif :</strong> 
                                    <a href="{{ Storage::url($justification->document_path) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-file"></i> Voir le document
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mb-3">Raison de l'absence</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $justification->reason }}
                            </div>
                        </div>
                    </div>
                    
                    @if($justification->admin_comment)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3">Commentaire de l'administration</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $justification->admin_comment }}
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                        @if($justification->status === 'pending')
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="border-bottom pb-2 mb-3">Traiter cette justification</h6>
                                    <form action="{{ route('absences.justifications.process', $justification) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Décision</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="">Sélectionner une décision</option>
                                                <option value="approved">Approuver</option>
                                                <option value="rejected">Rejeter</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="admin_comment" class="form-label">Commentaire (optionnel)</label>
                                            <textarea class="form-control" id="admin_comment" name="admin_comment" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Valider</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 