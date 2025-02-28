<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
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
                
                <!-- Gestion académique -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-university"></i>
                        <p>
                            Gestion académique
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Années d'études -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Années d'études</p>
                            </a>
                        </li>
                        
                        <!-- Semestres -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Semestres</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Étudiants -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-graduate"></i>
                        <p>Étudiants</p>
                    </a>
                </li>
                
                <!-- Enseignants -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Enseignants</p>
                    </a>
                </li>
                
                <!-- Présences -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Présences</p>
                    </a>
                </li>
                
                <!-- Notes -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Notes</p>
                    </a>
                </li>
                
                <!-- Organisation -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-sitemap"></i>
                        <p>Organisation</p>
                    </a>
                </li>
                
                <!-- Emplois du temps -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Emplois du temps</p>
                    </a>
                </li>
                
                <!-- Notifications -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notifications</p>
                    </a>
                </li>
                
                <!-- Certificats -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-certificate"></i>
                        <p>Certificats</p>
                    </a>
                </li>
                
                <!-- ESBTP Module -->
                <li class="nav-item {{ request()->is('esbtp*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('esbtp*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>
                            ESBTP-YAKRO
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!-- Inscriptions -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.inscriptions.index') }}" class="nav-link {{ request()->routeIs('esbtp.inscriptions.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Inscriptions</p>
                            </a>
                        </li>
                        
                        <!-- Filières -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.filieres.index') }}" class="nav-link {{ request()->routeIs('esbtp.filieres.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Filières</p>
                            </a>
                        </li>
                        
                        <!-- Niveaux d'études -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.niveaux-etudes.index') }}" class="nav-link {{ request()->routeIs('esbtp.niveaux-etudes.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Niveaux d'études</p>
                            </a>
                        </li>
                        
                        <!-- Années universitaires -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.annees-universitaires.index') }}" class="nav-link {{ request()->routeIs('esbtp.annees-universitaires.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Années universitaires</p>
                            </a>
                        </li>
                        
                        <!-- Salles de classe -->
                        <li class="nav-item">
                            <a href="{{ route('esbtp.salles.index') }}" class="nav-link {{ request()->routeIs('esbtp.salles.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Salles de classe</p>
                            </a>
                        </li>
                        
                        <!-- Nouveaux éléments selon la demande -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Cycles de Formation</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Spécialités</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Partenariats</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Formation Continue</p>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Départements</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Paramètres -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Paramètres
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Profil</p>
                            </a>
                        </li>
                        <!-- Autres liens de paramètres -->
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside> 