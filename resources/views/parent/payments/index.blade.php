@extends('layouts.app')

@section('title', 'Paiements | Espace parent')

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
                                    <li class="breadcrumb-item active" aria-current="page">Paiements</li>
                                </ol>
                            </nav>
                            <h1 class="fw-bold mb-0">Historique des paiements</h1>
                            <p class="text-muted">Consultez et gérez tous les paiements de vos étudiants.</p>
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

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('parent.payments') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="student_id" class="form-label">Étudiant</label>
                                <select class="form-select" id="student_id" name="student_id">
                                    <option value="">Tous les étudiants</option>
                                    @foreach($etudiants as $etudiant)
                                        <option value="{{ $etudiant->id }}" {{ request('student_id') == $etudiant->id ? 'selected' : '' }}>
                                            {{ $etudiant->nom }} {{ $etudiant->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('parent.payments') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des paiements -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Liste des paiements</h5>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($payments) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Référence</th>
                                        <th>Étudiant</th>
                                        <th>Description</th>
                                        <th>Montant</th>
                                        <th>Méthode</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y') }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>
                                            @php
                                                $etudiant = $etudiants->firstWhere('id', $payment->student_id);
                                            @endphp
                                            @if($etudiant)
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $etudiant->photo ? asset('storage/' . $etudiant->photo) : asset('images/default-avatar.png') }}" 
                                                         alt="{{ $etudiant->nom }}" 
                                                         class="rounded-circle me-2"
                                                         width="32" height="32">
                                                    <div>
                                                        <div class="fw-medium">{{ $etudiant->nom }} {{ $etudiant->prenom }}</div>
                                                        <small class="text-muted">{{ $etudiant->matricule }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                Étudiant inconnu
                                            @endif
                                        </td>
                                        <td>{{ $payment->description }}</td>
                                        <td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            @if($payment->payment_method == 'cash')
                                                <span class="badge bg-secondary">Espèces</span>
                                            @elseif($payment->payment_method == 'bank_transfer')
                                                <span class="badge bg-info">Virement</span>
                                            @elseif($payment->payment_method == 'mobile_money')
                                                <span class="badge bg-primary">Mobile Money</span>
                                            @elseif($payment->payment_method == 'credit_card')
                                                <span class="badge bg-dark">Carte bancaire</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                                            @endif
                                        </td>
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
                                            <div class="btn-group">
                                                <a href="{{ route('parent.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($payment->status == 'confirmed' && $payment->receipt_path)
                                                    <a href="{{ route('parent.payments.download-receipt', $payment->id) }}" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $payments->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>Aucun paiement trouvé pour les critères sélectionnés.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informations complémentaires -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Informations sur les paiements</h5>
                    <div class="mb-3">
                        <p>Pour toute question concernant vos paiements, veuillez contacter le service financier:</p>
                        <ul>
                            <li><strong>Téléphone:</strong> +225 xx xx xx xx</li>
                            <li><strong>Email:</strong> finance@esbtp-yakro.ci</li>
                            <li><strong>Bureau:</strong> Bâtiment administratif, 1er étage</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Veuillez conserver les reçus de tous vos paiements. Ils peuvent être demandés lors des inscriptions aux examens.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection 