<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>KLASSCI - Solution de Gestion Intelligente</title>

        <!-- Polices Google -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- AOS - Animation On Scroll -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

        <style>
        :root {
            /* Palette de couleurs */
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #6366f1;
            --accent: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;

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
            font-family: 'Poppins', sans-serif;
            color: var(--dark);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
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
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
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

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
        }

        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }

        /* Navigation */
        .navbar {
            padding: 1rem 0;
            transition: all 0.5s ease;
            z-index: 1000;
        }

        .navbar-brand img {
            height: 40px;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
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
            padding: 120px 0 80px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(79, 70, 229, 0.1) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-shape {
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 130px;
            background: white;
            clip-path: polygon(0 45%, 100% 0, 100% 100%, 0% 100%);
            z-index: 0;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .hero p {
            font-size: 1.1rem;
            color: var(--gray);
            margin-bottom: 2rem;
            line-height: 1.7;
        }
        
        .hero-img {
            position: relative;
            z-index: 1;
        }
        
        .hero-img img {
            max-width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: var(--gray);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .feature-card {
            padding: 2rem;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .feature-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            height: 70px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            font-size: 1.75rem;
        }
        
        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .feature-card p {
            color: var(--gray);
            margin-bottom: 0;
            line-height: 1.7;
        }

        /* Testimonials Section */
        .testimonials {
            background-color: #f8fafc;
        }

        .testimonial-card {
            background-color: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .testimonial-rating {
            color: #f59e0b;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            font-size: 1rem;
            color: var(--gray);
            font-style: italic;
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 1rem;
            object-fit: cover;
        }

        .testimonial-author h5 {
            margin-bottom: 0.25rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .testimonial-author p {
            margin-bottom: 0;
            font-size: 0.875rem;
            color: var(--gray);
        }

        @media (max-width: 767.98px) {
            .testimonial-card {
                margin-bottom: 1.5rem;
            }
        }

        /* CTA Section */
        .cta {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-light {
            background-color: white;
            color: var(--primary);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-light:hover {
            background-color: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
        }

        /* Footer */
        .footer {
            padding: 80px 0 40px;
            background-color: var(--dark);
            color: white;
        }
        
        .footer-logo {
            margin-bottom: 1.5rem;
        }
        
        .footer-logo img {
            height: 45px;
        }
        
        .footer p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
        }
        
        .footer h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: white;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.75rem;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
            text-decoration: none;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .copyright {
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        @media (max-width: 991.98px) {
            .hero h1 {
                font-size: 2.75rem;
            }
            
            .hero-img {
                margin-top: 3rem;
            }
        }
        
        @media (max-width: 767.98px) {
            .hero {
                padding: 100px 0 60px;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 2.25rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .cta h2 {
                font-size: 2rem;
            }
            
            .footer {
                text-align: center;
            }
            
            .social-links {
                justify-content: center;
            }
        }

        /* Contact Section Styles */
        .contact {
            background-color: var(--light-bg);
            position: relative;
            overflow: hidden;
        }

        .contact-info {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .contact-card {
            display: flex;
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background-color: var(--primary-color-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .contact-icon i {
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .contact-text h5 {
            margin-bottom: 0.5rem;
            color: var(--dark-text);
            font-weight: 600;
        }

        .contact-text p {
            margin-bottom: 0.25rem;
            color: var(--muted-text);
        }

        .contact-form-wrapper {
            background-color: #fff;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
        }

        .contact-form .form-control,
        .contact-form .form-select {
            border: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-form .form-control:focus,
        .contact-form .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.15);
        }

        .contact-form .form-floating label {
            padding: 0.75rem 1rem;
        }

        .contact-form .form-check-label {
            color: var(--muted-text);
            font-size: 0.875rem;
        }

        .contact-form .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        @media (max-width: 991.98px) {
            .contact-info {
                margin-bottom: 2rem;
            }
        }
        </style>
    </head>
    <body>
        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#"><img src="{{ asset('img/KLASSCI-PNG.png') }}" alt="KLASSCI Logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Fonctionnalités</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#solutions">Solutions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#pricing">Tarifs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary" href="{{ route('login') }}">Se connecter</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                        <h1>La solution de gestion intelligente pour votre entreprise</h1>
                        <p>KLASSCI est une plateforme tout-en-un qui simplifie la gestion de vos processus métier, améliore la productivité et vous aide à prendre des décisions basées sur des données fiables.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="#demo" class="btn btn-primary">Demander une démo</a>
                            <a href="#" class="btn btn-outline-primary">En savoir plus</a>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                        <div class="hero-img floating">
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="KLASSCI Dashboard" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-shape"></div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features">
            <div class="container">
                <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Fonctionnalités principales</h2>
                    <p>Découvrez comment KLASSCI peut transformer vos opérations quotidiennes en processus efficaces et automatisés</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Tableaux de bord interactifs</h3>
                            <p>Visualisez vos données clés en temps réel avec des tableaux de bord personnalisables qui vous permettent de suivre facilement vos performances.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>Gestion des utilisateurs</h3>
                            <p>Créez et gérez facilement des comptes utilisateurs avec différents niveaux d'accès pour sécuriser vos données sensibles.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h3>Automatisation des tâches</h3>
                            <p>Automatisez les tâches répétitives pour gagner du temps et réduire les erreurs humaines dans vos processus quotidiens.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h3>Gestion documentaire</h3>
                            <p>Stockez, organisez et partagez vos documents importants en toute sécurité avec un système de classement intelligent.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h3>Notifications et alertes</h3>
                            <p>Restez informé des événements importants grâce à un système de notifications personnalisables par email ou dans l'application.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3>Application mobile</h3>
                            <p>Accédez à vos données et gérez vos opérations où que vous soyez grâce à notre application mobile intuitive et performante.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials py-5 bg-light">
            <div class="container py-5">
                <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Ce que nos clients disent</h2>
                    <p>Découvrez comment KLASSCI a transformé l'organisation et la productivité de nos clients à travers ces témoignages</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"KLASSCI a complètement révolutionné notre gestion de projets. Nous avons gagné un temps précieux et amélioré la collaboration entre nos équipes de 40%."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sophie Martin" class="testimonial-avatar">
                                <div>
                                    <h5>Sophie Martin</h5>
                                    <p>Directrice des Opérations, BTP Solutions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"Intuitive et puissante, l'interface de KLASSCI nous permet de suivre nos performances en temps réel. Une solution indispensable pour notre développement."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Thomas Dubois" class="testimonial-avatar">
                                <div>
                                    <h5>Thomas Dubois</h5>
                                    <p>PDG, Innov'Education</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="testimonial-text">"Notre transition vers KLASSCI s'est faite sans accroc. Le service client est exceptionnel et la solution s'adapte parfaitement à nos besoins spécifiques."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/women/67.jpg" alt="Isabelle Laurent" class="testimonial-avatar">
                                <div>
                                    <h5>Isabelle Laurent</h5>
                                    <p>Responsable IT, Santé Plus</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"Les fonctionnalités d'automatisation de KLASSCI nous ont permis de réduire de 30% le temps consacré aux tâches administratives. Une vraie révélation!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Pierre Moreau" class="testimonial-avatar">
                                <div>
                                    <h5>Pierre Moreau</h5>
                                    <p>Directeur Général, ConstructPro</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"Depuis que nous utilisons KLASSCI, nos équipes sont plus alignées et nos clients plus satisfaits. Le ROI est évident après seulement 6 mois d'utilisation."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/women/15.jpg" alt="Marie Petit" class="testimonial-avatar">
                                <div>
                                    <h5>Marie Petit</h5>
                                    <p>Directrice Marketing, TechInnov</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                        <div class="testimonial-card">
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <p class="testimonial-text">"La qualité des analyses et des rapports générés par KLASSCI nous aide à prendre de meilleures décisions. Un outil stratégique pour notre croissance."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/36.jpg" alt="Nicolas Bernard" class="testimonial-avatar">
                                <div>
                                    <h5>Nicolas Bernard</h5>
                                    <p>CFO, Groupe Bâtiment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact py-5">
            <div class="container py-5">
                <div class="section-title" data-aos="fade-up" data-aos-duration="1000">
                    <h2>Contactez-nous</h2>
                    <p>Nous sommes là pour répondre à toutes vos questions et vous aider à trouver la solution adaptée à vos besoins</p>
                </div>
                
                <div class="row g-5">
                    <!-- Contact Information -->
                    <div class="col-lg-5" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div class="contact-info">
                            <div class="contact-card mb-4">
                                <div class="contact-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <h5>Notre adresse</h5>
                                    <p>123 Avenue Example, Paris 75000, France</p>
                                </div>
                            </div>
                            
                            <div class="contact-card mb-4">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-text">
                                    <h5>Email</h5>
                                    <p>contact@klassci.com</p>
                                    <p>support@klassci.com</p>
                                </div>
                            </div>
                            
                            <div class="contact-card mb-4">
                                <div class="contact-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div class="contact-text">
                                    <h5>Téléphone</h5>
                                    <p>+33 1 23 45 67 89</p>
                                    <p>+33 1 23 45 67 90</p>
                                </div>
                            </div>
                            
                            <div class="contact-card">
                                <div class="contact-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="contact-text">
                                    <h5>Horaires d'ouverture</h5>
                                    <p>Lundi - Vendredi: 9h00 - 18h00</p>
                                    <p>Fermé le weekend</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Form -->
                    <div class="col-lg-7" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        <div class="contact-form-wrapper">
                            <form id="contactForm" class="contact-form">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="name" placeholder="Votre nom" required>
                                            <label for="name">Votre nom</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="email" placeholder="Votre email" required>
                                            <label for="email">Votre email</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="subject" placeholder="Sujet" required>
                                            <label for="subject">Sujet</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="service" aria-label="Service intéressé">
                                                <option selected disabled>Sélectionnez un service</option>
                                                <option value="gestion-projets">Gestion de projets</option>
                                                <option value="gestion-documents">Gestion documentaire</option>
                                                <option value="automatisation">Automatisation des tâches</option>
                                                <option value="analyse-donnees">Analyse de données</option>
                                                <option value="support-technique">Support technique</option>
                                                <option value="autre">Autre</option>
                                            </select>
                                            <label for="service">Service qui vous intéresse</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="message" placeholder="Votre message" style="height: 150px" required></textarea>
                                            <label for="message">Votre message</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="privacyPolicy" required>
                                            <label class="form-check-label" for="privacyPolicy">
                                                J'accepte que mes données soient traitées conformément à la <a href="#">politique de confidentialité</a>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">Envoyer le message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta">
            <div class="container">
                <div data-aos="fade-up" data-aos-duration="1000">
                    <h2>Prêt à transformer votre entreprise ?</h2>
                    <p>Rejoignez plus de 1000 entreprises qui font confiance à KLASSCI pour optimiser leur gestion quotidienne et stimuler leur croissance.</p>
                    <a href="#" class="btn btn-light">Commencer gratuitement</a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-5 mb-lg-0">
                        <div class="footer-logo">
                            <img src="{{ asset('img/KLASSCI-PNG.png') }}" alt="KLASSCI Logo">
                        </div>
                        <p>KLASSCI vous offre des solutions de gestion innovantes pour optimiser vos processus et maximiser votre productivité.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                        <h5>Société</h5>
                        <ul class="footer-links">
                            <li><a href="#">À propos</a></li>
                            <li><a href="#">Équipe</a></li>
                            <li><a href="#">Carrières</a></li>
                            <li><a href="#">Blog</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-5 mb-md-0">
                        <h5>Produit</h5>
                        <ul class="footer-links">
                            <li><a href="#">Fonctionnalités</a></li>
                            <li><a href="#">Tarifs</a></li>
                            <li><a href="#">Témoignages</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <h5>Contact</h5>
                        <ul class="footer-links">
                            <li><i class="fas fa-map-marker-alt me-2"></i> 123 Avenue Example, Paris 75000</li>
                            <li><i class="fas fa-phone me-2"></i> +33 1 23 45 67 89</li>
                            <li><i class="fas fa-envelope me-2"></i> contact@klassci.com</li>
                        </ul>
                    </div>
                </div>
                
                <div class="copyright">
                    <p>&copy; 2023 KLASSCI. Tous droits réservés.</p>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            // Initialize AOS
            AOS.init();
            
            // Navbar scroll behavior
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        </script>
    </body>
</html> 