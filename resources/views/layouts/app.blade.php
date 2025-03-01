<!-- 
    Layout principal de l'application ESBTP-yAKRO
    
    Ce fichier a été modifié pour :
    1. Corriger les routes non définies (erreurs 'Route [xxx] not defined')
    2. Organiser la barre latérale en fonction des rôles (superadmin, secretaire, enseignant, etudiant)
    3. Regrouper les fonctionnalités par catégories logiques
    
    Toutes les routes ont été alignées avec les contrôleurs existants et les préfixes 'esbtp.'
    Des vues pour les rôles, permissions et paramètres ont été ajoutées.
    
    Dernière mise à jour : 01/03/2025
-->

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
            min-height: 100vh;
            display: flex;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--esbtp-white);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: transform var(--transition-speed);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        
        .sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--esbtp-green);
            margin-bottom: 5px;
        }
        
        .sidebar-subtitle {
            font-size: 0.9rem;
            color: var(--esbtp-orange);
            font-weight: 500;
        }
        
        .sidebar-menu {
            padding: 20px;
        }
        
        .menu-category {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--esbtp-text);
            opacity: 0.6;
            margin: 20px 0 10px;
            font-weight: 600;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            color: var(--esbtp-text);
            border-radius: var(--border-radius);
            transition: all var(--transition-speed);
        }
        
        .nav-link:hover {
            background-color: var(--esbtp-light-green);
            color: var(--esbtp-green);
        }
        
        .nav-link.active {
            background-color: var(--esbtp-green);
            color: var(--esbtp-white);
        }
        
        .nav-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Styles */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            transition: margin var(--transition-speed);
        }
        
        .topbar {
            height: var(--topbar-height);
            background-color: var(--esbtp-white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        
        .toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--esbtp-text);
            cursor: pointer;
            margin-right: 15px;
        }
        
        .page-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--esbtp-dark);
            flex: 1;
        }
        
        .topbar-actions {
            display: flex;
            align-items: center;
        }
        
        .action-item {
            margin-left: 20px;
            position: relative;
        }
        
        .action-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--esbtp-text);
            cursor: pointer;
            transition: color var(--transition-speed);
        }
        
        .action-btn:hover {
            color: var(--esbtp-green);
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--esbtp-orange);
            color: var(--esbtp-white);
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--esbtp-dark);
        }
        
        .user-role {
            font-size: 0.8rem;
            color: var(--esbtp-text);
            opacity: 0.8;
        }
        
        .content {
            padding: 30px;
            flex: 1;
        }
        
        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.collapsed {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-left: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
        }
        
        /* Card Styles */
        .card {
            background-color: var(--esbtp-white);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--esbtp-white);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--esbtp-dark);
            margin: 0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Button Styles */
        .btn-primary {
            background-color: var(--esbtp-green);
            border-color: var(--esbtp-green);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--esbtp-green-dark);
            border-color: var(--esbtp-green-dark);
        }
        
        .btn-secondary {
            background-color: var(--esbtp-orange);
            border-color: var(--esbtp-orange);
        }
        
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: var(--esbtp-orange-dark);
            border-color: var(--esbtp-orange-dark);
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logo.png') }}" alt="ESBTP Logo" class="sidebar-logo">
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
                
                @role('superadmin')
                <div class="menu-category">Administration</div>
                <li class="nav-item">
                    <a href="{{ url('/users') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tag nav-icon"></i>
                        <span>Rôles et permissions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog nav-icon"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
                @endrole
                
                @hasanyrole('superadmin|secretaire')
                <div class="menu-category">Structure académique</div>
                <li class="nav-item">
                    <a href="{{ route('esbtp.filieres.index') }}" class="nav-link {{ request()->routeIs('esbtp.filieres.*') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram nav-icon"></i>
                        <span>Filières</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="nav-link {{ request()->routeIs('esbtp.niveaux-etudes.*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group nav-icon"></i>
                        <span>Niveaux d'études</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.annees-universitaires.index') }}" class="nav-link {{ request()->routeIs('esbtp.annees-universitaires.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt nav-icon"></i>
                        <span>Années universitaires</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.classes.index') }}" class="nav-link {{ request()->routeIs('esbtp.classes.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard nav-icon"></i>
                        <span>Classes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.salles.index') }}" class="nav-link {{ request()->routeIs('esbtp.salles.*') ? 'active' : '' }}">
                        <i class="fas fa-door-open nav-icon"></i>
                        <span>Salles de classe</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.matieres.index') }}" class="nav-link {{ request()->routeIs('esbtp.matieres.*') ? 'active' : '' }}">
                        <i class="fas fa-book nav-icon"></i>
                        <span>Matières</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.unites-enseignement.index') }}" class="nav-link {{ request()->routeIs('esbtp.unites-enseignement.*') ? 'active' : '' }}">
                        <i class="fas fa-cubes nav-icon"></i>
                        <span>Unités d'enseignement (UE)</span>
                    </a>
                </li>
                @endhasanyrole
                
                @hasanyrole('superadmin|secretaire')
                <div class="menu-category">Gestion des étudiants</div>
                <li class="nav-item">
                    <a href="{{ route('esbtp.etudiants.index') }}" class="nav-link {{ request()->routeIs('esbtp.etudiants.*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate nav-icon"></i>
                        <span>Étudiants</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.inscriptions.index') }}" class="nav-link {{ request()->routeIs('esbtp.inscriptions.*') ? 'active' : '' }}">
                        <i class="fas fa-user-plus nav-icon"></i>
                        <span>Inscriptions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link disabled">
                        <i class="fas fa-user-friends nav-icon"></i>
                        <span>Parents <small class="text-warning">(à venir)</small></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.paiements.index') }}" class="nav-link {{ request()->routeIs('esbtp.paiements.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave nav-icon"></i>
                        <span>Paiements</span>
                    </a>
                </li>
                @endhasanyrole

                @hasanyrole('superadmin|secretaire|enseignant')
                <div class="menu-category">Enseignement</div>
                @hasanyrole('superadmin|secretaire')
                <li class="nav-item">
                    <a href="{{ route('esbtp.enseignants.index') }}" class="nav-link {{ request()->routeIs('esbtp.enseignants.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher nav-icon"></i>
                        <span>Enseignants</span>
                    </a>
                </li>
                @endhasanyrole
                <li class="nav-item">
                    <a href="{{ route('esbtp.emplois-temps.index') }}" class="nav-link {{ request()->routeIs('esbtp.emplois-temps.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-week nav-icon"></i>
                        <span>Emplois du temps</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.presences.index') }}" class="nav-link {{ request()->routeIs('esbtp.presences.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check nav-icon"></i>
                        <span>Présences</span>
                    </a>
                </li>
                @endhasanyrole
                
                <div class="menu-category">Évaluations</div>
                @hasanyrole('superadmin|secretaire|enseignant')
                <li class="nav-item">
                    <a href="{{ route('esbtp.evaluations.index') }}" class="nav-link {{ request()->routeIs('esbtp.evaluations.*') ? 'active' : '' }}">
                        <i class="fas fa-pen-fancy nav-icon"></i>
                        <span>Évaluations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.notes.index') }}" class="nav-link {{ request()->routeIs('esbtp.notes.*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap nav-icon"></i>
                        <span>Notes</span>
                    </a>
                </li>
                @endhasanyrole
                
                <li class="nav-item">
                    <a href="{{ route('esbtp.bulletins.index') }}" class="nav-link {{ request()->routeIs('esbtp.bulletins.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <span>Bulletins</span>
                    </a>
                </li>
                
                @hasanyrole('superadmin|secretaire|enseignant')
                <li class="nav-item">
                    <a href="{{ route('esbtp.resultats.index') }}" class="nav-link {{ request()->routeIs('esbtp.resultats.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <span>Résultats</span>
                    </a>
                </li>
                @endhasanyrole
                
                <div class="menu-category">Communication</div>
                <li class="nav-item">
                    <a href="{{ route('esbtp.messages.index') }}" class="nav-link {{ request()->routeIs('esbtp.messages.*') ? 'active' : '' }}">
                        <i class="fas fa-envelope nav-icon"></i>
                        <span>Messagerie</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.notifications.index') }}" class="nav-link {{ request()->routeIs('esbtp.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell nav-icon"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                @hasanyrole('superadmin|secretaire')
                <li class="nav-item">
                    <a href="{{ route('esbtp.annonces.index') }}" class="nav-link {{ request()->routeIs('esbtp.annonces.*') ? 'active' : '' }}">
                        <i class="fas fa-bullhorn nav-icon"></i>
                        <span>Annonces</span>
                    </a>
                </li>
                @endhasanyrole
                
                @role('etudiant')
                <div class="menu-category">Mon espace</div>
                <li class="nav-item">
                    <a href="{{ route('esbtp.mon-profil.index') }}" class="nav-link {{ request()->routeIs('esbtp.mon-profil.*') ? 'active' : '' }}">
                        <i class="fas fa-user nav-icon"></i>
                        <span>Mon profil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.mes-notes.index') }}" class="nav-link {{ request()->routeIs('esbtp.mes-notes.*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap nav-icon"></i>
                        <span>Mes notes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.mes-absences.index') }}" class="nav-link {{ request()->routeIs('esbtp.mes-absences.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check nav-icon"></i>
                        <span>Mes absences</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.mes-paiements.index') }}" class="nav-link {{ request()->routeIs('esbtp.mes-paiements.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave nav-icon"></i>
                        <span>Mes paiements</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('esbtp.mon-emploi-temps.index') }}" class="nav-link {{ request()->routeIs('esbtp.mon-emploi-temps.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-week nav-icon"></i>
                        <span>Mon emploi du temps</span>
                    </a>
                </li>
                @endrole
            </ul>
        </div>
    </div>

    <div class="main-wrapper">
        <header class="topbar">
            <button id="toggle-sidebar" class="toggle-sidebar">
                <i class="fas fa-bars"></i>
            </button>
            
            <h1 class="page-title">@yield('page_title', 'Tableau de bord')</h1>
            
            <div class="topbar-actions">
                <div class="action-item">
                    <button class="action-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                </div>
                
                <div class="action-item">
                    <button class="action-btn">
                        <i class="fas fa-envelope"></i>
                        <span class="notification-badge">5</span>
                    </button>
                </div>
                
                <div class="action-item dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                        <img src="{{ asset('images/avatar.jpg') }}" alt="User Avatar" class="user-avatar">
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