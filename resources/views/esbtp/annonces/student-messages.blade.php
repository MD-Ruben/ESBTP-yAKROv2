@extends('layouts.app')

@section('title', 'Mes Messages')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow border-0 rounded-lg">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-bottom-0">
                    <h3 class="card-title mb-0"><i class="fas fa-envelope-open-text me-2 text-primary"></i>Mes Messages</h3>
                    <div class="card-tools d-flex align-items-center">
                        <div class="btn-group shadow-0 me-2">
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" id="filterAll">
                                <i class="fas fa-inbox me-1"></i>Tous <span class="badge bg-white text-primary ms-1">{{ $stats['total'] }}</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" id="filterUnread">
                                <i class="fas fa-envelope me-1"></i>Non lus <span class="badge bg-warning text-dark ms-1">{{ $stats['unread'] }}</span>
                            </button>
                            @if($stats['urgent'] > 0)
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" id="filterUrgent">
                                <i class="fas fa-exclamation-triangle me-1"></i>Urgent <span class="badge bg-danger text-white ms-1">{{ $stats['urgent'] }}</span>
                            </button>
                            @endif
                        </div>
                        @if($stats['unread'] > 0)
                        <button class="btn btn-sm btn-success rounded-pill px-3" id="markAllRead">
                            <i class="fas fa-check-double me-1"></i>Tout marquer comme lu
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="messages-container">
                        @forelse($messages as $annonce)
                        <div class="message-item card mb-3 border-0 shadow-sm message-card {{ $annonce->is_read ? '' : 'unread' }} {{ $annonce->priority == 'high' ? 'urgent' : '' }}">
                            <div class="card-header d-flex justify-content-between align-items-center border-bottom-0 {{ $annonce->priority == 'high' ? 'bg-danger text-white' : ($annonce->is_read ? 'bg-light' : 'bg-warning bg-opacity-10') }}">
                                <h5 class="mb-0 d-flex align-items-center">
                                    @if(!$annonce->is_read)
                                    <span class="badge bg-primary rounded-pill me-2"><i class="fas fa-bell me-1"></i>Nouveau</span>
                                    @endif
                                    @if($annonce->priority == 'high')
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    @elseif(!$annonce->is_read)
                                    <i class="fas fa-envelope me-2"></i>
                                    @else
                                    <i class="fas fa-envelope-open me-2"></i>
                                    @endif
                                    {{ $annonce->titre }}
                                </h5>
                                <div class="d-flex align-items-center">
                                    <div class="date-badge me-2">
                                        @if($annonce->created_at->isToday())
                                            <span class="badge bg-info text-white rounded-pill"><i class="fas fa-clock me-1"></i>Aujourd'hui</span>
                                        @elseif($annonce->created_at->isYesterday())
                                            <span class="badge bg-secondary text-white rounded-pill"><i class="fas fa-history me-1"></i>Hier</span>
                                        @else
                                            <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ $annonce->created_at->format('d/m/Y H:i') }}</small>
                                        @endif
                                    </div>
                                    @if(!$annonce->is_read)
                                    <button class="btn btn-sm btn-info rounded-pill mark-read" data-id="{{ $annonce->id }}">
                                        <i class="fas fa-check"></i> Marquer comme lu
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="message-preview">{!! Str::limit(strip_tags($annonce->contenu), 150) !!}</p>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill read-more" data-toggle="modal" data-target="#messageModal{{ $annonce->id }}">
                                            Lire plus <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Modal -->
                        <div class="modal fade" id="messageModal{{ $annonce->id }}" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel{{ $annonce->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header {{ $annonce->priority == 'high' ? 'bg-danger text-white' : 'bg-primary text-white' }}">
                                        <h5 class="modal-title" id="messageModalLabel{{ $annonce->id }}">
                                            @if($annonce->priority == 'high')
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            @else
                                            <i class="fas fa-envelope-open me-2"></i>
                                            @endif
                                            {{ $annonce->titre }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="message-info mb-3 p-3 bg-light rounded-3">
                                            <div class="row">
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <i class="far fa-calendar-alt me-2 text-primary"></i><strong>Date:</strong> {{ $annonce->created_at->format('d/m/Y H:i') }}
                                                </div>
                                                <div class="col-md-4 mb-2 mb-md-0">
                                                    <i class="fas fa-hourglass-end me-2 text-primary"></i><strong>Expire le:</strong> {{ $annonce->expiration ? $annonce->expiration->format('d/m/Y') : 'Jamais' }}
                                                </div>
                                                <div class="col-md-4">
                                                    <i class="fas fa-signal me-2 text-primary"></i><strong>Priorité:</strong>
                                                    @if($annonce->priority == 'high')
                                                    <span class="badge bg-danger rounded-pill">Urgent</span>
                                                    @elseif($annonce->priority == 'medium')
                                                    <span class="badge bg-warning text-dark rounded-pill">Moyenne</span>
                                                    @else
                                                    <span class="badge bg-success rounded-pill">Normale</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="message-content p-3 bg-white border rounded-3">
                                            {!! $annonce->contenu !!}
                                        </div>
                                        @if($annonce->fichier)
                                        <div class="message-attachments mt-4 p-3 bg-light rounded-3">
                                            <h6 class="mb-3"><i class="fas fa-paperclip me-2 text-primary"></i>Pièce jointe:</h6>
                                            <a href="{{ asset('storage/' . $annonce->fichier) }}" class="btn btn-sm btn-outline-primary rounded-pill" target="_blank">
                                                <i class="fas fa-download me-1"></i> Télécharger la pièce jointe
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        @if(!$annonce->is_read)
                                        <button type="button" class="btn btn-success rounded-pill mark-read" data-id="{{ $annonce->id }}" data-dismiss="modal">
                                            <i class="fas fa-check me-1"></i> Marquer comme lu
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">
                                            <i class="fas fa-times me-1"></i> Fermer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info rounded-3 shadow-sm">
                            <i class="fas fa-info-circle me-2"></i> Vous n'avez pas de messages.
                        </div>
                        @endforelse
                    </div>

                    <div class="pagination-container mt-4 d-flex justify-content-center">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .message-item {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    .message-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.15)!important;
    }
    .message-card:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 12px;
        background: rgba(255,255,255,0.05);
        opacity: 0;
        transition: all 0.3s ease;
        pointer-events: none;
    }
    .message-card:hover:after {
        opacity: 1;
    }
    .message-item.unread {
        border-left: 4px solid #17a2b8;
        background-color: rgba(23, 162, 184, 0.02);
    }
    .message-item.urgent {
        border-left: 4px solid #dc3545;
    }
    .message-preview {
        margin-bottom: 0.8rem;
        color: #555;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    .message-attachments {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
    }
    .message-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
    }
    .message-content {
        box-shadow: 0 0 10px rgba(0,0,0,0.03);
    }
    .btn-group .btn {
        border-radius: 20px;
        margin-right: 2px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-group .btn:hover {
        transform: translateY(-2px);
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    #markAllRead {
        border-radius: 20px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    #markAllRead:hover {
        transform: translateY(-2px);
    }
    .card-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 1rem 1.25rem;
    }
    .read-more {
        transition: all 0.2s ease;
    }
    .read-more:hover {
        transform: translateX(3px);
    }
    .mark-read {
        transition: all 0.2s ease;
    }
    .mark-read:hover {
        transform: translateY(-2px);
    }
    .badge {
        font-weight: 500;
    }
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }
    .modal-header {
        padding: 1rem 1.5rem;
    }
    .modal-body {
        padding: 1.5rem;
    }
    .modal-footer {
        padding: 1rem 1.5rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        .card-tools {
            margin-top: 10px;
            width: 100%;
            flex-direction: column;
        }
        .btn-group {
            margin-bottom: 10px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }
        #markAllRead {
            width: 100%;
        }
        .message-item .card-header {
            flex-direction: column;
        }
        .message-item .card-header h5 {
            margin-bottom: 10px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Active filter indication
        $('#filterAll').on('click', function() {
            $('.btn-group .btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        });

        $('#filterUnread').on('click', function() {
            $('.btn-group .btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-warning').addClass('btn-warning');
        });

        $('#filterUrgent').on('click', function() {
            $('.btn-group .btn').removeClass('btn-primary').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-danger').addClass('btn-danger');
        });

        // Mark single message as read
        $('.mark-read').on('click', function() {
            const messageId = $(this).data('id');
            const messageElement = $(this).closest('.message-item');

            $.ajax({
                url: "{{ route('esbtp.mes-messages.read', '') }}/" + messageId,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI
                        messageElement.removeClass('unread');
                        messageElement.find('.card-header').removeClass('bg-warning bg-opacity-10').addClass('bg-light');
                        messageElement.find('.badge-primary').remove();
                        messageElement.find('.mark-read').remove();
                        messageElement.find('.fas.fa-envelope').removeClass('fa-envelope').addClass('fa-envelope-open');

                        // Update counts
                        const unreadCount = parseInt($('#filterUnread .badge').text().match(/\d+/)[0]) - 1;
                        $('#filterUnread .badge').html(unreadCount);

                        // Notification
                        toastr.success('Message marqué comme lu');

                        // If in modal, close it
                        $('.modal').modal('hide');

                        // Hide mark all read button if no unread messages
                        if (unreadCount <= 0) {
                            $('#markAllRead').hide();
                        }
                    }
                },
                error: function() {
                    toastr.error('Erreur lors du marquage du message comme lu');
                }
            });
        });

        // Mark all messages as read
        $('#markAllRead').on('click', function() {
            $.ajax({
                url: "{{ route('esbtp.mes-messages.mark-all-read') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI for all unread messages
                        $('.message-item.unread').removeClass('unread');
                        $('.message-item .card-header.bg-warning').removeClass('bg-warning bg-opacity-10').addClass('bg-light');
                        $('.badge-primary').remove();
                        $('.mark-read').remove();
                        $('.fas.fa-envelope').removeClass('fa-envelope').addClass('fa-envelope-open');

                        // Update count
                        $('#filterUnread .badge').html('0');

                        // Hide mark all read button
                        $('#markAllRead').hide();

                        // Notification
                        toastr.success('Tous les messages ont été marqués comme lus');
                    }
                },
                error: function() {
                    toastr.error('Erreur lors du marquage de tous les messages comme lus');
                }
            });
        });

        // Filters
        $('#filterAll').on('click', function() {
            $('.message-item').show();
        });

        $('#filterUnread').on('click', function() {
            $('.message-item').hide();
            $('.message-item.unread').show();
        });

        $('#filterUrgent').on('click', function() {
            $('.message-item').hide();
            $('.message-item.urgent').show();
        });
    });
</script>
@endsection
