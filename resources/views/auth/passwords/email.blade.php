<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ESBTP') }} - Réinitialisation du mot de passe</title>

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
        
        .reset-container {
            max-width: 500px;
            margin: 100px auto;
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
        
        .login-link {
            color: var(--esbtp-green);
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-link:hover {
            color: var(--esbtp-orange);
            transform: translateX(-5px);
        }
        
        .login-link i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Réinitialisation du mot de passe</h3>
                    <p class="mb-0 mt-2">ESBTP - Système de Gestion Universitaire</p>
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

                    <p class="mb-4">Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="votre@email.com">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le lien de réinitialisation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="login-link">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 