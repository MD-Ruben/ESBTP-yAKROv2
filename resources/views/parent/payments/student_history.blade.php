@extends('layouts.app')

@section('title', 'Historique des paiements - ' . $etudiant->prenoms . ' ' . $etudiant->nom)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Historique des paiements - {{ $etudiant->prenoms }} {{ $etudiant->nom }}</h5>
                    <div>
                        <a href="{{ route('parent.payments.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux paiements
                        </a>
                        <a href="{{ route('parent.student.details', $etudiant->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user me-1"></i>Profil de l'étudiant
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informations de l'étudiant</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Matricule:</strong> {{ $etudiant->matricule }}</p>
                                            <p><strong>Nom complet:</strong> {{ $etudiant->prenoms }} {{ $etudiant->nom }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            @if($etudiant->inscriptions && $etudiant->inscriptions->isNotEmpty())
                                                @php 
                                                    $inscription = $etudiant->inscriptions->sortByDesc('date_inscription')->first(); 
                                                @endphp
                                                <p><strong>Classe:</strong> {{ $inscription->classe->name ?? 'Non définie' }}</p>
                                                <p><strong>Filière:</strong> {{ $inscription->filiere->name ?? 'Non définie' }}</p>
                                                <p><strong>Année universitaire:</strong> {{ $inscription->anneeUniversitaire->name ?? 'Non définie' }}</p>
                                            @else
                                                <div class="alert alert-info">
                                                    Aucune inscription active pour cet étudiant.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Tous les paiements effectués</h6>
                        </div>
                        <div class="card-body">
                            @if ($paiements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Référence</th>
                                                <th>Mode de paiement</th>
                                                <th>Description</th>
                                                <th class="text-end">Montant</th>
                                                <th class="text-center">Statut</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paiements as $paiement)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($paiement->created_at)->format('d/m/Y') }}</td>
                                                    <td>{{ $paiement->reference }}</td>
                                                    <td>
                                                        @if($paiement->mode_paiement == 'mobile_money')
                                                            <span class="badge bg-success">Mobile Money</span>
                                                        @elseif($paiement->mode_paiement == 'carte_credit')
                                                            <span class="badge bg-primary">Carte de crédit</span>
                                                        @elseif($paiement->mode_paiement == 'virement')
                                                            <span class="badge bg-info">Virement bancaire</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($paiement->mode_paiement) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $paiement->description }}</td>
                                                    <td class="text-end">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                                    <td class="text-center">
                                                        @if($paiement->status == 'confirme' || $paiement->status == 'Confirmé')
                                                            <span class="badge bg-success">Confirmé</span>
                                                        @elseif($paiement->status == 'en_attente')
                                                            <span class="badge bg-warning">En attente</span>
                                                        @elseif($paiement->status == 'annule')
                                                            <span class="badge bg-danger">Annulé</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($paiement->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('parent.payments.show', $paiement->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye me-1"></i>Détails
                                                        </a>
                                                        @if($paiement->receipt_path)
                                                            <a href="{{ route('parent.payments.receipt', $paiement->id) }}" class="btn btn-sm btn-outline-primary ms-1">
                                                                <i class="fas fa-download me-1"></i>Reçu
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $paiements->links() }}
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Aucun paiement enregistré pour cet étudiant.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Commentaires</h6>
                        <div class="alert alert-info">
                            <p><i class="fas fa-info-circle me-2"></i>Si vous avez des questions concernant les paiements, veuillez contacter le service financier de l'école.</p>
                            <p class="mb-0">Téléphone: +225 XX XX XX XX | Email: finance@esbtp-yakro.ci</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 