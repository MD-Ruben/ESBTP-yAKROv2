@extends('layouts.app')

@section('title', 'Attacher des matières à une classe - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <!-- Titre de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Attacher des matières à une classe</h1>
        <a href="{{ route('esbtp.matieres.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <!-- Affichage des messages de notification -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Card principal -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Attacher des matières à une classe</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('esbtp.matieres.attach-to-classe') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="classe_id">Classe <span class="text-danger">*</span></label>
                    <select id="classe_id" name="classe_id" class="form-control select2 @error('classe_id') is-invalid @enderror" required>
                        <option value="">Sélectionner une classe</option>
                        @foreach(\App\Models\ESBTPClasse::with(['filiere', 'niveau', 'annee'])->orderBy('name')->get() as $classe)
                            <option value="{{ $classe->id }}" {{ old('classe_id') == $classe->id ? 'selected' : '' }}>
                                {{ $classe->name }} - {{ optional($classe->filiere)->nom }} / {{ optional($classe->niveau)->nom }} ({{ optional($classe->annee)->nom }})
                            </option>
                        @endforeach
                    </select>
                    @error('classe_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="matieres">Matières à attacher <span class="text-danger">*</span></label>
                    <select id="matieres" name="matieres[]" class="form-control select2 @error('matieres') is-invalid @enderror" multiple required>
                        @foreach(\App\Models\ESBTPMatiere::where('is_active', true)->orderBy('nom')->get() as $matiere)
                            <option value="{{ $matiere->id }}" {{ (is_array(old('matieres')) && in_array($matiere->id, old('matieres'))) ? 'selected' : '' }}>
                                {{ $matiere->nom ?? $matiere->name }} ({{ $matiere->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('matieres')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Vous pouvez sélectionner plusieurs matières en maintenant la touche Ctrl (ou Cmd sur Mac).</small>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Attacher les matières
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection 