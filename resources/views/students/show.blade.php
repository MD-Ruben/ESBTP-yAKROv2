@extends('layouts.app')

@section('title', 'Détails de l\'Étudiant')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Détails de l'Étudiant</h5>
                        <div>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning text-white">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations de base -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0">Informations Personnelles</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($student->profile_image)
                            <img src="{{ asset('storage/' . $student->profile_image) }}" alt="Photo de profil" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x"></i>
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $student->user->name ?? 'N/A' }}</h4>
                        <p class="text-muted">{{ $student->registration_number }}</p>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope text-muted me-2"></i> Email:</span>
                            <span>{{ $student->user->email ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-phone text-muted me-2"></i> Téléphone:</span>
                            <span>{{ $student->phone ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-birthday-cake text-muted me-2"></i> Date de naissance:</span>
                            <span>{{ $student->date_of_birth ? date('d/m/Y', strtotime($student->date_of_birth)) : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-venus-mars text-muted me-2"></i> Genre:</span>
                            <span>{{ $student->gender == 'M' ? 'Masculin' : ($student->gender == 'F' ? 'Féminin' : 'N/A') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-tint text-muted me-2"></i> Groupe sanguin:</span>
                            <span>{{ $student->blood_group ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <span><i class="fas fa-map-marker-alt text-muted me-2"></i> Adresse:</span>
                            <p class="mt-1 mb-0">{{ $student->address ?? 'N/A' }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Informations académiques -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-esbtp-orange text-white">
                    <h5 class="card-title mb-0">Informations Académiques</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-graduation-cap text-muted me-2"></i> Classe:</span>
                            <span>{{ $student->class->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-layer-group text-muted me-2"></i> Section:</span>
                            <span>{{ $student->section->name ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-check text-muted me-2"></i> Date d'admission:</span>
                            <span>{{ $student->admission_date ? date('d/m/Y', strtotime($student->admission_date)) : 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-school text-muted me-2"></i> École précédente:</span>
                            <span>{{ $student->previous_school ?? 'N/A' }}</span>
                        </li>
                    </ul>

                    <div class="mt-4">
                        <h6 class="border-bottom pb-2"><i class="fas fa-chart-line me-2"></i> Statistiques</h6>
                        <div class="row mt-3">
                            <div class="col-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center py-3">
                                        <h3 class="mb-0">{{ $student->attendance_percentage ?? '0' }}%</h3>
                                        <small class="text-muted">Présence</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center py-3">
                                        <h3 class="mb-0">{{ $student->average_grade ?? 'N/A' }}</h3>
                                        <small class="text-muted">Moyenne</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('grades.report', $student->id) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-file-alt me-1"></i> Voir le bulletin de notes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du parent -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-esbtp-green text-white">
                    <h5 class="card-title mb-0">Informations du Parent</h5>
                </div>
                <div class="card-body">
                    @if($student->parent)
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                <i class="fas fa-user-tie fa-3x text-muted"></i>
                            </div>
                            <h5 class="mt-3">{{ $student->parent->user->name ?? 'N/A' }}</h5>
                            <p class="text-muted">Parent / Tuteur</p>
                        </div>

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope text-muted me-2"></i> Email:</span>
                                <span>{{ $student->parent->user->email ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-phone text-muted me-2"></i> Téléphone:</span>
                                <span>{{ $student->parent->phone ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-briefcase text-muted me-2"></i> Profession:</span>
                                <span>{{ $student->parent->occupation ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item">
                                <span><i class="fas fa-map-marker-alt text-muted me-2"></i> Adresse:</span>
                                <p class="mt-1 mb-0">{{ $student->parent->address ?? 'N/A' }}</p>
                            </li>
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fa-4x text-muted mb-3"></i>
                            <p>Aucun parent associé à cet étudiant.</p>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-link me-1"></i> Associer un parent
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Onglets pour les détails supplémentaires -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="studentDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="true">
                                <i class="fas fa-clipboard-check me-1"></i> Présences
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab" aria-controls="grades" aria-selected="false">
                                <i class="fas fa-graduation-cap me-1"></i> Notes
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="timetable-tab" data-bs-toggle="tab" data-bs-target="#timetable" type="button" role="tab" aria-controls="timetable" aria-selected="false">
                                <i class="fas fa-calendar-alt me-1"></i> Emploi du temps
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab" aria-controls="certificates" aria-selected="false">
                                <i class="fas fa-certificate me-1"></i> Certificats
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-3" id="studentDetailTabsContent">
                        <!-- Onglet Présences -->
                        <div class="tab-pane fade show active" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                            <h5 class="border-bottom pb-2 mb-3">Historique des présences</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Remarque</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances ?? [] as $attendance)
                                            <tr>
                                                <td>{{ date('d/m/Y', strtotime($attendance->date)) }}</td>
                                                <td>
                                                    @if($attendance->status == 'present')
                                                        <span class="badge bg-success">Présent</span>
                                                    @elseif($attendance->status == 'absent')
                                                        <span class="badge bg-danger">Absent</span>
                                                    @elseif($attendance->status == 'late')
                                                        <span class="badge bg-warning text-dark">En retard</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $attendance->remark ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Aucune donnée de présence disponible</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $attendances->links() ?? '' }}
                            </div>
                        </div>
                        
                        <!-- Onglet Notes -->
                        <div class="tab-pane fade" id="grades" role="tabpanel" aria-labelledby="grades-tab">
                            <h5 class="border-bottom pb-2 mb-3">Notes récentes</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Matière</th>
                                            <th>Note</th>
                                            <th>Coefficient</th>
                                            <th>Semestre</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($grades ?? [] as $grade)
                                            <tr>
                                                <td>{{ $grade->subject->name ?? 'N/A' }}</td>
                                                <td>{{ $grade->score }}</td>
                                                <td>{{ $grade->coefficient }}</td>
                                                <td>{{ $grade->semester->name ?? 'N/A' }}</td>
                                                <td>{{ date('d/m/Y', strtotime($grade->created_at)) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Aucune note disponible</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $grades->links() ?? '' }}
                            </div>
                        </div>
                        
                        <!-- Onglet Emploi du temps -->
                        <div class="tab-pane fade" id="timetable" role="tabpanel" aria-labelledby="timetable-tab">
                            <h5 class="border-bottom pb-2 mb-3">Emploi du temps de la classe</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="15%">Horaire</th>
                                            <th>Lundi</th>
                                            <th>Mardi</th>
                                            <th>Mercredi</th>
                                            <th>Jeudi</th>
                                            <th>Vendredi</th>
                                            <th>Samedi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($timetableSlots ?? [] as $slot => $days)
                                            <tr>
                                                <td class="fw-bold">{{ $slot }}</td>
                                                @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
                                                    <td>
                                                        @if(isset($days[$day]))
                                                            <div class="p-2 rounded bg-light">
                                                                <div class="fw-bold">{{ $days[$day]['subject'] ?? 'N/A' }}</div>
                                                                <small>{{ $days[$day]['teacher'] ?? 'N/A' }}</small>
                                                            </div>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Aucun emploi du temps disponible</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Onglet Certificats -->
                        <div class="tab-pane fade" id="certificates" role="tabpanel" aria-labelledby="certificates-tab">
                            <h5 class="border-bottom pb-2 mb-3">Certificats délivrés</h5>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Date de délivrance</th>
                                            <th>Référence</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($certificates ?? [] as $certificate)
                                            <tr>
                                                <td>{{ $certificate->type->name ?? 'N/A' }}</td>
                                                <td>{{ date('d/m/Y', strtotime($certificate->issue_date)) }}</td>
                                                <td>{{ $certificate->reference_number }}</td>
                                                <td>
                                                    <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-download"></i> Télécharger
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Aucun certificat délivré</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $certificates->links() ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 