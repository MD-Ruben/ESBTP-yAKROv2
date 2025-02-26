@extends('layouts.app')

@section('title', 'Confirmer la Suppression')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la Suppression
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Attention :</strong> Vous êtes sur le point de supprimer définitivement cet étudiant. Cette action est irréversible.
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            @if($student->profile_image)
                                <img src="{{ asset('storage/' . $student->profile_image) }}" alt="Photo de profil" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; margin: 0 auto;">
                                    <i class="fas fa-user fa-4x text-secondary"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4 class="text-danger">{{ $student->user->name ?? 'N/A' }}</h4>
                            <p class="mb-1"><strong>Matricule :</strong> {{ $student->registration_number ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Classe :</strong> {{ $student->class->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Email :</strong> {{ $student->user->email ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Date d'admission :</strong> {{ $student->admission_date ? date('d/m/Y', strtotime($student->admission_date)) : 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Conséquences de la suppression :</h6>
                        <ul class="mb-0">
                            <li>Toutes les données personnelles de l'étudiant seront supprimées</li>
                            <li>L'historique des présences sera supprimé</li>
                            <li>Les notes et résultats scolaires seront supprimés</li>
                            <li>Les certificats et documents associés seront supprimés</li>
                            <li>Le compte utilisateur associé sera désactivé</li>
                        </ul>
                    </div>
                    
                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="confirm_delete" name="confirm_delete" required>
                            <label class="form-check-label" for="confirm_delete">
                                Je confirme vouloir supprimer définitivement cet étudiant et toutes ses données associées.
                            </label>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-danger" id="delete_button" disabled>
                                <i class="fas fa-trash-alt me-1"></i> Supprimer définitivement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Activer/désactiver le bouton de suppression en fonction de la case à cocher
    document.addEventListener('DOMContentLoaded', function() {
        const confirmCheckbox = document.getElementById('confirm_delete');
        const deleteButton = document.getElementById('delete_button');
        
        confirmCheckbox.addEventListener('change', function() {
            deleteButton.disabled = !this.checked;
        });
    });
</script>
@endpush 