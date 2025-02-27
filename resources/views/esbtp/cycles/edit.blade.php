@extends('layouts.app')

@section('title', 'Modifier un Cycle de Formation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Cycle: {{ $cycle->name }}</h3>
                </div>
                <!-- /.card-header -->
                
                <!-- form start -->
                <form method="POST" action="{{ route('esbtp.cycles.update', $cycle->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Nom du cycle -->
                                <div class="form-group">
                                    <label for="name">Nom du cycle <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $cycle->name) }}" required placeholder="Entrez le nom du cycle (ex: Cycle Ingénieurs)">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <!-- Code du cycle -->
                                <div class="form-group">
                                    <label for="code">Code du cycle <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $cycle->code) }}" required placeholder="Entrez le code du cycle (ex: ING, TSP)">
                                    @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Durée du cycle -->
                                <div class="form-group">
                                    <label for="duration_years">Durée (en années) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration_years') is-invalid @enderror" id="duration_years" name="duration_years" value="{{ old('duration_years', $cycle->duration_years) }}" required min="1" max="10" placeholder="Durée du cycle en années">
                                    @error('duration_years')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <!-- Diplôme délivré -->
                                <div class="form-group">
                                    <label for="diploma_awarded">Diplôme délivré <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('diploma_awarded') is-invalid @enderror" id="diploma_awarded" name="diploma_awarded" value="{{ old('diploma_awarded', $cycle->diploma_awarded) }}" required placeholder="Diplôme délivré à la fin du cycle">
                                    @error('diploma_awarded')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <!-- Statut actif/inactif -->
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $cycle->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Cycle actif</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description du cycle -->
                        <div class="form-group">
                            <label for="description">Description du cycle</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Description détaillée du cycle de formation">{{ old('description', $cycle->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Mettre à jour</button>
                        <a href="{{ route('esbtp.cycles.show', $cycle->id) }}" class="btn btn-default">Annuler</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        // Initialiser l'éditeur de texte pour la description
        $('#description').summernote({
            height: 200,
            placeholder: 'Rédigez une description détaillée du cycle de formation ici...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            lang: 'fr-FR'
        });
    });
</script>
@endsection 