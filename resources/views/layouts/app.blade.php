<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'ESBTP'))</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --esbtp-green: #01632f;
            --esbtp-green-dark: #014a23;
            --esbtp-green-light: #e6f3ee;
            --esbtp-orange: #f29400;
            --esbtp-orange-dark: #d98600;
            --esbtp-orange-light: #fff8eb;
            --esbtp-white: #ffffff;
            --esbtp-light-green: rgba(1, 99, 47, 0.1);
            --esbtp-light-orange: rgba(242, 148, 0, 0.1);
            --esbtp-gray: #f8f9fa;
            --esbtp-dark: #343a40;
            --esbtp-text: #495057;
            --sidebar-width: 280px;
            --topbar-height: 70px;
            --border-radius: 10px;
            --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            --transition-speed: 0.3s;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--esbtp-gray);
            color: var(--esbtp-text);
            overflow-x: hidden;
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Layout */
        .main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .content-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
        }
        
        @media (max-width: 992px) {
            .content-wrapper {
                margin-left: 0;
            }
            
            .sidebar.collapsed {
                transform: translateX(-100%);
            }
        }
        
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--esbtp-white);
            box-shadow: var(--card-shadow);
            z-index: 1000;
            transition: transform var(--transition-speed);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, var(--esbtp-green), var(--esbtp-green-dark));
            color: var(--esbtp-white);
            padding-top: 30px;
            padding-bottom: 30px;
        }
        
        .sidebar-logo {
            margin-bottom: 10px;
        }
        
        .sidebar-brand {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .sidebar-subtitle {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 15px 0;
        }
        
        .menu-category {
            padding: 0 20px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #a0a0a0;
            font-weight: 600;
        }
        
        .nav-item {
            margin: 2px 15px;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--esbtp-text);
            border-radius: var(--border-radius);
            transition: all var(--transition-speed);
            font-weight: 500;
        }
        
        .nav-link:hover {
            background-color: var(--esbtp-green-light);
            color: var(--esbtp-green);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, var(--esbtp-green), var(--esbtp-green-dark));
            color: var(--esbtp-white);
            box-shadow: 0 5px 15px rgba(1, 99, 47, 0.2);
        }
        
        .nav-icon {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            transition: all var(--transition-speed);
        }
        
        /* Topbar */
        .topbar {
            height: var(--topbar-height);
            background-color: var(--esbtp-white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: var(--esbtp-text);
            font-size: 1.3rem;
            cursor: pointer;
            margin-right: 20px;
            display: none;
        }
        
        @media (max-width: 992px) {
            .toggle-sidebar {
                display: block;
            }
        }
        
        .topbar-title {
            font-weight: 600;
            color: var(--esbtp-green);
            margin-bottom: 0;
            font-size: 1.3rem;
        }
        
        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
        }
        
        .notification-bell {
            position: relative;
            margin-right: 20px;
            color: var(--esbtp-text);
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--esbtp-orange);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--esbtp-green-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--esbtp-green);
            font-weight: 600;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--esbtp-dark);
        }
        
        .user-role {
            font-size: 0.75rem;
            color: #888;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 10px 0;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            color: var(--esbtp-text);
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: var(--esbtp-green-light);
            color: var(--esbtp-green);
        }
        
        .dropdown-divider {
            margin: 5px 0;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        /* Content area */
        .content {
            padding: 30px;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--esbtp-white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--esbtp-green);
            border-color: var(--esbtp-green);
        }
        
        .btn-primary:hover {
            background-color: var(--esbtp-green-dark);
            border-color: var(--esbtp-green-dark);
            box-shadow: 0 5px 15px rgba(1, 99, 47, 0.2);
        }
        
        .btn-secondary {
            background-color: var(--esbtp-orange);
            border-color: var(--esbtp-orange);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: var(--esbtp-orange-dark);
            border-color: var(--esbtp-orange-dark);
            box-shadow: 0 5px 15px rgba(242, 148, 0, 0.2);
        }
        
        /* Page transitions */
        .fade-enter-active, .fade-leave-active {
            transition: opacity 0.3s;
        }
        
        .fade-enter, .fade-leave-to {
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/esbtp_logo.png') }}" alt="ESBTP Logo" class="img-fluid" style="max-height: 60px;">
                </div>
                <div class="sidebar-brand">ESBTP-yAKRO</div>
                <div class="sidebar-subtitle">Gestion Universitaire</div>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-category">Navigation principale</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                    
                    <div class="menu-category">Gestion académique</div>
                    <li class="nav-item">
                        <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                            <i class="fas fa-user-graduate nav-icon"></i>
                            <span>Étudiants</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teachers.index') }}" class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                            <i class="fas fa-chalkboard-teacher nav-icon"></i>
                            <span>Enseignants</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                            <i class="fas fa-clipboard-check nav-icon"></i>
                            <span>Présences</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('grades.index') }}" class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap nav-icon"></i>
                            <span>Notes</span>
                        </a>
                    </li>
                    
                    <div class="menu-category">Organisation</div>
                    <li class="nav-item">
                        <a href="{{ route('timetables.index') }}" class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt nav-icon"></i>
                            <span>Emplois du temps</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <i class="fas fa-bell nav-icon"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('certificates.index') }}" class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}">
                            <i class="fas fa-certificate nav-icon"></i>
                            <span>Certificats</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main content -->
        <div class="content-wrapper">
            <!-- Topbar -->
            <header class="topbar">
                <button class="toggle-sidebar" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <h4 class="topbar-title">@yield('title', 'Tableau de bord')</h4>
                
                <div class="topbar-right">
                    <div class="notification-bell">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    
                    <div class="dropdown">
                        <div class="user-profile" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ Auth::user() ? substr(Auth::user()->name, 0, 1) : 'U' }}
                            </div>
                            <div class="user-info">
                                <div class="user-name">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                                <div class="user-role">{{ Auth::user()->role ?? 'Rôle' }}</div>
                            </div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Mon profil</a></li>
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
            </header>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar on mobile
            const toggleBtn = document.getElementById('toggle-sidebar');
            const sidebar = document.getElementById('sidebar');
            
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggleBtn = toggleBtn.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnToggleBtn && !sidebar.classList.contains('collapsed')) {
                        sidebar.classList.add('collapsed');
                    }
                }
            });
            
            // Adjust sidebar on window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('collapsed');
                }
            });
        });
    </script>
</body>
</html> 