<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLASSCI - École Spéciale du Bâtiment et des Travaux Publics</title>

        <!-- Polices Google -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- AOS - Animation On Scroll -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <style>
        :root {
            /* Palette de couleurs */
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: rgba(37, 99, 235, 0.1);
            --secondary: #f97316;
            --secondary-dark: #ea580c;
            --secondary-light: rgba(249, 115, 22, 0.1);
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --light: #f8fafc;
            --success: #22c55e;
            --warning: #eab308;
            --danger: #ef4444;
            --info: #06b6d4;

            /* Espacement */
            --spacing-xs: 0.5rem;
            --spacing-sm: 1rem;
            --spacing-md: 1.5rem;
            --spacing-lg: 2rem;
            --spacing-xl: 3rem;
            --spacing-2xl: 5rem;

            /* Bordures */
            --border-radius-sm: 0.25rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 1rem;
            --border-radius-xl: 2rem;
            --border-radius-full: 9999px;
        }

        /* Base */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            overflow-x: hidden;
            background-color: var(--light);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            line-height: 1.2;
        }

        a {
            text-decoration: none;
            color: var(--primary);
            transition: all 0.3s ease;
        }

        a:hover {
            color: var(--primary-dark);
        }

        .section {
            padding: var(--spacing-2xl) 0;
            position: relative;
        }

        .subtitle {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: var(--spacing-xs);
            display: inline-block;
            font-size: 1rem;
        }

        .section-title {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-md);
            color: var(--dark);
        }

        .section-description {
            font-size: 1.125rem;
            color: var(--gray);
            margin-bottom: var(--spacing-lg);
            max-width: 800px;
        }

        /* Boutons */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-full);
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary {
            background-color: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            border-color: var(--secondary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.7);
            color: white;
            background-color: transparent;
        }

        .btn-outline-light:hover {
            background-color: white;
            color: var(--primary);
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        /* Navigation */
        .navbar {
            padding: 1rem 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .navbar-brand img {
            height: 48px;
            transition: all 0.3s ease;
        }

        .navbar-scrolled {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 0.75rem 0;
        }

        .navbar-scrolled .navbar-brand img {
            height: 40px;
        }

        .navbar-scrolled .nav-link {
            color: var(--dark);
        }

        .navbar-scrolled .nav-link:hover, 
        .navbar-scrolled .nav-link.active {
            color: var(--primary);
        }

        .nav-link {
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            position: relative;
        }

        .nav-link:hover, .nav-link.active {
            color: rgba(255, 255, 255, 0.8);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: var(--secondary);
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after, 
        .nav-link.active::after {
            width: 30px;
        }

        .navbar-toggler {
            border: none;
            padding: 0;
            outline: none !important;
            box-shadow: none !important;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            transition: all 0.3s ease;
        }

        .navbar-scrolled .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(15, 23, 42, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .nav-cta {
            background-color: var(--secondary);
            color: white !important;
            border-radius: var(--border-radius-full);
            padding: 0.5rem 1.5rem;
            margin-left: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.3), 0 2px 4px -1px rgba(249, 115, 22, 0.06);
        }

        .nav-cta:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.2), 0 4px 6px -2px rgba(249, 115, 22, 0.1);
        }

        .nav-cta::after {
            display: none;
        }

        /* Hero Section */
            .hero {
                position: relative;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.8) 100%), 
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
                background-size: cover;
            background-position: center;
                background-attachment: fixed;
            min-height: 100vh;
                display: flex;
                align-items: center;
            padding: 5rem 0;
                color: white;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-subtitle {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 1rem;
            display: inline-block;
            font-size: 1.25rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-description {
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            max-width: 600px;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Décorations Hero */
        .hero-shape {
            position: absolute;
            bottom: -5%;
            left: 0;
            width: 100%;
            height: 10rem;
            background-color: white;
            clip-path: polygon(0 60%, 100% 0, 100% 100%, 0% 100%);
                z-index: 1;
            }

        .hero-circle {
            position: absolute;
            height: 300px;
            width: 300px;
            border-radius: 50%;
        }

        .hero-circle-1 {
            top: 15%;
            right: -5%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0) 70%);
            animation: float 10s ease-in-out infinite;
        }

        .hero-circle-2 {
            bottom: 15%;
            left: -5%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.1) 0%, rgba(249, 115, 22, 0) 70%);
            animation: float 14s ease-in-out infinite reverse;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }

        .hero-stats {
            margin-top: 4rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .hero-stat {
            text-align: center;
            min-width: 140px;
        }

        .hero-stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: block;
            color: var(--secondary);
        }

        .hero-stat-label {
            font-size: 1rem;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero {
                min-height: 90vh;
            }
        }

        @media (max-width: 767.98px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-description {
                font-size: 1.125rem;
            }

            .hero-stats {
                justify-content: center;
            }
        }

        @media (max-width: 575.98px) {
            .hero-title {
                font-size: 1.75rem;
            }

            .nav-cta {
                margin-left: 0;
                margin-top: 0.5rem;
            }
        }

        .rounded-xl {
            border-radius: var(--border-radius-lg);
        }
        
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .about-image::before {
                content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--secondary-light);
            top: -20px;
            left: -20px;
            z-index: -1;
        }
        
        .about-image::after {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 20px;
            border: 5px solid var(--primary-light);
            bottom: -30px;
            left: -30px;
            z-index: -1;
        }

        /* Styles pour la section formations */
        .formations-filter .nav-link {
            color: var(--dark);
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            margin: 0 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        
        .formations-filter .nav-link.active {
            background-color: var(--primary);
            color: white;
        }
        
        .formations-filter .nav-link:not(.active):hover {
            background-color: var(--primary-light);
        }
        
        .formation-image {
            overflow: hidden;
            height: 200px;
        }
        
        .formation-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.6s ease;
        }
        
        .formation-card:hover .formation-image img {
            transform: scale(1.1);
        }
        
        .formation-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .formation-card:hover .formation-overlay {
            opacity: 1;
            }

        .formation-card {
                transition: all 0.3s ease;
            }

        .formation-card:hover {
                transform: translateY(-10px);
            }

        /* Styles pour la section témoignages */
        .testimonials {
            background-color: var(--light);
            position: relative;
            overflow: hidden;
        }
        
        .testimonials::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background-color: rgba(99, 102, 241, 0.05);
            top: -150px;
            right: -150px;
            z-index: 0;
        }
        
        .testimonials::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background-color: rgba(236, 72, 153, 0.05);
            bottom: -100px;
            left: -100px;
            z-index: 0;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
                transition: all 0.3s ease;
            }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

        .testimonial-quote {
            font-size: 3rem;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 1rem;
            opacity: 0.3;
            }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        
        .testimonial-author-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 1rem;
        }
        
        .testimonial-author-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .testimonial-author-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }
        
        .testimonial-author-role {
            font-size: 0.9rem;
            color: var(--secondary);
            }
        </style>
    </head>
    <body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                <img src="{{ asset('images/LOGO-KLASSCI-PNG.png') }}" alt="KLASSCI Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                        <a class="nav-link active" href="#home">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">À propos</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link" href="#formations">Formations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link nav-cta" href="{{ route('login') }}">Connexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
            <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-right" data-aos-duration="1000">
                    <div class="hero-content">
                        <span class="hero-subtitle">L'excellence dans la formation technique</span>
                        <h1 class="hero-title">Former la prochaine génération de professionnels du bâtiment</h1>
                        <p class="hero-description">L'École Spéciale du Bâtiment et des Travaux Publics vous offre un enseignement de qualité, dispensé par des professionnels expérimentés.</p>
                    <div class="hero-buttons">
                            <a href="#formations" class="btn btn-secondary btn-lg">Découvrir nos formations</a>
                            <a href="#contact" class="btn btn-outline-light btn-lg">Nous contacter</a>
                    </div>
                        
                        <div class="hero-stats">
                            <div class="hero-stat">
                                <span class="hero-stat-value" data-count="15">15</span>
                                <span class="hero-stat-label">Années d'excellence</span>
                </div>
                            <div class="hero-stat">
                                <span class="hero-stat-value" data-count="5000">5000+</span>
                                <span class="hero-stat-label">Diplômés</span>
            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-value" data-count="25">25</span>
                                <span class="hero-stat-label">Partenaires industriels</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left" data-aos-duration="1000">
                    <!-- Espace réservé pour une éventuelle illustration ou vidéo -->
                </div>
            </div>
        </div>
        
        <!-- Éléments décoratifs -->
        <div class="hero-shape"></div>
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
        </section>

        <!-- Section À propos -->
    <section id="about" class="section">
            <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <div class="about-image position-relative">
                        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1300&q=80" alt="Chantier de construction" class="img-fluid rounded-xl shadow-lg" style="width: 100%;">
                        <div class="about-badge position-absolute" style="bottom: -20px; right: 30px; background-color: white; border-radius: var(--border-radius-lg); padding: 1rem; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);">
                            <div class="d-flex align-items-center">
                                <div class="badge-icon" style="background-color: var(--primary-light); color: var(--primary); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                    <i class="fas fa-award fa-lg"></i>
                        </div>
                                <div>
                                    <span class="d-block fw-bold" style="color: var(--dark);">15+ Années</span>
                                    <span style="color: var(--gray);">d'excellence</span>
                    </div>
                            </div>
                                        </div>
                                        </div>
                                    </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                    <div class="ps-lg-5 mt-5 mt-lg-0">
                        <span class="subtitle">À propos de l'ESBTP</span>
                        <h2 class="section-title">Former la prochaine génération de professionnels du bâtiment</h2>
                        <p class="section-description">L'École Spéciale du Bâtiment et des Travaux Publics (ESBTP) de Yamoussoukro est un établissement d'enseignement supérieur spécialisé dans la formation aux métiers du bâtiment et des travaux publics. Notre mission est de former des professionnels qualifiés capables de répondre aux défis du secteur de la construction en Côte d'Ivoire et en Afrique.</p>
                        
                        <div class="features-grid row g-4 mt-4">
                            <div class="col-md-6">
                                <div class="feature-card p-4 rounded-lg h-100" style="background-color: var(--primary-light); border-left: 3px solid var(--primary);">
                                    <div class="feature-icon mb-3" style="color: var(--primary);">
                                        <i class="fas fa-graduation-cap fa-2x"></i>
                                </div>
                                    <h5 class="feature-title">Enseignement de qualité</h5>
                                    <p class="feature-text mb-0" style="color: var(--gray);">Des professeurs expérimentés et des méthodes pédagogiques innovantes.</p>
                                        </div>
                                        </div>
                            <div class="col-md-6">
                                <div class="feature-card p-4 rounded-lg h-100" style="background-color: var(--secondary-light); border-left: 3px solid var(--secondary);">
                                    <div class="feature-icon mb-3" style="color: var(--secondary);">
                                        <i class="fas fa-cogs fa-2x"></i>
                                    </div>
                                    <h5 class="feature-title">Équipements modernes</h5>
                                    <p class="feature-text mb-0" style="color: var(--gray);">Des laboratoires et ateliers équipés des dernières technologies.</p>
                                </div>
                                        </div>
                            <div class="col-md-6">
                                <div class="feature-card p-4 rounded-lg h-100" style="background-color: var(--primary-light); border-left: 3px solid var(--primary);">
                                    <div class="feature-icon mb-3" style="color: var(--primary);">
                                        <i class="fas fa-handshake fa-2x"></i>
                                        </div>
                                    <h5 class="feature-title">Partenariats professionnels</h5>
                                    <p class="feature-text mb-0" style="color: var(--gray);">Des liens étroits avec les entreprises du secteur pour des stages et emplois.</p>
                                    </div>
                                </div>
                            <div class="col-md-6">
                                <div class="feature-card p-4 rounded-lg h-100" style="background-color: var(--secondary-light); border-left: 3px solid var(--secondary);">
                                    <div class="feature-icon mb-3" style="color: var(--secondary);">
                                        <i class="fas fa-certificate fa-2x"></i>
                                        </div>
                                    <h5 class="feature-title">Diplômes reconnus</h5>
                                    <p class="feature-text mb-0" style="color: var(--gray);">Des formations certifiées et reconnues au niveau national et international.</p>
                                        </div>
                                    </div>
                                </div>
                        
                        <div class="mt-5">
                            <a href="#formations" class="btn btn-primary">Découvrir nos programmes <i class="fas fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Section Formations -->
    <section id="formations" class="section bg-light">
            <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="subtitle">Nos programmes académiques</span>
                <h2 class="section-title">Formations adaptées aux besoins du marché</h2>
                <p class="section-description mx-auto">Découvrez nos différentes filières d'études dans le domaine du bâtiment et des travaux publics, conçues pour vous préparer aux défis du monde professionnel.</p>
            </div>
            
            <!-- Filtres de formations -->
            <div class="formations-filter mb-5" data-aos="fade-up">
                <ul class="nav nav-pills justify-content-center flex-wrap">
                    <li class="nav-item">
                        <a class="nav-link active" data-filter="all" href="#">Toutes les formations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="licence" href="#">Licences</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="master" href="#">Masters</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-filter="court" href="#">Formations courtes</a>
                    </li>
                </ul>
                </div>

                <div class="row g-4">
                <!-- Formation 1 -->
                <div class="col-lg-4 col-md-6 formation-item" data-category="licence" data-aos="fade-up" data-aos-delay="100">
                    <div class="formation-card card border-0 shadow-sm h-100">
                        <div class="formation-image position-relative">
                            <img src="https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1400&q=80" class="card-img-top" alt="Génie Civil">
                            <div class="formation-overlay d-flex align-items-center justify-content-center">
                                <a href="#" class="btn btn-light btn-sm">Voir détails</a>
                                </div>
                            <div class="badge bg-primary position-absolute" style="top: 20px; right: 20px;">Licence</div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Génie Civil</h5>
                                <p class="card-text">Formation en conception, analyse et réalisation d'infrastructures civiles comme les ponts, routes et bâtiments.</p>
                            <hr>
                            <div class="formation-details">
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">Durée</small>
                                                <p class="mb-0 small">3 ans</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">Niveau requis</small>
                                                <p class="mb-0 small">Bac</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">Places</small>
                                                <p class="mb-0 small">40 disponibles</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">Début</small>
                                                <p class="mb-0 small">Septembre 2023</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 p-4">
                            <a href="#" class="btn btn-primary w-100">Plus de détails</a>
                            </div>
                        </div>
                    </div>

                <!-- Formation 2 -->
                <div class="col-lg-4 col-md-6 formation-item" data-category="licence" data-aos="fade-up" data-aos-delay="200">
                    <div class="formation-card card border-0 shadow-sm h-100">
                        <div class="formation-image position-relative">
                            <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="card-img-top" alt="Travaux Publics">
                            <div class="formation-overlay d-flex align-items-center justify-content-center">
                                <a href="#" class="btn btn-light btn-sm">Voir détails</a>
                                </div>
                            <div class="badge bg-primary position-absolute" style="top: 20px; right: 20px;">Licence</div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Travaux Publics</h5>
                                <p class="card-text">Spécialisation dans la construction d'infrastructures publiques comme les autoroutes, barrages et aéroports.</p>
                            <hr>
                            <div class="formation-details">
                                <div class="row g-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock"></i>
                            </div>
                                            <div>
                                                <small class="text-muted">Durée</small>
                                                <p class="mb-0 small">3 ans</p>
                        </div>
                    </div>
                                </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-graduation-cap"></i>
                            </div>
                                            <div>
                                                <small class="text-muted">Niveau requis</small>
                                                <p class="mb-0 small">Bac</p>
                        </div>
                    </div>
                                </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-users"></i>
                            </div>
                                            <div>
                                                <small class="text-muted">Places</small>
                                                <p class="mb-0 small">35 disponibles</p>
                        </div>
                            </div>
                                </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-calendar-alt"></i>
                            </div>
                                            <div>
                                                <small class="text-muted">Début</small>
                                                <p class="mb-0 small">Septembre 2023</p>
                        </div>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="card-footer bg-white border-0 p-4">
                            <a href="#" class="btn btn-primary w-100">Plus de détails</a>
                </div>
            </div>
                </div>

                <!-- Formation 3 -->
                <div class="col-lg-4 col-md-6 formation-item" data-category="licence" data-aos="fade-up" data-aos-delay="300">
                    <div class="formation-card card border-0 shadow-sm h-100">
                        <div class="formation-image position-relative">
                            <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1300&q=80" class="card-img-top" alt="Architecture">
                            <div class="formation-overlay d-flex align-items-center justify-content-center">
                                <a href="#" class="btn btn-light btn-sm">Voir détails</a>
                        </div>
                            <div class="badge bg-primary position-absolute" style="top: 20px; right: 20px;">Licence</div>
                    </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Architecture</h5>
                            <p class="card-text">Formation en conception architecturale, design d'intérieur et planification urbaine pour créer des espaces fonctionnels.</p>
                            <hr>
                            <div class="formation-details">
                        <div class="row g-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-clock"></i>
                                                </div>
                                            <div>
                                                <small class="text-muted">Durée</small>
                                                <p class="mb-0 small">3 ans</p>
                                            </div>
                                                </div>
                                            </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-graduation-cap"></i>
                                                </div>
                                            <div>
                                                <small class="text-muted">Niveau requis</small>
                                                <p class="mb-0 small">Bac</p>
                                            </div>
                                                </div>
                                            </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">Places</small>
                                                <p class="mb-0 small">30 disponibles</p>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-2" style="min-width: 30px; width: 30px; height: 30px; border-radius: 50%; background-color: var(--secondary-light); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted">Début</small>
                                                <p class="mb-0 small">Septembre 2023</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 p-4">
                            <a href="#" class="btn btn-primary w-100">Plus de détails</a>
                        </div>
                    </div>
                                </div>
                            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <a href="#" class="btn btn-outline-primary btn-lg">Voir toutes nos formations</a>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section id="testimonials" class="section bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h6 class="subtitle text-primary fw-bold mb-2" data-aos="fade-up">Témoignages</h6>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Ce que disent nos étudiants et diplômés</h2>
                    <p class="text-muted" data-aos="fade-up" data-aos-delay="200">
                        Découvrez les expériences de ceux qui ont choisi KLASSCI pour leur formation dans le domaine du bâtiment et des travaux publics.
                    </p>
                </div>
            </div>
            
            <div class="testimonial-slider position-relative" data-aos="fade-up" data-aos-delay="300">
                                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card bg-white p-4 rounded-4 shadow-sm h-100">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                                </div>
                            <p class="testimonial-text mb-4">
                                "La formation en Génie Civil à KLASSCI a été un véritable tremplin pour ma carrière. Les professeurs sont des professionnels du secteur qui partagent leur expérience concrète, et les projets pratiques m'ont permis d'acquérir des compétences directement applicables sur le terrain."
                            </p>
                            <div class="testimonial-user d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <img src="https://randomuser.me/api/portraits/women/42.jpg" alt="Témoignage" class="rounded-circle" width="60" height="60">
                                            </div>
                                <div class="user-info">
                                    <h6 class="mb-0">Sophie Kamga</h6>
                                    <span class="small text-muted">Diplômée en Génie Civil 2022</span>
                                        </div>
                                                </div>
                                            </div>
                                        </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card bg-white p-4 rounded-4 shadow-sm h-100">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                                </div>
                            <p class="testimonial-text mb-4">
                                "J'ai choisi KLASSCI pour sa réputation d'excellence, et je n'ai pas été déçu. La qualité de l'enseignement, les équipements de pointe et le réseau de partenaires professionnels m'ont ouvert des portes dès l'obtention de mon diplôme. Aujourd'hui, je dirige ma propre entreprise d'architecture."
                            </p>
                            <div class="testimonial-user d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Témoignage" class="rounded-circle" width="60" height="60">
                                </div>
                                <div class="user-info">
                                    <h6 class="mb-0">Jean-Paul Mbarga</h6>
                                    <span class="small text-muted">Diplômé en Architecture 2020</span>
                                </div>
                            </div>
                                            </div>
                                        </div>

                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="testimonial-card bg-white p-4 rounded-4 shadow-sm h-100">
                            <div class="testimonial-rating mb-3">
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star text-warning"></i>
                                <i class="fas fa-star-half-alt text-warning"></i>
                                                </div>
                            <p class="testimonial-text mb-4">
                                "En tant qu'étudiante actuelle en Travaux Publics, je suis impressionnée par la pédagogie innovante et l'accompagnement personnalisé offerts par KLASSCI. Les visites de chantiers et les ateliers pratiques nous permettent de confronter la théorie à la réalité du terrain. Une formation complète et stimulante!"
                            </p>
                            <div class="testimonial-user d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Témoignage" class="rounded-circle" width="60" height="60">
                                </div>
                                <div class="user-info">
                                    <h6 class="mb-0">Marie Kouassi</h6>
                                    <span class="small text-muted">Étudiante en Travaux Publics</span>
                                </div>
                            </div>
                                            </div>
                                        </div>
                                    </div>

                <div class="text-center mt-4">
                    <div class="testimonial-dots d-inline-flex">
                        <span class="dot active mx-1"></span>
                        <span class="dot mx-1"></span>
                        <span class="dot mx-1"></span>
                                        </div>
                                    </div>
                                </div>
            
            <div class="row mt-5">
                <div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
                    <div class="stats-container p-4 bg-white rounded-4 shadow-sm">
                        <div class="row g-4">
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <h3 class="stat-number text-primary mb-2"><span class="counter">98</span>%</h3>
                                    <p class="stat-text mb-0">Taux d'insertion professionnelle</p>
                            </div>
                        </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <h3 class="stat-number text-primary mb-2"><span class="counter">1500</span>+</h3>
                                    <p class="stat-text mb-0">Diplômés depuis 2005</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <h3 class="stat-number text-primary mb-2"><span class="counter">85</span>+</h3>
                                    <p class="stat-text mb-0">Entreprises partenaires</p>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="stat-item">
                                    <h3 class="stat-number text-primary mb-2"><span class="counter">95</span>%</h3>
                                    <p class="stat-text mb-0">Satisfaction des étudiants</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>

    <!-- Section Contact -->
    <section id="contact" class="section">
            <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h6 class="subtitle text-primary fw-bold mb-2" data-aos="fade-up">Contactez-nous</h6>
                    <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Avez-vous des questions ? Nous sommes là pour vous</h2>
                    <p class="text-muted" data-aos="fade-up" data-aos-delay="200">
                        Contactez-nous pour plus d'informations sur nos programmes de formation ou pour planifier une visite de notre campus.
                    </p>
                </div>
            </div>
            
                <div class="row g-4">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="contact-info p-4 bg-white rounded-4 shadow-sm h-100">
                        <h4 class="mb-4">Informations de contact</h4>
                        
                        <div class="d-flex mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="fs-6 mb-1">Adresse</h5>
                                <p class="text-muted mb-0">123 Avenue de la Construction, Yaoundé, Cameroun</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="fs-6 mb-1">Téléphone</h5>
                                <p class="text-muted mb-0">+237 123 456 789 / +237 987 654 321</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="fs-6 mb-1">Email</h5>
                                <p class="text-muted mb-0">info@klassci.cm</p>
                            </div>
                        </div>
                        
                        <div class="d-flex mb-4">
                            <div class="contact-icon">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="fs-6 mb-1">Heures d'ouverture</h5>
                                <p class="text-muted mb-0">Lundi - Vendredi: 8h00 - 17h00</p>
                                <p class="text-muted mb-0">Samedi: 9h00 - 13h00</p>
                            </div>
                        </div>
                        
                        <div class="social-links mt-4">
                                <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>

                <div class="col-lg-7" data-aos="fade-left">
                    <div class="contact-form p-4 bg-white rounded-4 shadow-sm">
                        <h4 class="mb-4">Envoyez-nous un message</h4>
                        <form>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="floatingName" placeholder="Votre nom">
                                        <label for="floatingName">Votre nom</label>
                        </div>
                    </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="floatingEmail" placeholder="Votre email">
                                        <label for="floatingEmail">Votre email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="floatingSubject" placeholder="Sujet">
                                        <label for="floatingSubject">Sujet</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" placeholder="Votre message" id="floatingMessage" style="height: 150px"></textarea>
                                        <label for="floatingMessage">Votre message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Envoyer le message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Carte -->
    <section class="map-section">
        <div class="container-fluid p-0">
            <div class="row g-0">
                <div class="col-12">
                    <div class="map-container" data-aos="zoom-in">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127503.42520015746!2d11.447529!3d3.8557085!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x108bcf7a309a7977%3A0x8484d7c2d6b7a7eb!2sYaound%C3%A9!5e0!3m2!1sfr!2scm!4v1701188988321!5m2!1sfr!2scm" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-dark text-light pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="mb-4 text-white">KLASSCI</h4>
                        <p>L'École Spéciale du Bâtiment et des Travaux Publics forme les professionnels de demain dans le secteur de la construction au Cameroun.</p>
                        <div class="footer-social mt-4">
                            <a href="#" class="social-icon me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon me-2"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="mb-4 text-white">Liens rapides</h4>
                        <ul class="footer-links list-unstyled">
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Accueil</a></li>
                            <li><a href="#about"><i class="fas fa-chevron-right me-2"></i> À propos</a></li>
                            <li><a href="#formations"><i class="fas fa-chevron-right me-2"></i> Formations</a></li>
                            <li><a href="#contact"><i class="fas fa-chevron-right me-2"></i> Contact</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> FAQ</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Blog</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="mb-4 text-white">Nos formations</h4>
                        <ul class="footer-links list-unstyled">
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Génie Civil</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Travaux Publics</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Architecture</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Gestion de Projets</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i> Formation Continue</a></li>
                            </ul>
                        </div>
                    </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h4 class="mb-4 text-white">Contact</h4>
                        <div class="footer-contact">
                            <p><i class="fas fa-map-marker-alt me-2"></i> 123 Avenue de la Construction, Yaoundé, Cameroun</p>
                            <p><i class="fas fa-phone-alt me-2"></i> +237 123 456 789</p>
                            <p><i class="fas fa-envelope me-2"></i> info@klassci.cm</p>
                            <p><i class="fas fa-clock me-2"></i> Lun-Ven: 8h-17h</p>
                        </div>
                        </div>
                    </div>
                </div>

            <hr class="mt-4 mb-3 border-secondary">
            
                    <div class="row">
                <div class="col-md-6">
                    <p class="mb-md-0">© 2023 KLASSCI - École Spéciale du Bâtiment et des Travaux Publics. Tous droits réservés.</p>
                        </div>
                <div class="col-md-6 text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#">Politique de confidentialité</a></li>
                        <li class="list-inline-item"><a href="#">Conditions d'utilisation</a></li>
                    </ul>
                    </div>
                </div>
        </div>
        </footer>

    <!-- Bouton retour en haut -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="fas fa-arrow-up"></i></a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
        // Initialisation AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Navbar change on scroll
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            
            // Back to top button
            const backToTop = document.querySelector('.back-to-top');
            if (window.scrollY > 100) {
                backToTop.classList.add('active');
            } else {
                backToTop.classList.remove('active');
            }
            });

        // Scroll doux pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;

                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Fonctionnalité de filtrage des formations
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.formations-filter .nav-link');
            const formationItems = document.querySelectorAll('.formation-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Retirer la classe active de tous les boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');
                    
                    // Récupérer la valeur du filtre
                    const filter = this.getAttribute('data-filter');
                    
                    // Filtrer les formations
                    formationItems.forEach(item => {
                        if (filter === 'all' || item.getAttribute('data-category') === filter) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        });
        
        // Animation des chiffres dans la section hero
        document.addEventListener('DOMContentLoaded', function() {
            const statValues = document.querySelectorAll('.hero-stat-value');
            
            const animateValue = (obj, start, end, duration) => {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.innerHTML = Math.floor(progress * (end - start) + start);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            };
            
            // Observer pour déclencher l'animation quand les éléments sont visibles
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        const endValue = parseInt(target.getAttribute('data-count'));
                        animateValue(target, 0, endValue, 2000);
                        observer.unobserve(target);
                        }
                });
            }, { threshold: 0.5 });
            
            statValues.forEach(value => {
                observer.observe(value);
                });
            });
        </script>
    </body>
</html>