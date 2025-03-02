@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Messages</h5>
                    <div>
                        <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Tableau de bord
                        </a>
                        <a href="{{ route('parent.messages.create') }}" class="btn btn-primary btn-sm me-2">
                            <i class="fas fa-plus me-1"></i>Nouveau message
                        </a>
                        <a href="{{ route('parent.messages.mark-all-read') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-check-double me-1"></i>Tout marquer comme lu
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

                    <ul class="nav nav-tabs mb-4" id="messagesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox" type="button" role="tab" aria-controls="inbox" aria-selected="true">
                                <i class="fas fa-inbox me-1"></i>Boîte de réception
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab" aria-controls="sent" aria-selected="false">
                                <i class="fas fa-paper-plane me-1"></i>Messages envoyés
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="messagesTabContent">
                        <div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                            @php
                                $inboxMessages = $messages->where('receiver_id', Auth::id());
                            @endphp
                            
                            @if ($inboxMessages->count() > 0)
                                <div class="list-group">
                                    @foreach ($inboxMessages as $message)
                                        <a href="{{ route('parent.messages.show', $message->id) }}" 
                                           class="list-group-item list-group-item-action {{ $message->read_at ? '' : 'fw-bold bg-light' }}">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $message->subject }}</h6>
                                                <small>{{ $message->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 text-truncate">{{ Str::limit($message->content, 100) }}</p>
                                            <div class="d-flex align-items-center mt-2">
                                                <small class="text-primary">
                                                    <i class="fas fa-user me-1"></i>
                                                    De: {{ $message->sender->name ?? 'Utilisateur inconnu' }}
                                                </small>
                                                
                                                @if ($message->etudiant_id)
                                                    @php
                                                        $etudiant = App\Models\ESBTPEtudiant::find($message->etudiant_id);
                                                    @endphp
                                                    @if ($etudiant)
                                                        <small class="text-muted ms-3">
                                                            <i class="fas fa-user-graduate me-1"></i>
                                                            Concernant: {{ $etudiant->prenoms }} {{ $etudiant->nom }}
                                                        </small>
                                                    @endif
                                                @endif
                                                
                                                @if (!$message->read_at)
                                                    <span class="badge bg-primary ms-auto">Non lu</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Aucun message dans votre boîte de réception.
                                </div>
                            @endif
                        </div>
                        
                        <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
                            @php
                                $sentMessages = $messages->where('sender_id', Auth::id());
                            @endphp
                            
                            @if ($sentMessages->count() > 0)
                                <div class="list-group">
                                    @foreach ($sentMessages as $message)
                                        <a href="{{ route('parent.messages.show', $message->id) }}" 
                                           class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">{{ $message->subject }}</h6>
                                                <small>{{ $message->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 text-truncate">{{ Str::limit($message->content, 100) }}</p>
                                            <div class="d-flex align-items-center mt-2">
                                                <small class="text-primary">
                                                    <i class="fas fa-user me-1"></i>
                                                    À: {{ $message->receiver->name ?? 'Utilisateur inconnu' }}
                                                </small>
                                                
                                                @if ($message->etudiant_id)
                                                    @php
                                                        $etudiant = App\Models\ESBTPEtudiant::find($message->etudiant_id);
                                                    @endphp
                                                    @if ($etudiant)
                                                        <small class="text-muted ms-3">
                                                            <i class="fas fa-user-graduate me-1"></i>
                                                            Concernant: {{ $etudiant->prenoms }} {{ $etudiant->nom }}
                                                        </small>
                                                    @endif
                                                @endif
                                                
                                                @if ($message->read_at)
                                                    <span class="badge bg-success ms-auto">Lu</span>
                                                @else
                                                    <span class="badge bg-secondary ms-auto">Non lu</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>Vous n'avez envoyé aucun message.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 