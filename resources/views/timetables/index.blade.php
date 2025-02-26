@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Emplois du temps') }}</h5>
                    <a href="{{ route('timetables.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Ajouter') }}
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filtres -->
                    <form action="{{ route('timetables.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="class_id">{{ __('Classe') }}</label>
                                    <select name="class_id" id="class_id" class="form-control">
                                        <option value="">{{ __('Toutes les classes') }}</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="section_id">{{ __('Section') }}</label>
                                    <select name="section_id" id="section_id" class="form-control">
                                        <option value="">{{ __('Toutes les sections') }}</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="day">{{ __('Jour') }}</label>
                                    <select name="day" id="day" class="form-control">
                                        <option value="">{{ __('Tous les jours') }}</option>
                                        <option value="monday" {{ request('day') == 'monday' ? 'selected' : '' }}>{{ __('Lundi') }}</option>
                                        <option value="tuesday" {{ request('day') == 'tuesday' ? 'selected' : '' }}>{{ __('Mardi') }}</option>
                                        <option value="wednesday" {{ request('day') == 'wednesday' ? 'selected' : '' }}>{{ __('Mercredi') }}</option>
                                        <option value="thursday" {{ request('day') == 'thursday' ? 'selected' : '' }}>{{ __('Jeudi') }}</option>
                                        <option value="friday" {{ request('day') == 'friday' ? 'selected' : '' }}>{{ __('Vendredi') }}</option>
                                        <option value="saturday" {{ request('day') == 'saturday' ? 'selected' : '' }}>{{ __('Samedi') }}</option>
                                        <option value="sunday" {{ request('day') == 'sunday' ? 'selected' : '' }}>{{ __('Dimanche') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> {{ __('Filtrer') }}
                                </button>
                                <a href="{{ route('timetables.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-sync"></i> {{ __('Réinitialiser') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tableau des emplois du temps -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Classe') }}</th>
                                    <th>{{ __('Section') }}</th>
                                    <th>{{ __('Jour') }}</th>
                                    <th>{{ __('Horaire') }}</th>
                                    <th>{{ __('Matière') }}</th>
                                    <th>{{ __('Enseignant') }}</th>
                                    <th>{{ __('Salle') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($timetables as $timetable)
                                    <tr>
                                        <td>{{ $timetable->class->name }}</td>
                                        <td>{{ $timetable->section->name }}</td>
                                        <td>
                                            @switch($timetable->day_of_week)
                                                @case('monday')
                                                    {{ __('Lundi') }}
                                                    @break
                                                @case('tuesday')
                                                    {{ __('Mardi') }}
                                                    @break
                                                @case('wednesday')
                                                    {{ __('Mercredi') }}
                                                    @break
                                                @case('thursday')
                                                    {{ __('Jeudi') }}
                                                    @break
                                                @case('friday')
                                                    {{ __('Vendredi') }}
                                                    @break
                                                @case('saturday')
                                                    {{ __('Samedi') }}
                                                    @break
                                                @case('sunday')
                                                    {{ __('Dimanche') }}
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($timetable->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($timetable->end_time)->format('H:i') }}</td>
                                        <td>{{ $timetable->subject->name }}</td>
                                        <td>{{ $timetable->teacher->user->name ?? 'N/A' }}</td>
                                        <td>{{ $timetable->room_number ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('timetables.show', $timetable) }}" class="btn btn-info btn-sm" title="{{ __('Voir') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('timetables.edit', $timetable) }}" class="btn btn-primary btn-sm" title="{{ __('Modifier') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('timetables.destroy', $timetable) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cet emploi du temps ?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Supprimer') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('Aucun emploi du temps trouvé') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $timetables->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script pour charger dynamiquement les sections en fonction de la classe sélectionnée
    $(document).ready(function() {
        $('#class_id').change(function() {
            var classId = $(this).val();
            if (classId) {
                $.ajax({
                    url: '/api/sections/by-class/' + classId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#section_id').empty();
                        $('#section_id').append('<option value="">{{ __("Toutes les sections") }}</option>');
                        $.each(data, function(key, value) {
                            $('#section_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#section_id').empty();
                $('#section_id').append('<option value="">{{ __("Toutes les sections") }}</option>');
            }
        });
    });
</script>
@endsection 