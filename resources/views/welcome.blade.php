<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ESBTP - École Supérieure du Bâtiment et des Travaux Publics</title>

        <!-- Polices Google -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- AOS - Animation On Scroll -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        
        <!-- Styles personnalisés -->
        <link rel="stylesheet" href="{{ asset('css/esbtp-colors.css') }}">

        <style>
            /* Styles spécifiques à cette page */
            .hero {
                position: relative;
                height: 100vh;
                background: url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80') no-repeat center center;
                background-size: cover;
                background-attachment: fixed;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: white;
                z-index: 1;
            }
            
            .hero:before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.6);
                z-index: -1;
            }
            
            .program-card {
                transition: all 0.3s ease;
                height: 100%;
            }
            
            .program-card:hover {
                transform: translateY(-10px);
            }
            
            .program-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: var(--primary);
                transition: all 0.3s ease;
            }
            
            .program-card:hover .program-icon {
                transform: scale(1.2);
            }
            
            .navbar-scrolled {
                background-color: white !important;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-scrolled .nav-link {
                color: var(--gray-dark) !important;
            }
            
            .navbar-scrolled .nav-link:hover {
                color: var(--primary) !important;
            }
        </style>
    </head>
    <body>
        <!-- Barre de navigation -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('images/esbtp_logo.png') }}" alt="ESBTP Logo" height="60">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#hero">Accueil</a>
                        </li>
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
                            <a class="nav-link btn btn-primary text-white ms-lg-3 px-4" href="{{ route('login') }}">Connexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Section Hero -->
        <section id="hero" class="hero">
            <div class="container">
                <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                    <h1 class="display-4 fw-bold mb-4">École Supérieure du Bâtiment et des Travaux Publics</h1>
                    <p class="lead mb-5">Formez-vous aux métiers du bâtiment et des travaux publics avec des professionnels expérimentés</p>
                    <div class="hero-buttons">
                        <a href="#programs" class="btn btn-primary btn-lg me-3">Nos formations</a>
                        <a href="#contact" class="btn btn-outline-white btn-lg">Contactez-nous</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section À propos -->
        <section id="about" class="about section bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="about-img" data-aos="fade-right" data-aos-duration="1000">
                            <img src="https://images.unsplash.com/photo-1508450859948-4e04fabaa4ea?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1469&q=80" alt="ESBTP Campus" class="img-fluid rounded shadow">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content" data-aos="fade-left" data-aos-duration="1000">
                            <div class="section-title mb-4">
                                <h2>À propos de l'ESBTP</h2>
                            </div>
                            <p>L'École Supérieure du Bâtiment et des Travaux Publics (ESBTP) de Yamoussoukro est un établissement d'enseignement supérieur spécialisé dans la formation aux métiers du bâtiment et des travaux publics. Notre mission est de former des professionnels qualifiés capables de répondre aux défis du secteur de la construction en Côte d'Ivoire et en Afrique.</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-start">
                                        <div class="about-feature-icon me-3">
                                            <i class="fas fa-graduation-cap text-primary"></i>
                                        </div>
                                        <div>
                                            <h5>Enseignement de qualité</h5>
                                            <p>Des professeurs expérimentés et des méthodes pédagogiques innovantes.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-start">
                                        <div class="about-feature-icon me-3">
                                            <i class="fas fa-tools text-primary"></i>
                                        </div>
                                        <div>
                                            <h5>Équipements modernes</h5>
                                            <p>Des laboratoires et ateliers équipés des dernières technologies.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-start">
                                        <div class="about-feature-icon me-3">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <h5>Partenariats professionnels</h5>
                                            <p>Des liens étroits avec les entreprises du secteur pour des stages et emplois.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="about-feature d-flex align-items-start">
                                        <div class="about-feature-icon me-3">
                                            <i class="fas fa-certificate text-primary"></i>
                                        </div>
                                        <div>
                                            <h5>Diplômes reconnus</h5>
                                            <p>Des formations certifiées et reconnues au niveau national et international.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Programmes -->
        <section id="programs" class="programs section">
            <div class="container">
                <div class="section-title text-center mb-5" data-aos="fade-up">
                    <h2>Nos programmes de formation</h2>
                    <p>Découvrez nos différentes filières d'études dans le domaine du bâtiment et des travaux publics</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card program-card shadow h-100">
                            <div class="card-body text-center p-4">
                                <div class="program-icon">
                                    <i class="fas fa-hard-hat"></i>
                                </div>
                                <h4 class="card-title">Génie Civil</h4>
                                <p class="card-text">Formation en conception, analyse et réalisation d'infrastructures civiles comme les ponts, routes et bâtiments.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card program-card shadow h-100">
                            <div class="card-body text-center p-4">
                                <div class="program-icon">
                                    <i class="fas fa-road"></i>
                                </div>
                                <h4 class="card-title">Travaux Publics</h4>
                                <p class="card-text">Spécialisation dans la construction d'infrastructures publiques comme les autoroutes, barrages et aéroports.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card program-card shadow h-100">
                            <div class="card-body text-center p-4">
                                <div class="program-icon">
                                    <i class="fas fa-drafting-compass"></i>
                                </div>
                                <h4 class="card-title">Architecture</h4>
                                <p class="card-text">Formation en conception architecturale, design d'intérieur et planification urbaine.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="card program-card shadow h-100">
                            <div class="card-body text-center p-4">
                                <div class="program-icon">
                                    <i class="fas fa-mountain"></i>
                                </div>
                                <h4 class="card-title">Topographie</h4>
                                <p class="card-text">Expertise en mesure et représentation des terrains pour les projets de construction.</p>
                            </div>
                        </div>
                            </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="card program-card shadow h-100">
                            <div class="card-body text-center p-4">
                                <div class="program-icon">
                                    <i class="fas fa-water"></i>
                                </div>
                                <h4 class="card-title">Hydraulique</h4>
                                <p class="card-text">Spécialisation dans la gestion des ressources en eau, l'assainissement et les systèmes d'irrigation.</p>
                            </div>
                        </div>
                            </div>
                    
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
                        <div class="card program-card shadow h-100">
                            <div class="card-body text-center p-4">
                                <div class="program-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <h4 class="card-title">Gestion de Chantier</h4>
                                <p class="card-text">Formation en planification, coordination et supervision des projets de construction.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Contact -->
        <section id="contact" class="contact section bg-light">
            <div class="container">
                <div class="section-title text-center mb-5" data-aos="fade-up">
                    <h2>Contactez-nous</h2>
                    <p>Nous sommes à votre disposition pour répondre à toutes vos questions</p>
                </div>
                
                <div class="row g-5">
                    <!-- Carte Google Maps à gauche en format portrait -->
                    <div class="col-lg-5" data-aos="fade-right">
                        <div class="map-container bg-white p-3 rounded shadow h-100">
                            <!-- Carte Google Maps avec les coordonnées exactes de l'ESBTP Yamoussoukro -->
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3972.4410263625247!2d-5.2957698!3d6.8088889!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfb366f6d3c4d3c5%3A0x4b7f1e350d62e8c6!2sESBTP%20-%20%C3%89cole%20Sup%C3%A9rieure%20du%20B%C3%A2timent%20et%20des%20Travaux%20Publics!5e0!3m2!1sfr!2sfr!4v1652345678901!5m2!1sfr!2sfr" width="100%" height="100%" style="border:0; min-height: 500px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    
                    <!-- Formulaire et informations de contact à droite -->
                    <div class="col-lg-7" data-aos="fade-left">
                        <div class="row g-4">
                            <!-- Formulaire d'envoi de message -->
                            <div class="col-12 mb-4">
                                <div class="contact-form bg-white p-4 rounded shadow">
                                    <h3 class="mb-4">Envoyez-nous un message</h3>
                            <form>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Votre nom" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="email" class="form-control" placeholder="Votre email" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Sujet" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <textarea class="form-control" rows="5" placeholder="Votre message" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary btn-submit">Envoyer le message</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Informations de contact sous le formulaire -->
                            <div class="col-12">
                                <div class="contact-info bg-white p-4 rounded shadow">
                                    <h3 class="mb-4">Informations de contact</h3>
                                    
                                <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex">
                                                <i class="fas fa-map-marker-alt text-primary me-3 mt-1 fa-lg"></i>
                                                <div>
                                                    <h5 class="mb-1">Adresse</h5>
                                                    <p class="text-dark">Quartier Millionnaire, Yamoussoukro, Côte d'Ivoire</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex">
                                                <i class="fas fa-phone text-primary me-3 mt-1 fa-lg"></i>
                                                <div>
                                                    <h5 class="mb-1">Téléphone</h5>
                                                    <p class="text-dark">+225 27 30 64 66 75<br>+225 07 07 43 43 75</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex">
                                                <i class="fas fa-envelope text-primary me-3 mt-1 fa-lg"></i>
                                                <div>
                                                    <h5 class="mb-1">Email</h5>
                                                    <p class="text-dark">info@esbtp-ci.net</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="d-flex">
                                                <i class="fas fa-globe text-primary me-3 mt-1 fa-lg"></i>
                                                <div>
                                                    <h5 class="mb-1">Site Web</h5>
                                                    <p class="text-dark">www.esbtp-ci.net</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <h5 class="mb-3">Suivez-nous</h5>
                                        <div class="social-links">
                                            <a href="#" class="me-2 btn btn-outline-primary btn-sm rounded-circle"><i class="fab fa-facebook-f"></i></a>
                                            <a href="#" class="me-2 btn btn-outline-primary btn-sm rounded-circle"><i class="fab fa-twitter"></i></a>
                                            <a href="#" class="me-2 btn btn-outline-primary btn-sm rounded-circle"><i class="fab fa-instagram"></i></a>
                                            <a href="#" class="me-2 btn btn-outline-primary btn-sm rounded-circle"><i class="fab fa-linkedin-in"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-dark text-white pt-5 pb-3">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-about">
                            <div class="footer-logo mb-3">
                                <img src="{{ asset('img/esbtp_logo_white.png') }}" alt="ESBTP Logo" height="60">
                            </div>
                            <p>L'ESBTP est un établissement d'enseignement supérieur spécialisé dans la formation aux métiers du bâtiment et des travaux publics, situé à Yamoussoukro, Côte d'Ivoire.</p>
                            <div class="social-links mt-3">
                                <a href="#" class="me-2"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6">
                        <div class="footer-links">
                            <h4>Liens rapides</h4>
                            <ul class="list-unstyled">
                                <li><a href="#hero"><i class="fas fa-angle-right me-2"></i>Accueil</a></li>
                                <li><a href="#about"><i class="fas fa-angle-right me-2"></i>À propos</a></li>
                                <li><a href="#programs"><i class="fas fa-angle-right me-2"></i>Formations</a></li>
                                <li><a href="#contact"><i class="fas fa-angle-right me-2"></i>Contact</a></li>
                                <li><a href="{{ route('login') }}"><i class="fas fa-angle-right me-2"></i>Connexion</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-links">
                            <h4>Nos formations</h4>
                            <ul class="list-unstyled">
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>Génie Civil</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>Travaux Publics</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>Architecture</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>Topographie</a></li>
                                <li><a href="#"><i class="fas fa-angle-right me-2"></i>Hydraulique</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-links">
                            <h4>Contact</h4>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-map-marker-alt me-2"></i>Quartier Millionnaire, Yamoussoukro, Côte d'Ivoire</li>
                                <li><i class="fas fa-phone me-2"></i>+225 27 30 64 66 75</li>
                                <li><i class="fas fa-envelope me-2"></i>info@esbtp-ci.net</li>
                                <li><i class="fas fa-clock me-2"></i>Lun-Ven: 8h00 - 17h00</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="copyright mt-4 pt-3 border-top border-secondary">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start">
                            <p>&copy; 2023 ESBTP Yamoussoukro. Tous droits réservés.</p>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <p>Conçu avec <i class="fas fa-heart text-danger"></i> pour l'éducation</p>
                        </div>
                    </div>
                </div>
        </div>
        </footer>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            // Initialisation AOS (Animation On Scroll)
            AOS.init();
            
            // Changement de couleur de la navbar au défilement
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });
            
            // Défilement fluide pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 70,
                        behavior: 'smooth'
                    });
                        
                        // Fermer le menu mobile après clic
                        const navbarCollapse = document.querySelector('.navbar-collapse');
                        if (navbarCollapse.classList.contains('show')) {
                            navbarCollapse.classList.remove('show');
                        }
                    }
                });
            });
        </script>
    </body>
</html>
