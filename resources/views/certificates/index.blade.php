@extends('layouts.app')

@section('title', 'Gestion des Certificats')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Certificats</h1>
        <div>
            <a href="{{ route('certificate-types.index') }}" class="btn btn-info me-2">
                <i class="fas fa-list"></i> Types de Certificats
            </a>
            <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Générer un Certificat
            </a>
        </div>
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Carte pour la recherche et le filtrage -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recherche et Filtrage</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('certificates.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nom, numéro..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="certificate_type_id" class="form-label">Type de Certificat</label>
                    <select class="form-select" id="certificate_type_id" name="certificate_type_id">
                        <option value="">Tous les types</option>
                        @foreach($certificateTypes ?? [] as $type)
                            <option value="{{ $type->id }}" {{ request('certificate_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>Révoqué</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('certificates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques des certificats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Certificats</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCertificates ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Certificats Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeCertificates ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Certificats Révoqués</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $revokedCertificates ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Certificats ce Mois</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $certificatesThisMonth ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des certificats -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Certificats</h6>
            <button class="btn btn-sm btn-success" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Étudiant</th>
                            <th>Type de Certificat</th>
                            <th>Numéro</th>
                            <th>Date d'Émission</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificates ?? [] as $certificate)
                            <tr>
                                <td>{{ $certificate->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($certificate->student->photo)
                                            <img src="{{ asset('storage/' . $certificate->student->photo) }}" 
                                                 alt="Photo" class="rounded-circle me-2" width="40" height="40">
                                        @else
                                            <img src="{{ asset('images/default-avatar.png') }}" 
                                                 alt="Default" class="rounded-circle me-2" width="40" height="40">
                                        @endif
                                        <div>
                                            <div>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</div>
                                            <small class="text-muted">{{ $certificate->student->admission_no }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $certificate->certificateType->name ?? 'N/A' }}</td>
                                <td>{{ $certificate->certificate_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($certificate->issue_date)->format('d/m/Y') }}</td>
                                <td>
                                    @if($certificate->status == 'active')
                                        <span class="badge bg-success">Actif</span>
                                    @elseif($certificate->status == 'revoked')
                                        <span class="badge bg-danger">Révoqué</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $certificate->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('certificates.download', $certificate->id) }}" 
                                           class="btn btn-sm btn-success" title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal{{ $certificate->id }}" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($certificate->status == 'active')
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#revokeModal{{ $certificate->id }}" 
                                                    title="Révoquer">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $certificate->id }}" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de visualisation -->
                                    <div class="modal fade" id="viewModal{{ $certificate->id }}" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel">Certificat #{{ $certificate->certificate_number }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="certificate-preview">
                                                        <div class="text-center mb-4">
                                                            <img src="{{ asset('images/esbtp_logo.png') }}" alt="ESBTP Logo" class="img-fluid" style="max-height: 80px;">
                                                            <h2 class="mt-3">{{ $certificate->certificateType->name }}</h2>
                                                            <div class="certificate-number">N° {{ $certificate->certificate_number }}</div>
                                                        </div>
                                                        
                                                        <div class="certificate-content">
                                                            <p>Ce document certifie que</p>
                                                            <h3>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</h3>
                                                            <p>Né(e) le {{ \Carbon\Carbon::parse($certificate->student->date_of_birth)->format('d/m/Y') }}</p>
                                                            <p>Numéro d'admission: {{ $certificate->student->admission_no }}</p>
                                                            <p class="certificate-description">{{ $certificate->description }}</p>
                                                            
                                                            <div class="certificate-date">
                                                                Fait à Yakro, le {{ \Carbon\Carbon::parse($certificate->issue_date)->format('d/m/Y') }}
                                                            </div>
                                                            
                                                            <div class="row mt-5">
                                                                <div class="col-md-6 text-center">
                                                                    <p>Le Directeur</p>
                                                                    <div class="signature-placeholder">Signature</div>
                                                                </div>
                                                                <div class="col-md-6 text-center">
                                                                    <p>Cachet de l'établissement</p>
                                                                    <div class="stamp-placeholder">Cachet</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-primary">
                                                        <i class="fas fa-download"></i> Télécharger
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de révocation -->
                                    <div class="modal fade" id="revokeModal{{ $certificate->id }}" tabindex="-1" aria-labelledby="revokeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="revokeModalLabel">Confirmer la révocation</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir révoquer ce certificat pour <strong>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</strong> ?
                                                    <p class="text-warning mt-2">Cette action rendra le certificat invalide.</p>
                                                    <form action="{{ route('certificate.revoke', $certificate->id) }}" method="POST" id="revokeForm{{ $certificate->id }}">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="revocation_reason{{ $certificate->id }}" class="form-label">Raison de la révocation</label>
                                                            <textarea class="form-control" id="revocation_reason{{ $certificate->id }}" name="revocation_reason" rows="3" required></textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" form="revokeForm{{ $certificate->id }}" class="btn btn-warning">Révoquer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de suppression -->
                                    <div class="modal fade" id="deleteModal{{ $certificate->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer ce certificat pour <strong>{{ $certificate->student->first_name }} {{ $certificate->student->last_name }}</strong> ?
                                                    <p class="text-danger mt-2">Cette action est irréversible.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucun certificat trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($certificates) && $certificates->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $certificates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Styles pour les certificats -->
<style>
    .certificate-preview {
        background-color: #fff;
        border: 1px solid #ddd;
        padding: 30px;
        position: relative;
        font-family: 'Times New Roman', Times, serif;
    }
    
    .certificate-preview::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('{{ asset("images/esbtp_logo.png") }}');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 50%;
        opacity: 0.05;
        pointer-events: none;
    }
    
    .certificate-number {
        font-size: 1.2rem;
        color: #777;
        margin-bottom: 20px;
    }
    
    .certificate-content {
        text-align: center;
        line-height: 1.8;
    }
    
    .certificate-description {
        margin: 30px 0;
        font-style: italic;
    }
    
    .certificate-date {
        margin: 30px 0;
        text-align: right;
    }
    
    .signature-placeholder, .stamp-placeholder {
        height: 60px;
        border-bottom: 1px solid #333;
        margin-top: 10px;
    }
    
    .stamp-placeholder {
        border: 1px dashed #333;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        margin: 10px auto;
    }
</style>
@endsection 