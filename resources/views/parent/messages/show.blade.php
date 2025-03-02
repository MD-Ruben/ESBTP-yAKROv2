@extends('layouts.app')

@section('title', $message->subject)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détail du message</h5>
                    <div>
                        <a href="{{ route('parent.messages.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i>Retour aux messages
                        </a>
                        <a href="{{ route('parent.messages.reply', $message->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-reply me-1"></i>Répondre
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

                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">{{ $message->subject }}</h4>
                            <span class="badge {{ $message->read_at ? 'bg-success' : 'bg-primary' }}">
                                {{ $message->read_at ? 'Lu' : 'Non lu' }}
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-3 text-muted">
                            <div class="me-4 mb-2">
                                <strong>De:</strong> {{ $message->sender->name ?? 'Utilisateur inconnu' }}
                            </div>
                            <div class="me-4 mb-2">
                                <strong>À:</strong> {{ $message->receiver->name ?? 'Utilisateur inconnu' }}
                            </div>
                            <div class="me-4 mb-2">
                                <strong>Date:</strong> {{ $message->created_at->format('d/m/Y à H:i') }}
                            </div>
                            
                            @if ($message->etudiant_id)
                                @php
                                    $etudiant = App\Models\ESBTPEtudiant::find($message->etudiant_id);
                                @endphp
                                @if ($etudiant)
                                    <div class="mb-2">
                                        <strong>Concernant:</strong> {{ $etudiant->prenoms }} {{ $etudiant->nom }}
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <hr>
                        
                        <div class="message-content mb-3">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                        
                        @if (!$message->read_at && $message->receiver_id === Auth::id())
                            <div class="text-end">
                                <a href="{{ route('parent.messages.mark-read', $message->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check me-1"></i>Marquer comme lu
                                </a>
                            </div>
                        @endif
                    </div>

                    @if ($replies->count() > 0)
                        <h5 class="mb-3">Réponses</h5>
                        
                        @foreach ($replies as $reply)
                            <div class="mb-3 p-3 border rounded {{ $reply->sender_id === Auth::id() ? 'bg-light-success' : 'bg-light' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $reply->subject }}</h6>
                                    <small>{{ $reply->created_at->format('d/m/Y à H:i') }}</small>
                                </div>
                                
                                <div class="d-flex mb-3 text-muted">
                                    <div class="me-4">
                                        <strong>De:</strong> {{ $reply->sender->name ?? 'Utilisateur inconnu' }}
                                    </div>
                                    <div>
                                        <strong>À:</strong> {{ $reply->receiver->name ?? 'Utilisateur inconnu' }}
                                    </div>
                                </div>
                                
                                <div class="message-content mb-0">
                                    {!! nl2br(e($reply->content)) !!}
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="mt-4">
                        <form action="{{ route('parent.messages.store-reply', $message->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Répondre</label>
                                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Envoyer la réponse
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 