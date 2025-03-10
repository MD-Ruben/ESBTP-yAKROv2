@extends('layouts.app')

@section('title', 'Ajouter une filière - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ajouter une nouvelle filière</h5>
                    <a href="{{ route('esbtp.filieres.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('esbtp.filieres.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom de la filière *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ex: Génie Civil, Mine - Géologie - Pétrole, etc.</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Code de la filière *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Ex: GC, MGP, etc.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="option_filiere" class="form-label">Option de la filière</label>
                                    <input type="text" class="form-control @error('option_filiere') is-invalid @enderror" id="option_filiere" name="option_filiere" value="{{ old('option_filiere') }}">
                                    @error('option_filiere')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Indiquez si cette filière est une option (ex: BATIMENT est une option de Génie Civil)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niveau_etude_ids" class="form-label">Niveaux d'études associés</label>
                                    <select class="form-select select2-niveaux @error('niveau_etude_ids') is-invalid @enderror" id="niveau_etude_ids" name="niveau_etude_ids[]" multiple>
                                        @foreach($niveauxEtudes as $niveau)
                                            <option value="{{ $niveau->id }}" {{ in_array($niveau->id, old('niveau_etude_ids', [])) ? 'selected' : '' }}>
                                                {{ $niveau->name }} ({{ $niveau->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('niveau_etude_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez les niveaux d'études associés à cette filière (BTS 1ère année, BTS 2ème année, etc.)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Une brève description de la filière.</small>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Filière active
                            </label>
                            <div class="form-text">
                                <span class="text-info"><i class="fas fa-info-circle me-1"></i>Info :</span>
                                Une filière inactive ne sera pas disponible lors de la création de classes.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Configuration améliorée pour la sélection des niveaux d'études
        $('.select2-niveaux').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionnez les niveaux d\'études',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatNiveau,
            templateSelection: formatNiveauSelection,
            dropdownCssClass: 'select2-dropdown-niveaux'
        });

        function formatNiveau(niveau) {
            if (!niveau.id) {
                return niveau.text;
            }

            var $niveau = $(
                '<div class="select2-niveau-item">' +
                    '<div class="niveau-icon"><i class="fas fa-graduation-cap"></i></div>' +
                    '<div class="niveau-info">' +
                        '<div class="niveau-name">' + niveau.text.split('(')[0] + '</div>' +
                        '<div class="niveau-code">' + (niveau.text.split('(')[1] ? '(' + niveau.text.split('(')[1] : '') + '</div>' +
                    '</div>' +
                '</div>'
            );

            return $niveau;
        }

        function formatNiveauSelection(niveau) {
            return niveau.text;
        }

        // Ajout de styles personnalisés pour les niveaux d'études
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .select2-dropdown-niveaux {
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                }
                .select2-niveau-item {
                    display: flex;
                    align-items: center;
                    padding: 8px 0;
                }
                .niveau-icon {
                    width: 30px;
                    height: 30px;
                    background-color: #f8f9fa;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-right: 10px;
                    color: #0d6efd;
                }
                .niveau-info {
                    display: flex;
                    flex-direction: column;
                }
                .niveau-name {
                    font-weight: 500;
                }
                .niveau-code {
                    font-size: 0.8em;
                    color: #6c757d;
                }
                .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
                    background-color: #0d6efd;
                    color: white;
                    border: none;
                    padding: 5px 10px;
                    border-radius: 20px;
                }
                .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
                    color: white;
                    margin-right: 5px;
                }
            `)
            .appendTo('head');

        // Génération automatique du code basé sur le nom
        $('#name').on('blur', function() {
            if ($('#code').val() === '') {
                // Prendre les premières lettres de chaque mot
                let words = $(this).val().split(' ');
                let code = '';

                words.forEach(function(word) {
                    if (word.length > 0) {
                        code += word.charAt(0).toUpperCase();
                    }
                });

                // Si le code est trop court, ajouter d'autres lettres
                if (code.length < 2 && words[0] && words[0].length > 1) {
                    code += words[0].charAt(1).toUpperCase();
                }

                $('#code').val(code);
            }
        });
    });
</script>
@endsection
