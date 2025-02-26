@extends('layouts.app')

@section('title', 'Gestion des Notifications')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Notifications</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createNotificationModal">
            <i class="fas fa-paper-plane"></i> Envoyer une Notification
        </button>
    </div>

    <!-- Messages de notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filtres de recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recherche et Filtrage</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('notifications.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Type de Notification</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        <option value="info" {{ request('type') == 'info' ? 'selected' : '' }}>Information</option>
                        <option value="alert" {{ request('type') == 'alert' ? 'selected' : '' }}>Alerte</option>
                        <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Rappel</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="recipient_type" class="form-label">Type de Destinataire</label>
                    <select class="form-select" id="recipient_type" name="recipient_type">
                        <option value="">Tous les destinataires</option>
                        <option value="students" {{ request('recipient_type') == 'students' ? 'selected' : '' }}>Étudiants</option>
                        <option value="teachers" {{ request('recipient_type') == 'teachers' ? 'selected' : '' }}>Enseignants</option>
                        <option value="parents" {{ request('recipient_type') == 'parents' ? 'selected' : '' }}>Parents</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyé</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques des notifications -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des Notifications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNotifications ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
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
                                Notifications Envoyées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sentNotifications ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingNotifications ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Échecs d'Envoi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $failedNotifications ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des notifications -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Historique des Notifications</h6>
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
                            <th>Type</th>
                            <th>Titre</th>
                            <th>Destinataires</th>
                            <th>Date d'Envoi</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications ?? [] as $notification)
                            <tr>
                                <td>{{ $notification->id }}</td>
                                <td>
                                    @if($notification->type == 'info')
                                        <span class="badge bg-info">Information</span>
                                    @elseif($notification->type == 'alert')
                                        <span class="badge bg-warning">Alerte</span>
                                    @elseif($notification->type == 'reminder')
                                        <span class="badge bg-primary">Rappel</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $notification->type }}</span>
                                    @endif
                                </td>
                                <td>{{ $notification->title }}</td>
                                <td>
                                    @if($notification->recipient_type == 'students')
                                        <span class="badge bg-primary">Étudiants</span>
                                    @elseif($notification->recipient_type == 'teachers')
                                        <span class="badge bg-success">Enseignants</span>
                                    @elseif($notification->recipient_type == 'parents')
                                        <span class="badge bg-info">Parents</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $notification->recipient_type }}</span>
                                    @endif
                                    <small class="d-block text-muted mt-1">{{ $notification->recipients_count ?? 0 }} destinataires</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($notification->sent_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($notification->status == 'sent')
                                        <span class="badge bg-success">Envoyé</span>
                                    @elseif($notification->status == 'pending')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($notification->status == 'failed')
                                        <span class="badge bg-danger">Échoué</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $notification->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewModal{{ $notification->id }}" 
                                                title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($notification->status == 'failed')
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="retryNotification({{ $notification->id }})" 
                                                    title="Réessayer">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $notification->id }}" 
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Modal de visualisation -->
                                    <div class="modal fade" id="viewModal{{ $notification->id }}" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewModalLabel">Détails de la Notification</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <h6>Type</h6>
                                                            <p>{{ ucfirst($notification->type) }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Date d'Envoi</h6>
                                                            <p>{{ \Carbon\Carbon::parse($notification->sent_at)->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Titre</h6>
                                                        <p>{{ $notification->title }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Message</h6>
                                                        <div class="border rounded p-3 bg-light">
                                                            {!! nl2br(e($notification->message)) !!}
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Destinataires</h6>
                                                        <p>Type: {{ ucfirst($notification->recipient_type) }}</p>
                                                        @if($notification->recipients && $notification->recipients->count() > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nom</th>
                                                                            <th>Email</th>
                                                                            <th>Statut</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($notification->recipients as $recipient)
                                                                            <tr>
                                                                                <td>{{ $recipient->name }}</td>
                                                                                <td>{{ $recipient->email }}</td>
                                                                                <td>
                                                                                    @if($recipient->pivot->status == 'sent')
                                                                                        <span class="badge bg-success">Envoyé</span>
                                                                                    @elseif($recipient->pivot->status == 'failed')
                                                                                        <span class="badge bg-danger">Échoué</span>
                                                                                    @else
                                                                                        <span class="badge bg-secondary">{{ $recipient->pivot->status }}</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <p class="text-muted">Aucun destinataire trouvé</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
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
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Cette action est irréversible.
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
                                <td colspan="7" class="text-center">Aucune notification trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($notifications) && $notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'envoi de notification -->
<div class="modal fade" id="createNotificationModal" tabindex="-1" aria-labelledby="createNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createNotificationModalLabel">Envoyer une Nouvelle Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('notifications.store') }}" method="POST" id="notificationForm">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">Type de Notification</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="info">Information</option>
                                <option value="alert">Alerte</option>
                                <option value="reminder">Rappel</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="recipient_type" class="form-label">Type de Destinataire</label>
                            <select class="form-select" id="recipient_type" name="recipient_type" required>
                                <option value="students">Étudiants</option>
                                <option value="teachers">Enseignants</option>
                                <option value="parents">Parents</option>
                            </select>
                        </div>
                    </div>

                    <div id="recipientFilters" class="row mb-3">
                        <div class="col-md-6 student-filter">
                            <label for="class_id" class="form-label">Classe</label>
                            <select class="form-select" id="class_id" name="class_id">
                                <option value="">Toutes les classes</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 student-filter">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-select" id="section_id" name="section_id">
                                <option value="">Toutes les sections</option>
                            </select>
                        </div>
                        <div class="col-md-6 teacher-filter d-none">
                            <label for="department_id" class="form-label">Département</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">Tous les départements</option>
                                @foreach($departments ?? [] as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1">
                            <label class="form-check-label" for="send_email">
                                Envoyer également par email
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_sms" name="send_sms" value="1">
                            <label class="form-check-label" for="send_sms">
                                Envoyer également par SMS
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour la gestion des filtres de destinataires -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeSelect = document.getElementById('recipient_type');
    const studentFilters = document.querySelectorAll('.student-filter');
    const teacherFilters = document.querySelectorAll('.teacher-filter');
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section_id');

    // Gestion de l'affichage des filtres selon le type de destinataire
    recipientTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        
        studentFilters.forEach(filter => {
            filter.classList.toggle('d-none', selectedType !== 'students');
        });
        
        teacherFilters.forEach(filter => {
            filter.classList.toggle('d-none', selectedType !== 'teachers');
        });
    });

    // Chargement dynamique des sections selon la classe sélectionnée
    if (classSelect && sectionSelect) {
        classSelect.addEventListener('change', function() {
            const classId = this.value;
            
            // Réinitialiser le sélecteur de sections
            sectionSelect.innerHTML = '<option value="">Toutes les sections</option>';
            
            if (classId) {
                // Charger les sections pour cette classe
                fetch(`/api/sections/by-class/${classId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(section => {
                            const option = document.createElement('option');
                            option.value = section.id;
                            option.textContent = section.name;
                            sectionSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erreur lors du chargement des sections:', error));
            }
        });
    }
});

// Fonction pour réessayer l'envoi d'une notification
function retryNotification(notificationId) {
    if (confirm('Voulez-vous réessayer d'envoyer cette notification ?')) {
        fetch(`/notifications/${notificationId}/retry`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la tentative d\'envoi. Veuillez réessayer.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue. Veuillez réessayer.');
        });
    }
}
</script>
@endsection 