@extends('layouts.app')

@section('title', 'Détails de l\'absence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Détails de l'absence</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent.student.show', $etudiant->id) }}">{{ $etudiant->nom }} {{ $etudiant->prenoms }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent.absences.index', $etudiant->id) }}">Absences</a></li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start mb-4">
                        <div class="flex-grow-1">
                            <h5 class="card-title">Informations sur l'absence</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('parent.absences.index', $etudiant->id) }}" class="btn btn-primary">
                                    <i class="mdi mdi-arrow-left me-1"></i> Retour
                                </a>
                                @if(!$absence->justifie)
                                <a href="{{ route('parent.absences.edit', ['etudiant_id' => $etudiant->id, 'absence_id' => $absence->id]) }}" class="btn btn-info">
                                    <i class="mdi mdi-pencil me-1"></i> Justifier
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-primary">Date de l'absence</h5>
                                <p class="text-muted">{{ $absence->date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-primary">Statut</h5>
                                <p>
                                    @if($absence->justifie)
                                    <span class="badge bg-success">Justifiée</span>
                                    @else
                                    <span class="badge bg-danger">Non justifiée</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-primary">Cours</h5>
                                <p class="text-muted">
                                    @if($absence->cours)
                                        {{ $absence->cours->matiere->nom }}
                                    @else
                                        Non spécifié
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-primary">Horaire</h5>
                                <p class="text-muted">
                                    @if($absence->heure_debut && $absence->heure_fin)
                                        {{ $absence->heure_debut->format('H:i') }} - {{ $absence->heure_fin->format('H:i') }}
                                        ({{ $absence->duree_heures }} heures)
                                    @else
                                        Journée complète
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($absence->justifie)
                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-primary">Motif de l'absence</h5>
                                <p class="text-muted">{{ $absence->motif ?: 'Non spécifié' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="text-primary">Date de justification</h5>
                                <p class="text-muted">{{ $absence->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <h5 class="text-primary">Commentaire</h5>
                                <p class="text-muted">{{ $absence->commentaire ?: 'Aucun commentaire' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($absence->document_justificatif)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">Document justificatif</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm">
                                                <span class="avatar-title rounded-circle bg-primary">
                                                    <i class="mdi mdi-file-document-outline"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-14 mb-1">Document</h5>
                                            <small class="text-muted">Télécharger le document</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <a href="{{ Storage::url($absence->document_justificatif) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="mdi mdi-download me-1"></i> Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Informations sur l'étudiant</h5>
                    <div class="text-center">
                        <div class="avatar-xl mx-auto mb-4">
                            @if($etudiant->photo)
                            <img src="{{ Storage::url($etudiant->photo) }}" alt="{{ $etudiant->nom }} {{ $etudiant->prenoms }}" class="img-thumbnail rounded-circle">
                            @else
                            <span class="avatar-title rounded-circle bg-primary text-white" style="font-size: 32px;">
                                {{ substr($etudiant->prenoms, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}
                            </span>
                            @endif
                        </div>
                        <h5>{{ $etudiant->nom }} {{ $etudiant->prenoms }}</h5>
                        <p class="text-muted">
                            @if($etudiant->classe_active)
                                {{ $etudiant->classe_active->filiere->nom }} - {{ $etudiant->classe_active->niveau->nom }}
                            @else
                                Classe non définie
                            @endif
                        </p>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Matricule</h6>
                                <p class="font-size-15">{{ $etudiant->matricule }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Sexe</h6>
                                <p class="font-size-15">{{ $etudiant->sexe === 'M' ? 'Masculin' : 'Féminin' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Téléphone</h6>
                                <p class="font-size-15">{{ $etudiant->telephone ?: 'Non renseigné' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted">Email</h6>
                                <p class="font-size-15">{{ $etudiant->email ?: 'Non renseigné' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('parent.student.show', $etudiant->id) }}" class="btn btn-primary">
                            Voir le profil complet
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Statistiques des absences</h5>
                    
                    @php
                        $totalAbsences = \App\Models\ESBTPAbsence::where('etudiant_id', $etudiant->id)->count();
                        $justifiedAbsences = \App\Models\ESBTPAbsence::where('etudiant_id', $etudiant->id)
                            ->where('justifie', true)
                            ->count();
                        $unjustifiedAbsences = $totalAbsences - $justifiedAbsences;
                        
                        // Calculer le taux de présence
                        $totalDays = \App\Models\ESBTPAbsence::where('etudiant_id', $etudiant->id)
                            ->distinct('date')
                            ->count('date');
                        
                        $presentDays = $totalDays > 0 ? $totalDays - $unjustifiedAbsences : 0;
                        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 100;
                    @endphp
                    
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="me-2">Taux de présence:</h5>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendanceRate }}%;" aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="ms-2">{{ $attendanceRate }}%</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <h5>Total</h5>
                                <h3 class="text-primary">{{ $totalAbsences }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center mb-3">
                                <h5>Justifiées</h5>
                                <h3 class="text-success">{{ $justifiedAbsences }}</h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="{{ route('parent.absences.index', $etudiant->id) }}" class="btn btn-info btn-sm">
                            Voir toutes les absences
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 