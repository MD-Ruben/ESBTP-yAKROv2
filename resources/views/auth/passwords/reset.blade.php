<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KLASSCI') }} - Réinitialisation du mot de passe</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Animation CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <style>
        :root {
            /* KLASSCI Color Palette */
            --klassci-primary: #6366f1; /* Indigo */
            --klassci-secondary: #ec4899; /* Pink */
            --klassci-success: #22c55e; /* Green */
            --klassci-warning: #f59e0b; /* Amber */
            --klassci-danger: #ef4444; /* Red */
            --klassci-info: #0ea5e9; /* Light Blue */
            --klassci-light: #f8fafc;
            --klassci-dark: #0f172a;
            --klassci-gradient: linear-gradient(135deg, var(--klassci-primary), var(--klassci-secondary));
            --klassci-border-radius: 12px;
            --klassci-box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.1), 0 10px 10px -5px rgba(99, 102, 241, 0.05);
        }
        
        body {
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(236, 72, 153, 0.05)), url('/images/pattern-bg.png');
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated shapes */
        .shape {
            position: absolute;
            z-index: -1;
            border-radius: 50%;
            opacity: 0.4;
        }
        
        .shape-1 {
            top: 20%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(var(--klassci-primary), transparent 70%);
            animation: float 15s infinite alternate;
        }
        
        .shape-2 {
            bottom: 10%;
            right: 5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(var(--klassci-secondary), transparent 70%);
            animation: float 18s infinite alternate-reverse;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg) scale(1); }
            33% { transform: translate(30px, -50px) rotate(10deg) scale(1.05); }
            66% { transform: translate(-20px, 20px) rotate(-5deg) scale(0.95); }
            100% { transform: translate(0, 0) rotate(0deg) scale(1); }
        }
        
        .reset-container {
            width: 100%;
            max-width: 500px;
            perspective: 1000px;
        }
        
        .card {
            border-radius: var(--klassci-border-radius);
            box-shadow: var(--klassci-box-shadow);
            border: none;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transform-style: preserve-3d;
            transition: all 0.5s ease;
        }
        
        .card:hover {
            box-shadow: 0 20px 30px -10px rgba(99, 102, 241, 0.2), 0 10px 20px -5px rgba(99, 102, 241, 0.1);
            transform: translateY(-5px);
        }
        
        .card-header {
            background: var(--klassci-gradient);
            color: white;
            border-radius: var(--klassci-border-radius) var(--klassci-border-radius) 0 0 !important;
            padding: 30px 20px;
            text-align: center;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
            transform: rotate(30deg);
        }
        
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .logo-img {
            height: 60px;
            object-fit: contain;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .btn-primary {
            background: var(--klassci-gradient);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }
        
        .form-control {
            padding: 14px 16px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--klassci-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background-color: white;
        }
        
        .input-group-text {
            border-radius: 10px 0 0 10px;
            background-color: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            color: var(--klassci-primary);
        }
        
        .form-control {
            border-radius: 0 10px 10px 0;
        }
        
        .login-link {
            color: var(--klassci-primary);
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .login-link:hover {
            color: var(--klassci-secondary);
            transform: translateX(-5px);
        }
        
        .login-link i {
            margin-right: 8px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(34, 197, 94, 0.1);
            color: var(--klassci-success);
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--klassci-danger);
        }
        
        /* Animation pour les champs du formulaire */
        .fade-in {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .delay-1 {
            transition-delay: 0.1s;
        }
        
        .delay-2 {
            transition-delay: 0.2s;
        }
        
        .delay-3 {
            transition-delay: 0.3s;
        }
        
        /* Animation de l'icône de clé */
        .reset-icon {
            display: inline-block;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 15px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        /* Effet de force du mot de passe */
        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 5px;
            background: #e2e8f0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .password-strength-meter {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 5px;
        }
        
        .strength-weak {
            width: 33%;
            background: var(--klassci-danger);
        }
        
        .strength-medium {
            width: 66%;
            background: var(--klassci-warning);
        }
        
        .strength-strong {
            width: 100%;
            background: var(--klassci-success);
        }
    </style>
</head>
<body>
    <!-- Animated background shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="reset-container">
        <div class="card" data-aos="fade-up">
            <div class="card-header">
                <div class="logo-container">
                    <img src="{{ asset('images/LOGO-KLASSCI-PNG.png') }}" alt="KLASSCI Logo" class="logo-img">
                </div>
                <i class="fas fa-key reset-icon"></i>
                <h3 class="mb-0">Réinitialisation du mot de passe</h3>
                <p class="text-white-50 mt-2">Créez un nouveau mot de passe sécurisé</p>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="mb-4 fade-in active">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus readonly>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3 fade-in active delay-1">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="password-strength mt-2">
                            <div class="password-strength-meter" id="passwordStrengthMeter"></div>
                        </div>
                        <small id="passwordHelpBlock" class="form-text text-muted mt-1"></small>
                    </div>
                    
                    <div class="mb-4 fade-in active delay-2">
                        <label for="password-confirm" class="form-label">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-4 fade-in active delay-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Réinitialiser le mot de passe
                        </button>
                    </div>
                    
                    <div class="text-center fade-in active delay-3">
                        <a href="{{ route('login') }}" class="login-link">
                            <i class="fas fa-arrow-left"></i> Retour à la connexion
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser AOS
            AOS.init({
                duration: 800,
                easing: 'ease-out',
                once: true
            });
            
            // Vérification de la force du mot de passe
            const passwordInput = document.getElementById('password');
            const meterElement = document.getElementById('passwordStrengthMeter');
            const helpTextElement = document.getElementById('passwordHelpBlock');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Vérification de la longueur
                if (password.length >= 8) strength += 1;
                
                // Vérification de la présence de lettres et de chiffres
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
                
                // Vérification de la présence de caractères spéciaux
                if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
                
                // Mise à jour de l'indicateur
                meterElement.className = 'password-strength-meter';
                
                if (password === '') {
                    meterElement.style.width = '0';
                    helpTextElement.textContent = '';
                } else if (strength === 1) {
                    meterElement.classList.add('strength-weak');
                    helpTextElement.textContent = 'Mot de passe faible - Essayez d\'ajouter des majuscules et des caractères spéciaux';
                    helpTextElement.style.color = 'var(--klassci-danger)';
                } else if (strength === 2) {
                    meterElement.classList.add('strength-medium');
                    helpTextElement.textContent = 'Mot de passe moyen - Ajoutez des caractères spéciaux pour plus de sécurité';
                    helpTextElement.style.color = 'var(--klassci-warning)';
                } else if (strength === 3) {
                    meterElement.classList.add('strength-strong');
                    helpTextElement.textContent = 'Mot de passe fort - Parfait !';
                    helpTextElement.style.color = 'var(--klassci-success)';
                }
            });
        });
    </script>
</body>
</html> 