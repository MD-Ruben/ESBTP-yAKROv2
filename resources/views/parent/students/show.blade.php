@extends('layouts.app')

@section('title', $etudiant->nom . ' ' . $etudiant->prenom . ' | Détails étudiant')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-1">
                                    <li class="breadcrumb-item"><a href="{{ route('parent.dashboard') }}">Tableau de bord</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Détails étudiant</li>
                                </ol>
                            </nav>
                            <h1 class="fw-bold mb-0">{{ $etudiant->nom }} {{ $etudiant->prenom }}</h1>
                            <p class="text-muted">
                                Matricule: <span class="fw-medium">{{ $etudiant->matricule }}</span> | 
                                @if($inscriptionActive)
                                    Classe: <span class="fw-medium">{{ $inscriptionActive->classe->nom }}</span>
                                @else
                                    <span class="badge bg-warning">Non inscrit pour l'année en cours</span>
                                @endif
                            </p>
                        </div>
                        <div class="d-flex">
                            <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $etudiant->photo ? asset('storage/' . $etudiant->photo) : asset('images/default-avatar.png') }}" 
                             alt="{{ $etudiant->nom }} {{ $etudiant->prenom }}" 
                             class="rounded-circle img-thumbnail" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Nom complet:</span>
                            <span class="fw-medium">{{ $etudiant->nom }} {{ $etudiant->prenom }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Matricule:</span>
                            <span class="fw-medium">{{ $etudiant->matricule }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Date de naissance:</span>
                            <span class="fw-medium">{{ $etudiant->date_naissance ? \Carbon\Carbon::parse($etudiant->date_naissance)->format('d/m/Y') : 'Non définie' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Lieu de naissance:</span>
                            <span class="fw-medium">{{ $etudiant->lieu_naissance ?: 'Non défini' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Sexe:</span>
                            <span class="fw-medium">{{ $etudiant->sexe == 'M' ? 'Masculin' : 'Féminin' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Nationalité:</span>
                            <span class="fw-medium">{{ $etudiant->nationalite ?: 'Non définie' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Email:</span>
                            <span class="fw-medium">{{ $etudiant->email ?: 'Non défini' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Téléphone:</span>
                            <span class="fw-medium">{{ $etudiant->telephone ?: 'Non défini' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Adresse:</span>
                            <span class="fw-medium">{{ $etudiant->adresse ?: 'Non définie' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Onglets d'informations -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white p-0">
                    <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="inscriptions-tab" data-bs-toggle="tab" data-bs-target="#inscriptions" type="button" role="tab" aria-controls="inscriptions" aria-selected="true">
                                <i class="fas fa-user-plus me-1"></i>Inscriptions
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="false">
                                <i class="fas fa-clipboard-list me-1"></i>Notes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="absences-tab" data-bs-toggle="tab" data-bs-target="#absences" type="button" role="tab" aria-controls="absences" aria-selected="false">
                                <i class="fas fa-user-clock me-1"></i>Absences
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="paiements-tab" data-bs-toggle="tab" data-bs-target="#paiements" type="button" role="tab" aria-controls="paiements" aria-selected="false">
                                <i class="fas fa-money-bill-wave me-1"></i>Paiements
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="studentTabsContent">
                        <!-- Onglet Inscriptions -->
                        <div class="tab-pane fade show active" id="inscriptions" role="tabpanel" aria-labelledby="inscriptions-tab">
                            <h5 class="mb-3">Historique des inscriptions</h5>
                            @if($inscriptions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Année académique</th>
                                                <th>Classe</th>
                                                <th>Filière</th>
                                                <th>Date d'inscription</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inscriptions as $inscription)
                                            <tr class="{{ $inscription->id == ($inscriptionActive->id ?? null) ? 'table-success' : '' }}">
                                                <td>{{ $inscription->annee_academique->nom ?? 'Non définie' }}</td>
                                                <td>{{ $inscription->classe->nom ?? 'Non définie' }}</td>
                                                <td>{{ $inscription->classe->filiere->nom ?? 'Non définie' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($inscription->date_inscription)->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($inscription->id == ($inscriptionActive->id ?? null))
                                                        <span class="badge bg-success">Actuelle</span>
                                                    @else
                                                        <span class="badge bg-secondary">Terminée</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Aucune inscription enregistrée pour cet étudiant.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Onglet Notes -->
                        <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                            <h5 class="mb-3">Notes et évaluations</h5>
                            @if(count($notes) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Matière</th>
                                                <th>Type d'évaluation</th>
                                                <th>Note</th>
                                                <th>Commentaire</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($notes as $note)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($note->created_at)->format('d/m/Y') }}</td>
                                                <td>{{ $note->evaluation->matiere->nom ?? 'Non définie' }}</td>
                                                <td>{{ $note->evaluation->type ?? 'Non défini' }}</td>
                                                <td>
                                                    <span class="fw-bold {{ $note->valeur >= 10 ? 'text-success' : 'text-danger' }}">
                                                        {{ $note->valeur }}/{{ $note->evaluation->bareme ?? 20 }}
                                                    </span>
                                                </td>
                                                <td>{{ $note->commentaire ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Aucune note enregistrée pour cet étudiant.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Onglet Absences -->
                        <div class="tab-pane fade" id="absences" role="tabpanel" aria-labelledby="absences-tab">
                            <h5 class="mb-3">Absences et retards</h5>
                            @if(count($absences) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Cours</th>
                                                <th>Type</th>
                                                <th>Durée</th>
                                                <th>Justifiée</th>
                                                <th>Motif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($absences as $absence)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</td>
                                                <td>{{ $absence->session->matiere->nom ?? 'Non défini' }}</td>
                                                <td>
                                                    @if($absence->type == 'absence')
                                                        <span class="badge bg-danger">Absence</span>
                                                    @else
                                                        <span class="badge bg-warning">Retard</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($absence->type == 'absence')
                                                        Journée
                                                    @else
                                                        {{ $absence->duree ?? '-' }} min
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($absence->justifiee)
                                                        <span class="badge bg-success">Oui</span>
                                                    @else
                                                        <span class="badge bg-danger">Non</span>
                                                    @endif
                                                </td>
                                                <td>{{ $absence->motif ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Aucune absence enregistrée pour cet étudiant.
                                </div>
                            @endif
                        </div>
                        
                        <!-- Onglet Paiements -->
                        <div class="tab-pane fade" id="paiements" role="tabpanel" aria-labelledby="paiements-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Historique des paiements</h5>
                                <a href="{{ route('parent.payments.student', $etudiant->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Voir tous les paiements
                                </a>
                            </div>
                            
                            @if(count($payments) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Référence</th>
                                                <th>Description</th>
                                                <th>Montant</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($payments as $payment)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</td>
                                                <td>{{ $payment->reference }}</td>
                                                <td>{{ $payment->description }}</td>
                                                <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                                <td>
                                                    @if($payment->status == 'confirmed')
                                                        <span class="badge bg-success">Confirmé</span>
                                                    @elseif($payment->status == 'pending')
                                                        <span class="badge bg-warning">En attente</span>
                                                    @else
                                                        <span class="badge bg-danger">Annulé</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('parent.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($payment->status == 'confirmed' && $payment->receipt_path)
                                                    <a href="{{ route('parent.payments.download-receipt', $payment->id) }}" class="btn btn-sm btn-outline-success ms-1">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Aucun paiement enregistré pour cet étudiant.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Activation des onglets
    document.addEventListener('DOMContentLoaded', function() {
        var triggerTabList = [].slice.call(document.querySelectorAll('#studentTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })
    });
</script>
@endsection 