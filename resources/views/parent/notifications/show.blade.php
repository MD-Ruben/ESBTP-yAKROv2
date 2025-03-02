@extends('layouts.app')

@section('title', $notification->title)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détail de la notification</h5>
                    <a href="{{ route('parent.notifications.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour aux notifications
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4>{{ $notification->title }}</h4>
                        <div class="d-flex align-items-center text-muted mb-3">
                            <small class="me-3">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ $notification->created_at->format('d/m/Y à H:i') }}
                            </small>
                            @if ($notification->etudiant_id)
                                @php
                                    $etudiant = App\Models\ESBTPEtudiant::find($notification->etudiant_id);
                                @endphp
                                @if ($etudiant)
                                    <small class="me-3">
                                        <i class="fas fa-user-graduate me-1"></i>
                                        Concernant: {{ $etudiant->prenoms }} {{ $etudiant->nom }}
                                    </small>
                                @endif
                            @endif
                            <small>
                                <i class="fas fa-tag me-1"></i>
                                {{ ucfirst($notification->type ?? 'Information') }}
                            </small>
                            <span class="badge {{ $notification->read_at ? 'bg-secondary' : 'bg-primary' }} ms-auto">
                                {{ $notification->read_at ? 'Lu' : 'Non lu' }}
                            </span>
                        </div>
                        <hr>
                        <div class="notification-content">
                            {!! nl2br(e($notification->message)) !!}
                        </div>
                    </div>

                    @if ($notification->attachment)
                        <div class="mt-4">
                            <h6><i class="fas fa-paperclip me-1"></i>Pièce jointe</h6>
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $notification->attachment) }}" 
                                   class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-download me-1"></i>
                                    Télécharger le document
                                </a>
                            </div>
                        </div>
                    @endif

                    @if (!$notification->read_at)
                        <div class="mt-4 text-end">
                            <a href="{{ route('parent.notifications.mark-read', $notification->id) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-check me-1"></i>
                                Marquer comme lu
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($notification->etudiant_id)
        @php
            $etudiant = App\Models\ESBTPEtudiant::find($notification->etudiant_id);
        @endphp
        @if ($etudiant)
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Informations sur l'étudiant concerné</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 text-center mb-3">
                                    <img src="{{ $etudiant->photo ? asset('storage/'.$etudiant->photo) : asset('images/student-avatar.png') }}" 
                                         class="img-thumbnail rounded-circle" alt="{{ $etudiant->prenoms }}"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <div class="col-md-10">
                                    <h5>{{ $etudiant->prenoms }} {{ $etudiant->nom }}</h5>
                                    <p class="text-muted mb-1">Matricule: {{ $etudiant->matricule }}</p>
                                    <p class="mb-0">
                                        @if($etudiant->inscription && $etudiant->inscription->classe)
                                            <span class="badge bg-info">
                                                {{ $etudiant->inscription->classe->name }}
                                            </span>
                                        @endif
                                        <span class="badge {{ $etudiant->statut === 'actif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($etudiant->statut) }}
                                        </span>
                                    </p>
                                    <div class="mt-3">
                                        <a href="{{ route('parent.student.details', $etudiant->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Voir les détails complets
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    @if ($notification->type === 'note' && $notification->etudiant_id)
        <!-- Si la notification concerne une note, on pourrait afficher les détails de la note ici -->
    @elseif ($notification->type === 'absence' && $notification->etudiant_id)
        <!-- Si la notification concerne une absence, on pourrait afficher les détails de l'absence ici -->
    @elseif ($notification->type === 'paiement' && $notification->etudiant_id)
        <!-- Si la notification concerne un paiement, on pourrait afficher les détails du paiement ici -->
    @endif
</div>
@endsection 