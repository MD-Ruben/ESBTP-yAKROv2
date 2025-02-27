@extends('layouts.app')

@section('title', 'Tableau de bord Super Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenue dans le tableau de bord Super Admin</h5>
                    <p class="card-text">Gestion complète du système ESBTP-yAKRO</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Étudiants</h5>
                    <p class="card-text display-4">{{ $totalStudents ?? 0 }}</p>
                    <a href="{{ route('students.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Enseignants</h5>
                    <p class="card-text display-4">{{ $totalTeachers ?? 0 }}</p>
                    <a href="{{ route('teachers.index') }}" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Administrateurs</h5>
                    <p class="card-text display-4">{{ $totalAdmins ?? 0 }}</p>
                    <a href="#" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text display-4">{{ $totalUsers ?? 0 }}</p>
                    <a href="#" class="btn btn-light">Gérer</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Utilisateurs récents
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentUsers as $user)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $user->name }}</strong> ({{ $user->email }})
                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                </div>
                                <small>{{ $user->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun utilisateur récent</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Notifications récentes
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentNotifications as $notification)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $notification->title }}</strong>
                                    <p class="mb-0">{{ Str::limit($notification->message, 50) }}</p>
                                </div>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item">Aucune notification récente</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Certificats récents
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($recentCertificates as $certificate)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $certificate->certificateType->name }}</strong> pour
                                    {{ $certificate->student->user->name }}
                                </div>
                                <small>{{ $certificate->created_at->diffForHumans() }}</small>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun certificat récent</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    Statistiques de présence
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    Messagerie interne
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Envoyer un message</h5>
                            <form action="{{ route('messages.send-to-group') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="recipient_type" class="form-label">Destinataire</label>
                                    <select class="form-select" id="recipient_type" name="recipient_type" required>
                                        <option value="">Sélectionner un destinataire</option>
                                        <option value="all_students">Tous les étudiants</option>
                                        <option value="specific_classes">Classe(s) spécifique(s)</option>
                                        <option value="specific_students">Étudiant(s) spécifique(s)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3" id="classes_container" style="display: none;">
                                    <label for="selected_classes" class="form-label">Sélectionner une ou plusieurs classes</label>
                                    <select class="form-select" id="selected_classes" name="selected_classes[]" multiple>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs classes</small>
                                </div>
                                
                                <div class="mb-3" id="students_container" style="display: none;">
                                    <label for="selected_students" class="form-label">Sélectionner un ou plusieurs étudiants</label>
                                    <input type="text" class="form-control mb-2" id="student_search" placeholder="Rechercher un étudiant...">
                                    <select class="form-select" id="selected_students" name="selected_students[]" multiple size="8">
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->registration_number }})</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs étudiants</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Sujet</label>
                                    <input type="text" class="form-control" id="subject" name="subject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Message</label>
                                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Envoyer</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h5>Messages récents</h5>
                            <div class="list-group">
                                @forelse($recentMessages as $message)
                                    <a href="{{ route('messages.show', $message) }}" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $message->subject }}</h6>
                                            <small>{{ $message->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($message->content, 80) }}</p>
                                        <small>
                                            @if($message->isGroupMessage())
                                                Envoyé à: 
                                                @if($message->recipient_type == 'all')
                                                    Tous les utilisateurs
                                                @elseif($message->recipient_type == 'students')
                                                    Tous les étudiants
                                                @elseif($message->recipient_type == 'teachers')
                                                    Tous les enseignants
                                                @elseif($message->recipient_type == 'admins')
                                                    Tous les administrateurs
                                                @elseif($message->recipient_type == 'class')
                                                    Classe #{{ $message->recipient_group }}
                                                @elseif($message->recipient_type == 'department')
                                                    Département #{{ $message->recipient_group }}
                                                @endif
                                            @else
                                                Envoyé à: {{ $message->recipient->name ?? 'Destinataire inconnu' }}
                                            @endif
                                        </small>
                                    </a>
                                @empty
                                    <div class="list-group-item">
                                        <p class="mb-0">Aucun message récent</p>
                                    </div>
                                @endforelse
                                <div class="mt-3">
                                    <a href="{{ route('messages.inbox') }}" class="btn btn-outline-primary">Voir tous les messages</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        
        // Préparer les données pour le graphique
        const labels = @json($attendanceStats->pluck('attendance_date'));
        const presentData = @json($attendanceStats->pluck('present'));
        const totalData = @json($attendanceStats->pluck('total'));
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Présents',
                        data: presentData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total',
                        data: totalData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recipientTypeSelect = document.getElementById('recipient_type');
        const classesContainer = document.getElementById('classes_container');
        const studentsContainer = document.getElementById('students_container');
        const studentSearch = document.getElementById('student_search');
        const selectedStudents = document.getElementById('selected_students');
        
        // Afficher/masquer les conteneurs en fonction du type de destinataire
        recipientTypeSelect.addEventListener('change', function() {
            if (this.value === 'specific_classes') {
                classesContainer.style.display = 'block';
                studentsContainer.style.display = 'none';
            } else if (this.value === 'specific_students') {
                classesContainer.style.display = 'none';
                studentsContainer.style.display = 'block';
            } else {
                classesContainer.style.display = 'none';
                studentsContainer.style.display = 'none';
            }
        });
        
        // Fonction de recherche pour les étudiants
        if (studentSearch) {
            studentSearch.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const options = selectedStudents.options;
                
                for (let i = 0; i < options.length; i++) {
                    const optionText = options[i].text.toLowerCase();
                    if (optionText.includes(searchValue)) {
                        options[i].style.display = '';
                    } else {
                        options[i].style.display = 'none';
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection 