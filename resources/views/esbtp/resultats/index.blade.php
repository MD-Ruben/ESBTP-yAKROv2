@extends('layouts.app')

@section('title', 'Résultats des étudiants - ESBTP-yAKRO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Résultats des étudiants</h5>
                    <div>
                        <a href="{{ route('esbtp.bulletins.select') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour à la sélection
                        </a>
                    </div>
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

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('esbtp.resultats.index') }}" method="GET" class="row">
                                <div class="col-md-4 mb-2">
                                    <label for="classe_id">Classe :</label>
                                    <select class="form-select" id="classe_id" name="classe_id">
                                        <option value="">Sélectionnez une classe</option>
                                        @foreach($classes ?? [] as $classeItem)
                                            <option value="{{ $classeItem->id }}" {{ isset($classe_id) && $classe_id == $classeItem->id ? 'selected' : '' }}>
                                                {{ $classeItem->name }} ({{ $classeItem->filiere->name ?? 'N/A' }} - {{ $classeItem->niveau->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="annee_universitaire_id">Année Universitaire :</label>
                                    <select class="form-select" id="annee_universitaire_id" name="annee_universitaire_id">
                                        <option value="">Sélectionnez une année</option>
                                        @foreach($anneesUniversitaires ?? [] as $annee)
                                            <option value="{{ $annee->id }}" {{ isset($annee_id) && $annee_id == $annee->id ? 'selected' : '' }}>
                                                {{ $annee->annee_debut }}-{{ $annee->annee_fin }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="periode">Période :</label>
                                    <select class="form-select" id="periode" name="periode">
                                        <option value="semestre1" {{ isset($periode) && $periode == 'semestre1' ? 'selected' : '' }}>Semestre 1</option>
                                        <option value="semestre2" {{ isset($periode) && $periode == 'semestre2' ? 'selected' : '' }}>Semestre 2</option>
                                        <option value="annuel" {{ isset($periode) && $periode == 'annuel' ? 'selected' : '' }}>Annuel</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i> Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if(isset($etudiants) && $etudiants->count() > 0)
                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h4>Résultats : {{ $classe->name ?? 'Tous les étudiants' }} - {{ $periode == 'semestre1' ? 'Semestre 1' : ($periode == 'semestre2' ? 'Semestre 2' : 'Année complète') }}</h4>
                                <p>Année universitaire : {{ $anneeUniversitaire->annee_debut ?? '' }}-{{ $anneeUniversitaire->annee_fin ?? '' }}</p>
                            </div>
                            @if(isset($classe))
                            <div>
                                <a href="{{ route('esbtp.resultats.classe', ['classe' => $classe->id, 'annee_universitaire_id' => $annee_id, 'periode' => $periode]) }}" class="btn btn-primary">
                                    <i class="fas fa-chart-bar me-1"></i> Résultats détaillés de la classe
                                </a>
                            </div>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Nom et prénom</th>
                                        @if(!isset($classe))
                                        <th>Classe</th>
                                        @endif
                                        <th>Moyenne</th>
                                        <th>Rang</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etudiants as $etudiant)
                                        <tr>
                                            <td>{{ $etudiant->matricule }}</td>
                                            <td>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</td>
                                            @if(!isset($classe))
                                            <td>
                                                @php
                                                    $inscription = $etudiant->inscriptions->where('annee_universitaire_id', $annee_id)->first();
                                                    $etudiantClasse = $inscription ? $inscription->classe : null;
                                                @endphp
                                                {{ $etudiantClasse ? $etudiantClasse->name : 'N/A' }}
                                            </td>
                                            @endif
                                            <td>
                                                @if(isset($moyennes[$etudiant->id]))
                                                    <span class="badge bg-{{ $moyennes[$etudiant->id] >= 10 ? 'success' : 'danger' }}">
                                                        {{ number_format($moyennes[$etudiant->id], 2) }}/20
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($rangs[$etudiant->id]))
                                                    {{ $rangs[$etudiant->id] }}<sup>{{ $rangs[$etudiant->id] == 1 ? 'er' : 'ème' }}</sup> / {{ count($rangs) }}
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $inscription = $etudiant->inscriptions->where('annee_universitaire_id', $annee_id)->first();
                                                    $studentClasseId = $inscription ? $inscription->classe_id : null;
                                                    $actualClasseId = $classe_id ?? $studentClasseId;
                                                @endphp
                                                <a href="{{ route('esbtp.resultats.etudiant', ['etudiant' => $etudiant->id, 'classe_id' => $actualClasseId, 'annee_universitaire_id' => $annee_id, 'periode' => $periode]) }}" class="btn btn-sm btn-info me-1">
                                                    <i class="fas fa-chart-line me-1"></i>Détails
                                                </a>
                                                @if(isset($bulletins[$etudiant->id]))
                                                    <a href="{{ route('esbtp.bulletins.show', $bulletins[$etudiant->id]) }}" class="btn btn-sm btn-secondary me-1">
                                                        <i class="fas fa-eye me-1"></i>Bulletin
                                                    </a>
                                                    <a href="{{ route('esbtp.bulletins.pdf', $bulletins[$etudiant->id]) }}" class="btn btn-sm btn-danger" target="_blank">
                                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                                    </a>
                                                @else
                                                    <span class="badge bg-warning">Bulletin non généré</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(isset($notes) && $notes->isEmpty())
                            <div class="alert alert-warning">
                                Aucune note trouvée. Vérifiez que :
                                <ul>
                                    <li>Les évaluations sont bien créées pour cette période</li>
                                    <li>Les notes sont saisies et liées aux évaluations</li>
                                    <li>Les coefficients des évaluations sont > 0</li>
                                </ul>
                            </div>
                        @endif
                    @elseif(isset($classe))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun étudiant trouvé pour cette classe et cette période.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Veuillez sélectionner une classe, une année universitaire et une période pour afficher les résultats.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.form-select').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection
