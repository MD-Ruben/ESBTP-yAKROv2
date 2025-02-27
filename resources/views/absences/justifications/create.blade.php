@extends('layouts.app')

@section('title', 'Soumettre une justification d\'absence')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Soumettre une justification d'absence</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if($absences->isEmpty())
                        <div class="alert alert-info">
                            Vous n'avez aucune absence non justifiée à ce jour.
                        </div>
                    @else
                        <form action="{{ route('absences.justifications.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="attendance_id" class="form-label">Sélectionner l'absence à justifier</label>
                                <select class="form-select @error('attendance_id') is-invalid @enderror" id="attendance_id" name="attendance_id" required>
                                    <option value="">Sélectionner une absence</option>
                                    @foreach($absences as $absence)
                                        <option value="{{ $absence->id }}" {{ old('attendance_id') == $absence->id ? 'selected' : '' }}>
                                            {{ $absence->date->format('d/m/Y') }} - 
                                            {{ $absence->class->name ?? 'Classe inconnue' }} 
                                            {{ $absence->section->name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('attendance_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="reason" class="form-label">Raison de l'absence</label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Veuillez expliquer brièvement la raison de votre absence.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="document" class="form-label">Document justificatif (optionnel)</label>
                                <input type="file" class="form-control @error('document') is-invalid @enderror" id="document" name="document">
                                @error('document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Formats acceptés : JPG, PNG, PDF. Taille maximale : 2 Mo.</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('absences.justifications.index') }}" class="btn btn-secondary">Annuler</a>
                                <button type="submit" class="btn btn-primary">Soumettre la justification</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 