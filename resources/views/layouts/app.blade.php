<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'ESBTP'))</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --esbtp-green: #01632f;
            --esbtp-orange: #f29400;
            --esbtp-white: #ffffff;
            --esbtp-light-green: rgba(1, 99, 47, 0.1);
            --esbtp-light-orange: rgba(242, 148, 0, 0.1);
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar styles */
        .sidebar {
            min-height: 100vh;
            background-color: var(--esbtp-green);
            color: var(--esbtp-white);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: block;
            padding: 12px 15px;
            border-radius: 5px;
            margin: 2px 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar a:hover {
            color: var(--esbtp-white);
            background-color: rgba(242, 148, 0, 0.2);
        }
        
        .sidebar .active {
            background-color: var(--esbtp-orange);
            color: var(--esbtp-white);
        }
        
        .sidebar-logo {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Content area */
        .content {
            padding: 20px;
        }
        
        /* Navbar */
        .navbar {
            background-color: var(--esbtp-white) !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--esbtp-green) !important;
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--esbtp-green);
            border-color: var(--esbtp-green);
        }
        
        .btn-primary:hover {
            background-color: #014a23;
            border-color: #014a23;
        }
        
        .btn-secondary {
            background-color: var(--esbtp-orange);
            border-color: var(--esbtp-orange);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #d98600;
            border-color: #d98600;
        }
        
        .btn-outline-primary {
            color: var(--esbtp-green);
            border-color: var(--esbtp-green);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--esbtp-green);
            color: white;
        }
        
        .btn-outline-secondary {
            color: var(--esbtp-orange);
            border-color: var(--esbtp-orange);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--esbtp-orange);
            color: white;
        }
        
        /* Cards */
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Nav icons */
        .nav-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* User dropdown */
        .user-dropdown {
            background-color: var(--esbtp-green);
            color: white;
            border: none;
        }
        
        .user-dropdown:hover, .user-dropdown:focus {
            background-color: #014a23;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4 sidebar-logo">
                        <img src="{{ asset('images/esbtp_logo.png') }}" alt="ESBTP Logo" class="img-fluid" style="max-height: 60px;">
                        <small>Gestion Universitaire</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt nav-icon"></i> Tableau de bord
                            </a>
                        </li>
                        
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                                <i class="fas fa-user-graduate nav-icon"></i> Étudiants
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                                <i class="fas fa-chalkboard-teacher nav-icon"></i> Enseignants
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('classes.index') }}" class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">
                                <i class="fas fa-school nav-icon"></i> Classes
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin() || Auth::user()->isTeacher())
                        <li class="nav-item">
                            <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check nav-icon"></i> Présences
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isStudent() && Auth::user()->student)
                        <li class="nav-item">
                            <a href="{{ route('attendance.student', Auth::user()->student->id) }}" class="nav-link {{ request()->routeIs('attendance.student') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check nav-icon"></i> Mes présences
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isStudent())
                        <li class="nav-item">
                            <a href="{{ route('justifications.index') }}" class="nav-link {{ request()->routeIs('justifications.*') ? 'active' : '' }}">
                                <i class="fas fa-file-medical nav-icon"></i> Justifications d'absence
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a href="{{ route('justifications.index') }}" class="nav-link {{ request()->routeIs('justifications.*') ? 'active' : '' }}">
                                <i class="fas fa-file-medical nav-icon"></i> Justifications d'absence
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdmin() || Auth::user()->isTeacher())
                        <li class="nav-item">
                            <a href="{{ route('grades.index') }}" class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap nav-icon"></i> Notes
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isStudent() && Auth::user()->student)
                        <li class="nav-item">
                            <a href="{{ route('grades.report', [Auth::user()->student->id]) }}" class="nav-link {{ request()->routeIs('grades.report') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap nav-icon"></i> Mes notes
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('timetables.index') }}" class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt nav-icon"></i> Emplois du temps
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isStudent())
                        <li class="nav-item">
                            <a href="{{ route('student.timetable') }}" class="nav-link {{ request()->routeIs('student.timetable') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt nav-icon"></i> Mon emploi du temps
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isTeacher() && Auth::user()->teacher)
                        <li class="nav-item">
                            <a href="{{ route('timetable.teacher', Auth::user()->teacher->id) }}" class="nav-link {{ request()->routeIs('timetable.teacher') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt nav-icon"></i> Mon emploi du temps
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                                <i class="fas fa-bell nav-icon"></i> Notifications
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item">
                            <a href="{{ route('messages.inbox') }}" class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                                <i class="fas fa-envelope nav-icon"></i> Messagerie
                            </a>
                        </li>
                        
                        @if(Auth::user()->isSuperAdmin())
                        <li class="nav-item">
                            <a href="{{ route('certificates.index') }}" class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                                <i class="fas fa-certificate nav-icon"></i> Certificats
                            </a>
                        </li>
                        @endif
                        
                        @if(Auth::user()->isStudent())
                        <li class="nav-item">
                            <a href="{{ route('student.certificates') }}" class="nav-link {{ request()->routeIs('student.certificates') ? 'active' : '' }}">
                                <i class="fas fa-certificate nav-icon"></i> Mes certificats
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <nav class="navbar navbar-expand-lg navbar-light mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target=".sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="d-flex justify-content-between w-100">
                            <h4 class="mb-0" style="color: var(--esbtp-green);">@yield('title', 'Tableau de bord')</h4>
                            <div class="dropdown">
                                <button class="btn user-dropdown dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name ?? 'Utilisateur' }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Paramètres</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html> 