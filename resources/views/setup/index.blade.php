<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Installation - {{ config('app.name', 'Smart School') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        .setup-container {
            max-width: 800px;
            margin: 50px auto;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .setup-logo {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3490dc;
            margin-bottom: 10px;
        }
        .setup-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .setup-steps:before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }
        .step.active {
            background: #3490dc;
            color: white;
        }
        .step.completed {
            background: #38c172;
            color: white;
        }
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .step-title {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .btn-next, .btn-prev {
            min-width: 100px;
        }
        .setup-footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
        }
        .requirements-list {
            margin-bottom: 20px;
        }
        .requirement-item {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .requirement-item i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .requirement-success {
            background-color: rgba(56, 193, 114, 0.1);
            color: #38c172;
        }
        .requirement-error {
            background-color: rgba(227, 52, 47, 0.1);
            color: #e3342f;
        }
        .console {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            height: 200px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .console p {
            margin: 0;
            line-height: 1.5;
        }
        .console .success {
            color: #38c172;
        }
        .console .error {
            color: #e3342f;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <div class="setup-logo">ESBTP</div>
            <h2>Installation du système de gestion scolaire</h2>
            <p class="text-muted">Suivez les étapes ci-dessous pour configurer votre application</p>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="setup-steps">
                    <div class="step active" id="step-indicator-1">1</div>
                    <div class="step" id="step-indicator-2">2</div>
                    <div class="step" id="step-indicator-3">3</div>
                    <div class="step" id="step-indicator-4">4</div>
                </div>

                <!-- Étape 1: Bienvenue -->
                <div class="step-content active" id="step-1">
                    <h3 class="step-title">Bienvenue à l'installation</h3>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Ce processus va vous guider à travers l'installation du système de gestion scolaire ESBTP.
                    </div>
                    <p>Avant de commencer, assurez-vous que :</p>
                    <ul>
                        <li>Votre serveur web est correctement configuré</li>
                        <li>Votre base de données est créée et accessible</li>
                        <li>Les informations de connexion à la base de données sont correctement configurées dans le fichier .env</li>
                    </ul>
                    <p>Cliquez sur "Suivant" pour vérifier les prérequis du système.</p>
                </div>

                <!-- Étape 2: Vérification des prérequis -->
                <div class="step-content" id="step-2">
                    <h3 class="step-title">Vérification des prérequis</h3>
                    <div class="requirements-list">
                        <div class="requirement-item" id="php-version">
                            <i class="fas fa-spinner fa-spin"></i>
                            Vérification de la version PHP...
                        </div>
                        <div class="requirement-item" id="db-connection">
                            <i class="fas fa-spinner fa-spin"></i>
                            Vérification de la connexion à la base de données...
                        </div>
                        <div class="requirement-item" id="writable-dirs">
                            <i class="fas fa-spinner fa-spin"></i>
                            Vérification des permissions d'écriture...
                        </div>
                        <div class="requirement-item" id="required-extensions">
                            <i class="fas fa-spinner fa-spin"></i>
                            Vérification des extensions PHP requises...
                        </div>
                    </div>
                    <div class="alert alert-warning d-none" id="requirements-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Certains prérequis ne sont pas satisfaits. Vous pouvez continuer, mais l'application pourrait ne pas fonctionner correctement.
                    </div>
                </div>

                <!-- Étape 3: Configuration de la base de données -->
                <div class="step-content" id="step-3">
                    <h3 class="step-title">Configuration de la base de données</h3>
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Nous allons maintenant exécuter les migrations pour créer les tables nécessaires dans votre base de données.
                    </div>
                    <div class="console" id="migration-console">
                        <p>Prêt à exécuter les migrations...</p>
                    </div>
                    <button type="button" class="btn btn-primary" id="run-migrations">
                        <i class="fas fa-database me-2"></i>Exécuter les migrations
                    </button>
                </div>

                <!-- Étape 4: Création de l'administrateur -->
                <div class="step-content" id="step-4">
                    <h3 class="step-title">Création du compte administrateur</h3>
                    <p>Veuillez créer un compte administrateur pour accéder au système.</p>
                    <form id="admin-form">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="alert alert-success d-none" id="admin-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Compte administrateur créé avec succès! L'installation est terminée.
                        </div>
                        <div class="alert alert-danger d-none" id="admin-error">
                            <i class="fas fa-times-circle me-2"></i>
                            Une erreur s'est produite lors de la création du compte administrateur.
                        </div>
                    </form>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary btn-prev" id="prev-btn" disabled>
                        <i class="fas fa-arrow-left me-2"></i>Précédent
                    </button>
                    <button type="button" class="btn btn-primary btn-next" id="next-btn">
                        Suivant<i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="setup-footer">
            <p>&copy; {{ date('Y') }} École Supérieure du Bâtiment et des Travaux Publics. Tous droits réservés.</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Variables
            let currentStep = 1;
            const totalSteps = 4;
            
            // Fonctions pour la navigation entre les étapes
            function showStep(step) {
                $('.step-content').removeClass('active');
                $(`#step-${step}`).addClass('active');
                
                // Mettre à jour les indicateurs d'étape
                $('.step').removeClass('active completed');
                for (let i = 1; i <= totalSteps; i++) {
                    if (i < step) {
                        $(`#step-indicator-${i}`).addClass('completed');
                    } else if (i === step) {
                        $(`#step-indicator-${i}`).addClass('active');
                    }
                }
                
                // Gérer les boutons précédent/suivant
                if (step === 1) {
                    $('#prev-btn').prop('disabled', true);
                } else {
                    $('#prev-btn').prop('disabled', false);
                }
                
                if (step === totalSteps) {
                    $('#next-btn').text('Terminer').addClass('btn-success');
                } else {
                    $('#next-btn').text('Suivant').removeClass('btn-success');
                    $('#next-btn').html('Suivant<i class="fas fa-arrow-right ms-2"></i>');
                }
                
                // Actions spécifiques à chaque étape
                if (step === 2) {
                    checkRequirements();
                }
            }
            
            // Événements des boutons
            $('#next-btn').click(function() {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                } else {
                    // Soumettre le formulaire d'administrateur
                    submitAdminForm();
                }
            });
            
            $('#prev-btn').click(function() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            
            // Vérification des prérequis
            function checkRequirements() {
                // Simuler la vérification des prérequis (à remplacer par des appels AJAX réels)
                setTimeout(() => {
                    $('#php-version').html('<i class="fas fa-check text-success"></i> PHP version 7.4 ou supérieure').addClass('requirement-success');
                }, 500);
                
                setTimeout(() => {
                    $('#db-connection').html('<i class="fas fa-check text-success"></i> Connexion à la base de données établie').addClass('requirement-success');
                }, 1000);
                
                setTimeout(() => {
                    $('#writable-dirs').html('<i class="fas fa-check text-success"></i> Permissions d\'écriture correctes').addClass('requirement-success');
                }, 1500);
                
                setTimeout(() => {
                    $('#required-extensions').html('<i class="fas fa-check text-success"></i> Extensions PHP requises installées').addClass('requirement-success');
                }, 2000);
            }
            
            // Exécution des migrations
            $('#run-migrations').click(function() {
                const $button = $(this);
                const $console = $('#migration-console');
                
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Exécution en cours...');
                $console.append('<p>Démarrage des migrations...</p>');
                
                // Appel AJAX pour exécuter les migrations
                $.ajax({
                    url: '{{ route("setup.migrate") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $console.append('<p class="success">Migrations exécutées avec succès!</p>');
                        $button.html('<i class="fas fa-check me-2"></i>Migrations terminées').removeClass('btn-primary').addClass('btn-success');
                        
                        // Activer le bouton suivant
                        $('#next-btn').prop('disabled', false);
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Une erreur s\'est produite';
                        $console.append(`<p class="error">Erreur: ${errorMessage}</p>`);
                        $button.html('<i class="fas fa-redo me-2"></i>Réessayer').prop('disabled', false);
                    },
                    complete: function() {
                        // Faire défiler la console vers le bas
                        $console.scrollTop($console[0].scrollHeight);
                    }
                });
            });
            
            // Soumission du formulaire d'administrateur
            function submitAdminForm() {
                const $form = $('#admin-form');
                const $nextBtn = $('#next-btn');
                const formData = {
                    name: $('#name').val(),
                    email: $('#email').val(),
                    password: $('#password').val(),
                    password_confirmation: $('#password_confirmation').val()
                };
                
                // Réinitialiser les messages d'erreur
                $('.invalid-feedback').hide();
                $('.is-invalid').removeClass('is-invalid');
                $('#admin-success, #admin-error').addClass('d-none');
                
                $nextBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...');
                
                // Appel AJAX pour créer l'administrateur
                $.ajax({
                    url: '{{ route("setup.create-admin") }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#admin-success').removeClass('d-none');
                        $nextBtn.html('<i class="fas fa-sign-in-alt me-2"></i>Accéder à l\'application');
                        
                        // Finaliser l'installation
                        finalizeSetup();
                    },
                    error: function(xhr) {
                        $('#admin-error').removeClass('d-none').text('Erreur: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Une erreur s\'est produite'));
                        
                        // Afficher les erreurs de validation
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            for (const field in errors) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(errors[field][0]).show();
                            }
                        }
                        
                        $nextBtn.prop('disabled', false).html('Terminer');
                    }
                });
            }
            
            // Finalisation de l'installation
            function finalizeSetup() {
                $.ajax({
                    url: '{{ route("setup.finalize") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Rediriger vers la page de connexion après un court délai
                        setTimeout(() => {
                            window.location.href = response.redirect || '/login';
                        }, 2000);
                    }
                });
            }
            
            // Initialisation
            showStep(currentStep);
        });
    </script>
</body>
</html> 