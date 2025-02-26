<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>ESBTP - École Supérieure du Bâtiment et des Travaux Publics</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- ESBTP Colors CSS -->
        <link rel="stylesheet" href="{{ asset('css/esbtp-colors.css') }}">

        <style>
            :root {
                --esbtp-green: #01632f;
                --esbtp-orange: #f29400;
                --esbtp-white: #ffffff;
            }
            
            body {
                font-family: 'Nunito', sans-serif;
                color: #333;
            }
            
            /* Navigation */
            .navbar {
                background-color: var(--esbtp-white);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                padding: 15px 0;
            }
            
            .navbar-brand {
                font-weight: 700;
                color: var(--esbtp-green) !important;
            }
            
            .nav-link {
                color: #333 !important;
                font-weight: 600;
                margin: 0 10px;
                transition: color 0.3s ease;
            }
            
            .nav-link:hover {
                color: var(--esbtp-orange) !important;
            }
            
            .btn-login {
                background-color: var(--esbtp-green);
                color: white !important;
                border-radius: 30px;
                padding: 8px 20px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-login:hover {
                background-color: var(--esbtp-orange);
                transform: translateY(-2px);
            }
            
            /* Hero Section */
            .hero {
                background: linear-gradient(rgba(1, 99, 47, 0.8), rgba(242, 148, 0, 0.8)), url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
                background-size: cover;
                background-position: center;
                color: white;
                padding: 120px 0;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 20px;
            }
            
            .hero p {
                font-size: 1.2rem;
                max-width: 700px;
                margin: 0 auto 30px;
            }
            
            .btn-hero {
                background-color: var(--esbtp-orange);
                color: white;
                border-radius: 30px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
                border: none;
            }
            
            .btn-hero:hover {
                background-color: white;
                color: var(--esbtp-orange);
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }
            
            /* Sections */
            section {
                padding: 80px 0;
            }
            
            .section-title {
                text-align: center;
                margin-bottom: 60px;
            }
            
            .section-title h2 {
                color: var(--esbtp-green);
                font-weight: 700;
                position: relative;
                display: inline-block;
                margin-bottom: 15px;
            }
            
            .section-title h2::after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                width: 80px;
                height: 3px;
                background-color: var(--esbtp-orange);
            }
            
            /* About Section */
            .about-img {
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            }
            
            .about-img img {
                transition: transform 0.5s ease;
            }
            
            .about-img:hover img {
                transform: scale(1.05);
            }
            
            .about-content h3 {
                color: var(--esbtp-green);
                font-weight: 700;
                margin-bottom: 20px;
            }
            
            .about-content p {
                margin-bottom: 20px;
                line-height: 1.8;
            }
            
            /* Programs Section */
            .program-card {
                background-color: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                height: 100%;
                border: none;
            }
            
            .program-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            }
            
            .program-card .card-body {
                padding: 25px;
            }
            
            .program-card .card-title {
                color: var(--esbtp-green);
                font-weight: 700;
                margin-bottom: 15px;
            }
            
            .program-icon {
                font-size: 2.5rem;
                color: var(--esbtp-orange);
                margin-bottom: 20px;
            }
            
            /* Contact Section */
            .contact-info {
                background-color: var(--esbtp-green);
                color: white;
                padding: 30px;
                border-radius: 10px;
                height: 100%;
            }
            
            .contact-info h3 {
                margin-bottom: 20px;
                font-weight: 700;
            }
            
            .contact-info p {
                margin-bottom: 15px;
            }
            
            .contact-info i {
                color: var(--esbtp-orange);
                margin-right: 10px;
                font-size: 1.2rem;
            }
            
            .contact-form {
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            }
            
            .contact-form h3 {
                color: var(--esbtp-green);
                margin-bottom: 20px;
                font-weight: 700;
            }
            
            .form-control {
                border: 1px solid #e1e1e1;
                padding: 12px 15px;
                margin-bottom: 20px;
                border-radius: 5px;
            }
            
            .form-control:focus {
                border-color: var(--esbtp-orange);
                box-shadow: none;
            }
            
            .btn-submit {
                background-color: var(--esbtp-green);
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 5px;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .btn-submit:hover {
                background-color: var(--esbtp-orange);
            }
            
            /* Footer */
            footer {
                background-color: var(--esbtp-green);
                color: white;
                padding: 60px 0 20px;
            }
            
            .footer-logo {
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 15px;
            }
            
            .footer-about {
                margin-bottom: 30px;
            }
            
            .footer-links h4 {
                font-weight: 700;
                margin-bottom: 20px;
                color: var(--esbtp-orange);
            }
            
            .footer-links ul {
                list-style: none;
                padding: 0;
            }
            
            .footer-links li {
                margin-bottom: 10px;
            }
            
            .footer-links a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: color 0.3s ease;
            }
            
            .footer-links a:hover {
                color: var(--esbtp-orange);
            }
            
            .social-links {
                margin-top: 20px;
            }
            
            .social-links a {
                display: inline-block;
                width: 40px;
                height: 40px;
                background-color: rgba(255, 255, 255, 0.1);
                color: white;
                border-radius: 50%;
                text-align: center;
                line-height: 40px;
                margin-right: 10px;
                transition: all 0.3s ease;
            }
            
            .social-links a:hover {
                background-color: var(--esbtp-orange);
                transform: translateY(-3px);
            }
            
            .copyright {
                text-align: center;
                padding-top: 30px;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 50px;
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">ESBTP</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#about">À propos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#programs">Formations</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn-login" href="{{ route('login') }}">Connexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h1>École Supérieure du Bâtiment et des Travaux Publics</h1>
                <p>Formez-vous aux métiers du bâtiment et des travaux publics avec notre école d'excellence. Des formations de qualité pour construire votre avenir professionnel.</p>
                <a href="#programs" class="btn btn-hero">Découvrir nos formations</a>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="about">
            <div class="container">
                <div class="section-title">
                    <h2>À propos de l'ESBTP</h2>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="about-img">
                            <img src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="ESBTP Campus" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <h3>Former les professionnels de demain</h3>
                            <p>L'École Supérieure du Bâtiment et des Travaux Publics (ESBTP) est une institution d'enseignement supérieur dédiée à la formation de professionnels hautement qualifiés dans les domaines du bâtiment et des travaux publics.</p>
                            <p>Notre mission est de fournir une éducation de qualité qui combine théorie et pratique, préparant nos étudiants à relever les défis du secteur de la construction et à contribuer au développement des infrastructures.</p>
                            <p>Avec un corps enseignant qualifié, des équipements modernes et des partenariats solides avec l'industrie, nous offrons un environnement d'apprentissage optimal pour la réussite de nos étudiants.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programs Section -->
        <section id="programs" class="programs bg-light">
            <div class="container">
                <div class="section-title">
                    <h2>Nos formations</h2>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card program-card">
                            <div class="card-body text-center">
                                <div class="program-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h5 class="card-title">Génie Civil</h5>
                                <p class="card-text">Formation complète en conception, analyse et construction de structures comme les bâtiments, ponts et barrages.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card program-card">
                            <div class="card-body text-center">
                                <div class="program-icon">
                                    <i class="fas fa-road"></i>
                                </div>
                                <h5 class="card-title">Travaux Publics</h5>
                                <p class="card-text">Spécialisation dans la construction et l'entretien des infrastructures publiques comme les routes, tunnels et réseaux.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card program-card">
                            <div class="card-body text-center">
                                <div class="program-icon">
                                    <i class="fas fa-drafting-compass"></i>
                                </div>
                                <h5 class="card-title">Architecture</h5>
                                <p class="card-text">Formation en conception architecturale, combinant créativité artistique et compétences techniques.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card program-card">
                            <div class="card-body text-center">
                                <div class="program-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                                <h5 class="card-title">Topographie</h5>
                                <p class="card-text">Expertise en mesure et cartographie des terrains pour les projets de construction et d'aménagement.</p>
                            </div>
                        </div>
                            </div>
                    <div class="col-md-4 mb-4">
                        <div class="card program-card">
                            <div class="card-body text-center">
                                <div class="program-icon">
                                    <i class="fas fa-water"></i>
                                </div>
                                <h5 class="card-title">Hydraulique</h5>
                                <p class="card-text">Spécialisation dans la gestion des ressources en eau, les systèmes d'irrigation et les installations hydrauliques.</p>
                            </div>
                        </div>
                            </div>
                    <div class="col-md-4 mb-4">
                        <div class="card program-card">
                            <div class="card-body text-center">
                                <div class="program-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <h5 class="card-title">Gestion de Construction</h5>
                                <p class="card-text">Formation en planification, coordination et supervision des projets de construction du début à la fin.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact">
            <div class="container">
                <div class="section-title">
                    <h2>Contactez-nous</h2>
                </div>
                <div class="row">
                    <div class="col-lg-5 mb-4 mb-lg-0">
                        <div class="contact-info">
                            <h3>Nos coordonnées</h3>
                            <p><i class="fas fa-map-marker-alt"></i> 123 Avenue de la Construction, 75000 Paris, France</p>
                            <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                            <p><i class="fas fa-envelope"></i> contact@esbtp.edu</p>
                            <div class="mt-4">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937595!2d2.292292615509614!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1621956217640!5m2!1sfr!2sfr" width="100%" height="250" style="border:0; border-radius: 5px;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-form">
                            <h3>Envoyez-nous un message</h3>
                            <form>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Votre nom">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" placeholder="Votre email">
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="Sujet">
                                <textarea class="form-control" rows="5" placeholder="Votre message"></textarea>
                                <button type="submit" class="btn btn-submit">Envoyer le message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <div class="footer-about">
                            <div class="footer-logo">ESBTP</div>
                            <p>L'École Supérieure du Bâtiment et des Travaux Publics forme les professionnels de demain dans les domaines de la construction et des infrastructures.</p>
                            <div class="social-links">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                        <div class="footer-links">
                            <h4>Liens rapides</h4>
                            <ul>
                                <li><a href="#about">À propos</a></li>
                                <li><a href="#programs">Formations</a></li>
                                <li><a href="#contact">Contact</a></li>
                                <li><a href="{{ route('login') }}">Connexion</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                        <div class="footer-links">
                            <h4>Nos formations</h4>
                            <ul>
                                <li><a href="#">Génie Civil</a></li>
                                <li><a href="#">Travaux Publics</a></li>
                                <li><a href="#">Architecture</a></li>
                                <li><a href="#">Topographie</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-links">
                            <h4>Contact</h4>
                            <ul>
                                <li><i class="fas fa-map-marker-alt"></i> 123 Avenue de la Construction, 75000 Paris</li>
                                <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                                <li><i class="fas fa-envelope"></i> contact@esbtp.edu</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="copyright">
                    <p>&copy; {{ date('Y') }} ESBTP. Tous droits réservés.</p>
            </div>
        </div>
        </footer>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
            
            // Add padding to body for fixed navbar
            document.body.style.paddingTop = '76px';
        </script>
    </body>
</html>
