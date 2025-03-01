<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('dist/img/esbtp_logo.png') }}" alt="ESBTP Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                <small class="text-muted d-block">
                    @if(auth()->user()->hasRole('superAdmin'))
                        Super Admin
                    @elseif(auth()->user()->hasRole('secretaire'))
                        Secrétaire
                    @elseif(auth()->user()->hasRole('etudiant'))
                        Étudiant
                    @endif
                </small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Tableau de bord</p>
                    </a>
                </li>
                
                <!-- Structure académique - visible pour superAdmin et secretaire -->
                @if(auth()->user()->hasRole(['superAdmin', 'secretaire']))
                <li class="nav-item {{ request()->is('esbtp/filieres*', 'esbtp/niveaux-etudes*', 'esbtp/annees-universitaires*', 'esbtp/classes*', 'esbtp/matieres*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('esbtp/filieres*', 'esbtp/niveaux-etudes*', 'esbtp/annees-universitaires*', 'esbtp/classes*', 'esbtp/matieres*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-university"></i>
                        <p>
                            Structure académique
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Filières - visible pour superAdmin et secretaire (secretaire ne peut que voir) -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.filieres.index') }}" class="nav-link {{ request()->routeIs('esbtp.filieres.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Filières</p>
                            </a>
                        </li>
                        
                        <!-- Niveaux d'études - visible pour superAdmin et secretaire (secretaire ne peut que voir) -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="nav-link {{ request()->routeIs('esbtp.niveaux-etudes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Niveaux d'études</p>
                            </a>
                        </li>
                        
                        <!-- Années universitaires - visible pour superAdmin et secretaire (secretaire ne peut que voir) -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.annees-universitaires.index') }}" class="nav-link {{ request()->routeIs('esbtp.annees-universitaires.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Années universitaires</p>
                            </a>
                        </li>
                        
                        <!-- Classes - visible pour superAdmin et secretaire (secretaire ne peut que voir) -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.classes.index') }}" class="nav-link {{ request()->routeIs('esbtp.classes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Classes</p>
                            </a>
                        </li>
                        
                        <!-- Matières - visible pour superAdmin et secretaire (secretaire ne peut que voir) -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.matieres.index') }}" class="nav-link {{ request()->routeIs('esbtp.matieres.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Matières</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                
                <!-- Étudiants - visible pour superAdmin et secretaire -->
                @if(auth()->user()->hasRole(['superAdmin', 'secretaire']))
                <li class="nav-item {{ request()->is('esbtp/students*', 'esbtp/inscriptions*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('esbtp/students*', 'esbtp/inscriptions*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>
                            Étudiants
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Liste des étudiants - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.students.index') }}" class="nav-link {{ request()->routeIs('esbtp.students.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Liste des étudiants</p>
                            </a>
                        </li>
                        
                        <!-- Inscriptions - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.inscriptions.index') }}" class="nav-link {{ request()->routeIs('esbtp.inscriptions.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inscriptions</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                
                <!-- Examens - visible pour superAdmin et secretaire, l'étudiant ne voit que ses examens -->
                <li class="nav-item {{ request()->is('esbtp/exams*', 'esbtp/grades*', 'esbtp/bulletins*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('esbtp/exams*', 'esbtp/grades*', 'esbtp/bulletins*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>
                            Examens et Notes
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if(auth()->user()->hasRole(['superAdmin', 'secretaire']))
                        <!-- Gestion des examens - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.exams.index') }}" class="nav-link {{ request()->routeIs('esbtp.exams.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gestion des examens</p>
                            </a>
                        </li>
                        
                        <!-- Saisie des notes - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.grades.index') }}" class="nav-link {{ request()->routeIs('esbtp.grades.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Saisie des notes</p>
                            </a>
                        </li>
                        
                        <!-- Bulletins - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.bulletins.index') }}" class="nav-link {{ request()->routeIs('esbtp.bulletins.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Génération de bulletins</p>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('etudiant'))
                        <!-- Examens pour étudiant -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.exams.student') }}" class="nav-link {{ request()->routeIs('esbtp.exams.student') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mes examens</p>
                            </a>
                        </li>
                        
                        <!-- Notes pour étudiant -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.grades.student') }}" class="nav-link {{ request()->routeIs('esbtp.grades.student') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mes notes</p>
                            </a>
                        </li>
                        
                        <!-- Bulletin pour étudiant -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.bulletin.student') }}" class="nav-link {{ request()->routeIs('esbtp.bulletin.student') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mon bulletin</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                
                <!-- Présences - visible pour superAdmin, secretaire et étudiant (pour sa propre présence) -->
                <li class="nav-item {{ request()->is('esbtp/attendances*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('esbtp/attendances*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>
                            Présences
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if(auth()->user()->hasRole(['superAdmin', 'secretaire']))
                        <!-- Gestion des présences - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.attendances.index') }}" class="nav-link {{ request()->routeIs('esbtp.attendances.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gestion des présences</p>
                            </a>
                        </li>
                        
                        <!-- Saisie des présences - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.attendances.create') }}" class="nav-link {{ request()->routeIs('esbtp.attendances.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Saisie des présences</p>
                            </a>
                        </li>
                        
                        <!-- Rapport de présence - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.attendances.report') }}" class="nav-link {{ request()->routeIs('esbtp.attendances.report') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rapport de présence</p>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('etudiant'))
                        <!-- Présences de l'étudiant - visible pour l'étudiant -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.attendances.student') }}" class="nav-link {{ request()->routeIs('esbtp.attendances.student') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mes présences</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                
                <!-- Emplois du temps - visible pour tous -->
                <li class="nav-item {{ request()->is('esbtp/timetables*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('esbtp/timetables*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            Emplois du temps
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if(auth()->user()->hasRole(['superAdmin', 'secretaire']))
                        <!-- Gestion des emplois du temps - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.timetables.index') }}" class="nav-link {{ request()->routeIs('esbtp.timetables.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gestion des emplois du temps</p>
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->hasRole('etudiant'))
                        <!-- Emploi du temps de l'étudiant - visible pour l'étudiant -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.timetables.student') }}" class="nav-link {{ request()->routeIs('esbtp.timetables.student') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mon emploi du temps</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                
                <!-- Messagerie - visible pour tous -->
                <li class="nav-item {{ request()->is('messages*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('messages*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope"></i>
                        <p>
                            Messagerie
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('messages.inbox') }}" class="nav-link {{ request()->routeIs('messages.inbox') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Boîte de réception</p>
                            </a>
                        </li>
                        
                        @if(auth()->user()->hasRole(['superAdmin', 'secretaire']))
                        <!-- Envoyer des messages - visible pour superAdmin et secretaire -->
                        <li class="nav-item">
                            <a href="{{ route('messages.create') }}" class="nav-link {{ request()->routeIs('messages.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Envoyer un message</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                
                <!-- Administration - visible uniquement pour superAdmin -->
                @if(auth()->user()->hasRole('superAdmin'))
                <li class="nav-item {{ request()->is('users*', 'roles*', 'permissions*', 'settings*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('users*', 'roles*', 'permissions*', 'settings*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Administration
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Utilisateurs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Rôles et Permissions</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Paramètres du système</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                
                <!-- Profil utilisateur - visible pour tous -->
                <li class="nav-item">
                    <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Mon profil</p>
                    </a>
                </li>
                
                <!-- Déconnexion - visible pour tous -->
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Déconnexion</p>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside> 