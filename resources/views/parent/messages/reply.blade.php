@extends('layouts.app')

@section('title', 'Répondre au message')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Répondre au message</h5>
                    <a href="{{ route('parent.messages.show', $originalMessage->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour au message
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="mb-4 p-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">{{ $originalMessage->subject }}</h6>
                            <small>{{ $originalMessage->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                        
                        <div class="d-flex mb-3 text-muted">
                            <div class="me-4">
                                <strong>De:</strong> {{ $originalMessage->sender->name ?? 'Utilisateur inconnu' }}
                            </div>
                            <div>
                                <strong>À:</strong> {{ $originalMessage->receiver->name ?? 'Utilisateur inconnu' }}
                            </div>
                        </div>
                        
                        <div class="message-content">
                            {!! nl2br(e($originalMessage->content)) !!}
                        </div>
                    </div>

                    <form action="{{ route('parent.messages.store-reply', $originalMessage->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Votre réponse <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="6" required>{{ old('content') }}</textarea>
                            <small class="form-text text-muted">Rédigez votre réponse. Soyez clair et concis.</small>
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
@endsection 