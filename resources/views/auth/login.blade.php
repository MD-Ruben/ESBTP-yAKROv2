<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ESBTP') }} - Connexion</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --esbtp-orange: #f29400;
            --esbtp-green: #01632f;
            --esbtp-white: #ffffff;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            background-image: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), url('/images/school-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .login-container {
            max-width: 450px;
            margin: 80px auto;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--esbtp-green);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 25px 20px;
            text-align: center;
            border-bottom: 4px solid var(--esbtp-orange);
        }
        
        .school-logo {
            max-width: 80px;
            margin-bottom: 15px;
        }
        
        .btn-primary {
            background-color: var(--esbtp-orange);
            border-color: var(--esbtp-orange);
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--esbtp-green);
            border-color: var(--esbtp-green);
            transform: translateY(-2px);
        }
        
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .form-control:focus {
            border-color: var(--esbtp-orange);
            box-shadow: 0 0 0 0.2rem rgba(242, 148, 0, 0.25);
        }
        
        .btn-link {
            color: var(--esbtp-green);
            text-decoration: none;
        }
        
        .btn-link:hover {
            color: var(--esbtp-orange);
        }
        
        .home-link {
            color: var(--esbtp-green);
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .home-link:hover {
            color: var(--esbtp-orange);
            transform: translateX(-5px);
        }
        
        .home-link i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header">
                    <!-- Si vous avez un logo, vous pouvez l'ajouter ici -->
                    <!-- <img src="/images/esbtp-logo.png" alt="ESBTP Logo" class="school-logo"> -->
                    <h3 class="mb-0">École Supérieure du Bâtiment et des Travaux Publics</h3>
                    <p class="mb-0 mt-2">Système de Gestion Universitaire</p>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success mb-3" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="votre@email.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i> Connexion
                            </button>
                        </div>

                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    <i class="fas fa-key me-1"></i> Mot de passe oublié ?
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('welcome') }}" class="home-link">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 