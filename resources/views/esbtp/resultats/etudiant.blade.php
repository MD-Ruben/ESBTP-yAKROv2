@extends('layouts.app')

@section('title', 'Résultats de ' . $etudiant->nom . ' ' . $etudiant->prenoms)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Résultats de {{ $etudiant->nom }} {{ $etudiant->prenoms }}</h5>
                    <a href="{{ route('esbtp.resultats.classe', $classe) }}?periode={{ $periode }}&annee_universitaire_id={{ $annee_id }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la classe
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

                    <!-- Filtres -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('esbtp.resultats.etudiant', $etudiant) }}" method="GET" class="row">
                                <div class="col-md-3 mb-2">
                                    <label for="classe_id">Classe :</label>
                                    <select class="form-select" id="classe_id" name="classe_id">
                                        @foreach($classes ?? [] as $c)
                                            <option value="{{ $c->id }}" {{ isset($classe_id) && $classe_id == $c->id ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="annee_universitaire_id">Année Universitaire :</label>
                                    <select class="form-select" id="annee_universitaire_id" name="annee_universitaire_id">
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
                                        @foreach($periodes ?? [] as $p)
                                            <option value="{{ $p->id }}" {{ isset($periode) && $periode == $p->id ? 'selected' : '' }}>
                                                {{ $p->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0">Informations de l'étudiant</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Matricule:</strong> {{ $etudiant->matricule }}</p>
                                    <p class="mb-1"><strong>Nom:</strong> {{ $etudiant->nom }}</p>
                                    <p class="mb-1"><strong>Prénom:</strong> {{ $etudiant->prenoms }}</p>
                                    <p class="mb-1"><strong>Classe:</strong> {{ $classe->name }}</p>
                                    <p class="mb-1"><strong>Filière:</strong> {{ $classe->filiere->name ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Niveau:</strong> {{ $classe->niveau->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header {{ $moyenneGenerale >= 10 ? 'bg-success' : 'bg-danger' }} text-white">
                                    <h6 class="card-title mb-0">Résultats {{ $periodes[array_search($periode, array_column($periodes, 'id'))]->nom ?? $periode }}</h6>
                                </div>
                                <div class="card-body">
                                    <h3 class="text-center mb-3 {{ $moyenneGenerale >= 10 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($moyenneGenerale, 2) }}/20
                                    </h3>
                                    <p class="mb-1"><strong>Nombre de matières:</strong> {{ count($notesByMatiere) }}</p>
                                    <p class="mb-1"><strong>Nombre d'évaluations:</strong> {{ $notes->count() }}</p>
                                    <p class="mb-0"><strong>Décision:</strong>
                                        <span class="badge {{ $moyenneGenerale >= 10 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $moyenneGenerale >= 10 ? 'Admis' : 'Non admis' }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résultats par matière -->
                    <h5 class="my-3">Résultats par matière</h5>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="bg-light">
                                    <th style="width: 5%">#</th>
                                    <th style="width: 30%">Matière</th>
                                    <th style="width: 15%">Nombre d'évaluations</th>
                                    <th style="width: 15%">Coefficient total</th>
                                    <th style="width: 15%">Moyenne</th>
                                    <th style="width: 20%">Observations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($notesByMatiere as $matiere_id => $matiereData)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $matiereData['matiere']->name }}</td>
                                        <td>{{ count($matiereData['notes']) }}</td>
                                        <td>{{ $matiereData['total_coefficients'] }}</td>
                                        <td class="{{ $matiereData['moyenne'] >= 10 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                            {{ number_format($matiereData['moyenne'], 2) }}/20
                                        </td>
                                        <td>
                                            @if($matiereData['moyenne'] >= 16)
                                                <span class="badge bg-success">Excellent</span>
                                            @elseif($matiereData['moyenne'] >= 14)
                                                <span class="badge bg-primary">Très bien</span>
                                            @elseif($matiereData['moyenne'] >= 12)
                                                <span class="badge bg-info">Bien</span>
                                            @elseif($matiereData['moyenne'] >= 10)
                                                <span class="badge bg-warning text-dark">Passable</span>
                                            @else
                                                <span class="badge bg-danger">Insuffisant</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune note trouvée pour cet étudiant</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @if(count($notesByMatiere) > 0)
                                    <tr class="bg-light">
                                        <th colspan="3" class="text-end">Total des coefficients :</th>
                                        <th>{{ array_sum(array_column($notesByMatiere, 'total_coefficients')) }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                    <tr class="bg-info text-white">
                                        <th colspan="4" class="text-end">Moyenne générale :</th>
                                        <th colspan="2">{{ number_format($moyenneGenerale, 2) }}/20</th>
                                    </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>

                    <!-- Détail des notes par matière -->
                    <h5 class="my-3">Détail des évaluations</h5>

                    @forelse($notesByMatiere as $matiere_id => $matiereData)
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">{{ $matiereData['matiere']->name }} - Moyenne: {{ number_format($matiereData['moyenne'], 2) }}/20</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%">Évaluation</th>
                                                <th style="width: 15%">Type</th>
                                                <th style="width: 15%">Date</th>
                                                <th style="width: 10%">Coefficient</th>
                                                <th style="width: 10%">Note</th>
                                                <th style="width: 15%">Note pondérée</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($matiereData['notes'] as $note)
                                                <tr>
                                                    <td>{{ $note->evaluation->title }}</td>
                                                    <td>{{ $note->evaluation->type }}</td>
                                                    <td>{{ $note->evaluation->date ? $note->evaluation->date->format('d/m/Y') : 'N/A' }}</td>
                                                    <td>{{ $note->evaluation->coefficient }}</td>
                                                    <td class="{{ $note->valeur >= 10 ? 'text-success' : 'text-danger' }}">
                                                        <strong>{{ $note->valeur }}/20</strong>
                                                    </td>
                                                    <td>{{ $note->valeur * $note->evaluation->coefficient }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            Aucune note trouvée pour cet étudiant dans cette classe pour cette période.
                        </div>
                    @endforelse

                    <!-- Boutons d'action -->
                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('esbtp.resultats.classe', $classe) }}?periode={{ $periode }}&annee_universitaire_id={{ $annee_id }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux résultats de la classe
                        </a>

                        <a href="{{ route('esbtp.bulletins.generate', ['etudiant_id' => $etudiant->id, 'classe_id' => $classe->id, 'periode' => $periode, 'annee_universitaire_id' => $annee_id]) }}"
                           class="btn btn-primary">
                            <i class="fas fa-file-pdf me-1"></i>Générer bulletin PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
