<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KLASSCI') }} - Connexion</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            /* Palette de couleurs */
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: rgba(99, 102, 241, 0.1);
            --secondary: #ec4899;
            --secondary-dark: #db2777;
            --secondary-light: rgba(236, 72, 153, 0.1);
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --gray-light: #e2e8f0;
            --light: #f8fafc;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #0ea5e9;

            /* Bordures */
            --border-radius-sm: 0.25rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 1rem;
            --border-radius-xl: 1.5rem;
            --border-radius-pill: 9999px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.95) 0%, rgba(236, 72, 153, 0.8) 100%),
                        url('{{ asset('images/login_bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            z-index: 10;
        }

        .card {
            border-radius: var(--border-radius-xl);
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
            border: none;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            transition: all 0.4s ease;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 45px 80px -15px rgba(0, 0, 0, 0.4);
        }

        .card-header {
            background: white;
            border-bottom: none;
            padding: 2.5rem 2.5rem 0;
            text-align: center;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 0;
            right: 0;
            height: 30px;
            background: white;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
            z-index: 1;
        }

        .card-body {
            padding: 2.5rem;
            position: relative;
            z-index: 2;
        }

        .login-logo {
            max-width: 150px;
            margin-bottom: 1.5rem;
            display: block;
            margin-left: auto;
            margin-right: auto;
            filter: drop-shadow(0 10px 15px rgba(99, 102, 241, 0.3));
            animation: logoFloat 6s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        .login-title {
            color: var(--dark);
            font-size: 2rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .form-control {
            padding: 1rem 1.25rem;
            border-radius: var(--border-radius-md);
            border: 1.5px solid var(--gray-light);
            background-color: white;
            color: var(--dark);
            font-size: 1rem;
            transition: all 0.3s ease;
            height: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 5px var(--primary-light);
            transform: translateY(-2px);
        }

        .form-label {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            letter-spacing: 0.2px;
        }

        .input-group-text {
            background-color: var(--primary-light);
            border: 1.5px solid var(--primary-light);
            color: var(--primary);
            border-radius: var(--border-radius-md) 0 0 var(--border-radius-md);
            padding: 0 1.25rem;
        }

        .btn {
            padding: 1rem 1.75rem;
            border-radius: var(--border-radius-pill);
            font-weight: 600;
            transition: all 0.4s ease;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-5px);
            box-shadow: 0 20px 35px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:active {
            transform: translateY(-2px);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-label {
            color: var(--gray);
            font-size: 0.95rem;
        }

        .alert {
            border-radius: var(--border-radius-md);
            border: none;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.75rem;
            font-size: 0.95rem;
            display: flex;
            align-items: flex-start;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .alert i {
            margin-right: 1rem;
            font-size: 1.25rem;
            margin-top: 0.2rem;
        }

        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border-left: 5px solid var(--success);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-left: 5px solid var(--danger);
        }

        .back-to-home {
            position: absolute;
            top: 2rem;
            left: 2rem;
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background-color: rgba(15, 23, 42, 0.2);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius-pill);
            transition: all 0.3s ease;
            z-index: 100;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .back-to-home:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateX(-8px);
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.15);
        }

        .animated-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            z-index: 1;
        }

        .shape1 {
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: rgba(99, 102, 241, 0.3);
            animation: shapeFloat1 25s infinite alternate ease-in-out;
        }

        .shape2 {
            bottom: -300px;
            left: -200px;
            width: 700px;
            height: 700px;
            background: rgba(236, 72, 153, 0.3);
            animation: shapeFloat2 30s infinite alternate ease-in-out;
        }

        .shape3 {
            bottom: 50px;
            right: 10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.2);
            animation: shapeFloat3 20s infinite alternate ease-in-out;
        }

        @keyframes shapeFloat1 {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(100px, 150px) rotate(15deg); }
        }

        @keyframes shapeFloat2 {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-150px, 100px) rotate(-20deg); }
        }

        @keyframes shapeFloat3 {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, -80px) scale(1.2); }
        }

        .small.text-primary {
            color: var(--primary) !important;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
        }

        .small.text-primary:hover {
            color: var(--secondary) !important;
            text-decoration: underline;
        }

        .text-muted {
            color: var(--gray) !important;
        }

        .login-features {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-top: 2.5rem;
        }

        .login-features .feature-item {
            display: flex;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.15);
            padding: 0.85rem 1.5rem;
            border-radius: var(--border-radius-pill);
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .login-features .feature-item:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .login-features .feature-item i {
            margin-right: 0.85rem;
            font-size: 1.1rem;
            background: linear-gradient(135deg, #fff 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Séparateur stylisé */
        .custom-separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: var(--gray);
        }

        .custom-separator::before,
        .custom-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--gray-light);
        }

        .custom-separator::before {
            margin-right: 1rem;
        }

        .custom-separator::after {
            margin-left: 1rem;
        }

        /* Animation du bouton de connexion */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        /* Indication de force du mot de passe */
        .password-strength {
            height: 5px;
            margin-top: 0.5rem;
            border-radius: var(--border-radius-pill);
            background-color: var(--gray-light);
            position: relative;
            overflow: hidden;
        }

        .password-strength-meter {
            height: 100%;
            width: 0;
            border-radius: var(--border-radius-pill);
            transition: width 0.5s ease, background-color 0.5s ease;
        }

        .password-strength-text {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            text-align: right;
        }

        /* Icône de sécurité animée */
        .security-badge {
            display: inline-flex;
            align-items: center;
            background-color: var(--primary-light);
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius-pill);
            position: absolute;
            top: -10px;
            right: 20px;
        }

        .security-badge i {
            margin-right: 0.5rem;
            animation: securityPulse 2s infinite;
        }

        @keyframes securityPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        @media (max-width: 767.98px) {
            .card-header,
            .card-body {
                padding: 2rem 1.5rem;
            }

            .login-title {
                font-size: 1.75rem;
            }
            
            .back-to-home {
                top: 1.25rem;
                left: 1.25rem;
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .login-features {
                flex-direction: column;
                align-items: center;
            }
            
            .login-features .feature-item {
                width: 100%;
                justify-content: center;
            }

            .btn {
                padding: 0.85rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="back-to-home">
        <i class="fas fa-arrow-left"></i>
        <span>Retour à l'accueil</span>
    </a>

    <div class="animated-shape shape1"></div>
    <div class="animated-shape shape2"></div>
    <div class="animated-shape shape3"></div>

    <div class="container">
        <div class="login-container" data-aos="fade-up" data-aos-duration="800">
            <div class="card">
                <div class="card-header">
                    <img src="{{ asset('images/LOGO-KLASSCI-PNG.png') }}" alt="KLASSCI Logo" class="login-logo">
                    <h1 class="login-title">Bienvenue sur KLASSCI</h1>
                    <p class="login-subtitle">Connectez-vous pour accéder à votre espace personnel</p>
                </div>
                <div class="card-body">
                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Connexion sécurisée</span>
                    </div>

                    @if(session('status'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul class="mb-0 list-unstyled">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="username" class="form-label">Nom d'utilisateur</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autofocus placeholder="Votre nom d'utilisateur">
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted mt-2">Utilisez votre nom d'utilisateur (exemple: superadmin)</small>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="password" class="form-label mb-0">Mot de passe</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small text-primary">Mot de passe oublié ?</a>
                                @endif
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Votre mot de passe">
                                <button type="button" class="btn btn-outline-secondary toggle-password" onclick="togglePasswordVisibility()">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="password-strength-meter" id="strengthMeter"></div>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary animate-pulse">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </button>
                        </div>
                        
                        <div class="custom-separator">
                            <span>ou</span>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ url('/') }}#demo" class="btn btn-outline-primary w-100">
                                <i class="fas fa-play-circle me-2"></i>Demander une démo
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted">&copy; {{ date('Y') }} KLASSCI. Tous droits réservés.</p>
                    </div>
                </div>
            </div>
            
            <div class="login-features" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-item">
                    <i class="fas fa-lock"></i>
                    <span>Connexion sécurisée</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Protection des données</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <span>Support 24/7</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init();
            
            // Ajout de la force du mot de passe
            const passwordInput = document.getElementById('password');
            const strengthMeter = document.getElementById('strengthMeter');
            
            if (passwordInput && strengthMeter) {
                passwordInput.addEventListener('input', function() {
                    const val = passwordInput.value;
                    const strengthValue = calculatePasswordStrength(val);
                    
                    strengthMeter.style.width = strengthValue + '%';
                    
                    if (strengthValue < 30) {
                        strengthMeter.style.backgroundColor = '#ef4444';
                    } else if (strengthValue < 60) {
                        strengthMeter.style.backgroundColor = '#f59e0b';
                    } else {
                        strengthMeter.style.backgroundColor = '#22c55e';
                    }
                });
                
                // Fonction pour calculer la force du mot de passe
                function calculatePasswordStrength(password) {
                    if (!password) return 0;
                    
                    let strength = 0;
                    
                    // Longueur minimale
                    if (password.length >= 8) strength += 25;
                    
                    // Complexité
                    if (/[A-Z]/.test(password)) strength += 25;
                    if (/[0-9]/.test(password)) strength += 25;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                    
                    return strength;
                }
            }
        });
        
        // Fonction pour afficher/masquer le mot de passe
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('fa-eye');
                toggleButton.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('fa-eye-slash');
                toggleButton.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
