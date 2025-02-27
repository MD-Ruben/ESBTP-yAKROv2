@extends('layouts.app')

@section('title', 'Gestion des Notifications')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-8 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="fw-bold mb-0">Gestion des notifications</h2>
                                <button type="button" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                                    <i class="fas fa-paper-plane me-2"></i> Envoyer une notification
                                </button>
                            </div>
                            <p class="text-muted mb-4">Gérez les notifications envoyées aux utilisateurs, consultez les statistiques et suivez les interactions.</p>
                            
                            <div class="d-flex gap-3 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary-light rounded-circle p-2 me-2">
                                        <i class="fas fa-bell text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $totalNotifications ?? 0 }}</h6>
                                        <small class="text-muted">Notifications</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success-light rounded-circle p-2 me-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $readNotifications ?? 0 }}</h6>
                                        <small class="text-muted">Lues</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-warning-light rounded-circle p-2 me-2">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $unreadNotifications ?? 0 }}</h6>
                                        <small class="text-muted">Non lues</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-none d-md-block" style="background: linear-gradient(135deg, var(--esbtp-green-light), var(--esbtp-green)); min-height: 220px;">
                            <div class="h-100 d-flex align-items-center justify-content-center">
                                <img src="https://img.freepik.com/free-vector/push-notifications-concept-illustration_114360-4986.jpg" alt="Notifications" class="img-fluid" style="max-height: 200px; opacity: 0.9;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Search and Filter Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="{{ route('notifications.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label small text-muted">Type</label>
                            <select class="form-select border-0 bg-light" id="type" name="type">
                                <option value="">Tous les types</option>
                                <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Information</option>
                                <option value="warning" {{ request('type') == 'warning' ? 'selected' : '' }}>Avertissement</option>
                                <option value="success" {{ request('type') == 'success' ? 'selected' : '' }}>Succès</option>
                                <option value="danger" {{ request('type') == 'danger' ? 'selected' : '' }}>Alerte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="recipient_type" class="form-label small text-muted">Destinataire</label>
                            <select class="form-select border-0 bg-light" id="recipient_type" name="recipient_type">
                                <option value="">Tous les destinataires</option>
                                <option value="all" {{ request('recipient_type') == 'all' ? 'selected' : '' }}>Tous les utilisateurs</option>
                                <option value="students" {{ request('recipient_type') == 'students' ? 'selected' : '' }}>Étudiants</option>
                                <option value="teachers" {{ request('recipient_type') == 'teachers' ? 'selected' : '' }}>Enseignants</option>
                                <option value="parents" {{ request('recipient_type') == 'parents' ? 'selected' : '' }}>Parents</option>
                                <option value="staff" {{ request('recipient_type') == 'staff' ? 'selected' : '' }}>Personnel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label small text-muted">Statut</label>
                            <select class="form-select border-0 bg-light" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Lues</option>
                                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Non lues</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="d-grid w-100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des notifications -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Total</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $totalNotifications ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-primary-light rounded-circle p-3">
                            <i class="fas fa-bell text-primary fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Notifications envoyées</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Lues</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $readNotifications ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-success-light rounded-circle p-3">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($totalNotifications ?? 0) > 0 ? (($readNotifications ?? 0) / ($totalNotifications ?? 1)) * 100 : 0 }}%"></div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Taux de lecture: {{ ($totalNotifications ?? 0) > 0 ? round((($readNotifications ?? 0) / ($totalNotifications ?? 1)) * 100) : 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Non lues</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $unreadNotifications ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-warning-light rounded-circle p-3">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($totalNotifications ?? 0) > 0 ? (($unreadNotifications ?? 0) / ($totalNotifications ?? 1)) * 100 : 0 }}%"></div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">En attente de lecture</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 overflow-hidden stat-card">
                <div class="card-body position-relative">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="text-uppercase fw-semibold text-muted mb-1">Aujourd'hui</h6>
                            <h2 class="mb-0 display-5 fw-bold">{{ $todayNotifications ?? 0 }}</h2>
                        </div>
                        <div class="stat-icon bg-info-light rounded-circle p-3">
                            <i class="fas fa-calendar-day text-info fa-2x"></i>
                        </div>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($totalNotifications ?? 0) > 0 ? (($todayNotifications ?? 0) / ($totalNotifications ?? 1)) * 100 : 0 }}%"></div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Envoyées aujourd'hui</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des notifications -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <i class="fas fa-list text-primary me-2"></i>
                    <h5 class="card-title mb-0 fw-bold">Liste des notifications</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="ps-4">#</th>
                                    <th scope="col">Titre</th>
                                    <th scope="col">Message</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Destinataires</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Date</th>
                                    <th scope="col" class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications ?? [] as $notification)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">{{ $notification->title }}</h6>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-truncate" style="max-width: 200px;">{{ $notification->message }}</p>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($notification->type == 'info') bg-primary-light text-primary
                                            @elseif($notification->type == 'success') bg-success-light text-success
                                            @elseif($notification->type == 'warning') bg-warning-light text-warning
                                            @elseif($notification->type == 'danger') bg-danger-light text-danger
                                            @else bg-secondary-light text-secondary
                                            @endif">
                                            @if($notification->type == 'info') Information
                                            @elseif($notification->type == 'success') Succès
                                            @elseif($notification->type == 'warning') Avertissement
                                            @elseif($notification->type == 'danger') Alerte
                                            @else {{ $notification->type }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            @if($notification->recipient_type == 'all') Tous les utilisateurs
                                            @elseif($notification->recipient_type == 'students') Étudiants
                                            @elseif($notification->recipient_type == 'teachers') Enseignants
                                            @elseif($notification->recipient_type == 'parents') Parents
                                            @elseif($notification->recipient_type == 'staff') Personnel
                                            @else {{ $notification->recipient_type }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($notification->read_count > 0)
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ ($notification->read_count / $notification->recipient_count) * 100 }}%" 
                                                        aria-valuenow="{{ ($notification->read_count / $notification->recipient_count) * 100 }}" 
                                                        aria-valuemin="0" 
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small>{{ $notification->read_count }}/{{ $notification->recipient_count }}</small>
                                            </div>
                                        @else
                                            <span class="badge bg-warning-light text-warning">Non lue</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('notifications.show', $notification->id) }}">
                                                        <i class="fas fa-eye text-primary me-2"></i> Voir
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('notifications.resend', $notification->id) }}">
                                                        <i class="fas fa-paper-plane text-info me-2"></i> Renvoyer
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $notification->id }}">
                                                        <i class="fas fa-trash text-danger me-2"></i> Supprimer
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteModal{{ $notification->id }}" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir supprimer cette notification ?</p>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle me-2"></i> Cette action est irréversible.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
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
                                    <td colspan="8" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="mb-3">
                                                <i class="fas fa-bell-slash fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-muted">Aucune notification trouvée</h5>
                                            <p class="text-muted small mb-0">Ajustez vos filtres ou envoyez une nouvelle notification</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-0">Affichage de {{ $notifications->firstItem() ?? 0 }} à {{ $notifications->lastItem() ?? 0 }} sur {{ $notifications->total() ?? 0 }} notifications</p>
                        </div>
                        <div>
                            {{ $notifications->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour envoyer une notification -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNotificationModalLabel">Envoyer une notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('notifications.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="info">Information</option>
                                    <option value="success">Succès</option>
                                    <option value="warning">Avertissement</option>
                                    <option value="danger">Alerte</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="recipient_type" class="form-label">Destinataires <span class="text-danger">*</span></label>
                                <select class="form-select" id="recipient_type" name="recipient_type" required>
                                    <option value="all">Tous les utilisateurs</option>
                                    <option value="students">Étudiants</option>
                                    <option value="teachers">Enseignants</option>
                                    <option value="parents">Parents</option>
                                    <option value="staff">Personnel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1">
                            <label class="form-check-label" for="send_email">
                                Envoyer également par email
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i> Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Styles pour les badges et icônes */
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }
    
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }
    
    .bg-secondary-light {
        background-color: rgba(108, 117, 125, 0.1);
    }
    
    .icon-box {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Style pour les cartes statistiques */
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 15px;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    /* Style pour la pagination */
    .pagination {
        margin-bottom: 0;
    }
    
    .page-item.active .page-link {
        background-color: var(--esbtp-green);
        border-color: var(--esbtp-green);
    }
    
    .page-link {
        color: var(--esbtp-green);
    }
    
    /* Animation pour les lignes du tableau */
    tbody tr {
        transition: all 0.2s ease;
    }
    
    tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endsection 