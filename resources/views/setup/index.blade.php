<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - ESBTP School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .setup-container {
            max-width: 900px;
            margin: 2rem auto;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .setup-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .setup-header h1 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .setup-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        .setup-body {
            padding: 2rem;
            background-color: white;
        }
        .setup-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .setup-steps::before {
            content: '';
            position: absolute;
            top: 1.5rem;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }
        .step {
            position: relative;
            z-index: 2;
            background-color: white;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e9ecef;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        .step.completed {
            border-color: var(--success-color);
            background-color: var(--success-color);
            color: white;
        }
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .form-floating {
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 1rem;
            cursor: pointer;
            color: #6c757d;
        }
        .requirements-list {
            list-style-type: none;
            padding-left: 0;
        }
        .requirements-list li {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }
        .requirements-list li i {
            position: absolute;
            left: 0;
            top: 0.25rem;
        }
        .requirements-list .success {
            color: var(--success-color);
        }
        .requirements-list .warning {
            color: var(--warning-color);
        }
        .requirements-list .danger {
            color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>ESBTP School Management System</h1>
            <p>Assistant d'installation</p>
        </div>
        
        <div class="setup-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="setup-steps">
                <div class="step active" id="step1-indicator">1</div>
                <div class="step" id="step2-indicator">2</div>
                <div class="step" id="step3-indicator">3</div>
                <div class="step" id="step4-indicator">4</div>
            </div>

            <!-- Étape 1: Vérification des prérequis -->
            <div class="step-content active" id="step1-content">
                <h3 class="mb-4">Vérification des prérequis</h3>
                
                <div class="mb-4">
                    <h5>Statut du serveur</h5>
                    <ul class="requirements-list" id="server-requirements">
                        <li><i class="fas fa-spinner fa-spin"></i> Vérification de la version PHP...</li>
                        <li><i class="fas fa-spinner fa-spin"></i> Vérification des extensions PHP...</li>
                        <li><i class="fas fa-spinner fa-spin"></i> Vérification des permissions des dossiers...</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h5>Statut de la base de données</h5>
                    @if (isset($dbStatus['connected']) && $dbStatus['connected'])
                        <div class="alert alert-success">
                            <p><i class="fas fa-check-circle"></i> <strong>Connecté à:</strong> {{ $dbStatus['name'] }}</p>
                            <p><strong>Nombre de tables:</strong> {{ $dbStatus['tables_count'] }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <p><i class="fas fa-exclamation-triangle"></i> Non connecté à la base de données</p>
                            @if (isset($dbStatus['error']))
                                <p><strong>Erreur:</strong> {{ $dbStatus['error'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between">
                    <button class="btn btn-secondary" disabled>Précédent</button>
                    <button class="btn btn-primary" id="step1-next">Suivant</button>
                </div>
            </div>

            <!-- Étape 2: Configuration de la base de données -->
            <div class="step-content" id="step2-content">
                <h3 class="mb-4">Configuration de la base de données</h3>
                
                <form id="db-config-form" method="POST" action="{{ route('setup.setup') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="db_connection" class="form-label">Type de connexion</label>
                        <select name="db_connection" id="db_connection" class="form-select" required>
                            <option value="mysql">MySQL</option>
                            <option value="pgsql">PostgreSQL</option>
                            <option value="sqlite">SQLite</option>
                        </select>
                    </div>

                    <div class="db-config mysql pgsql">
                        <div class="form-floating mb-3">
                            <input type="text" name="db_host" id="db_host" class="form-control" placeholder="Hôte" value="127.0.0.1">
                            <label for="db_host">Hôte</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" name="db_port" id="db_port" class="form-control" placeholder="Port" value="3306">
                            <label for="db_port">Port</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" name="db_username" id="db_username" class="form-control" placeholder="Nom d'utilisateur" value="root">
                            <label for="db_username">Nom d'utilisateur</label>
                        </div>

                        <div class="form-floating mb-3 password-container">
                            <input type="password" name="db_password" id="db_password" class="form-control" placeholder="Mot de passe">
                            <label for="db_password">Mot de passe</label>
                            <span class="password-toggle" onclick="togglePasswordVisibility('db_password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="db_database" id="db_database" class="form-control" placeholder="Nom de la base de données" value="smart_school_db" required>
                        <label for="db_database">Nom de la base de données</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="run_migrations" id="run_migrations" class="form-check-input" checked>
                        <label for="run_migrations" class="form-check-label">Exécuter les migrations</label>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="run_seeders" id="run_seeders" class="form-check-input" checked>
                        <label for="run_seeders" class="form-check-label">Exécuter les seeders (données initiales)</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="step2-prev">Précédent</button>
                        <button type="button" class="btn btn-primary" id="step2-next">Suivant</button>
                    </div>
                </form>
            </div>

            <!-- Étape 3: Création du compte administrateur -->
            <div class="step-content" id="step3-content">
                <h3 class="mb-4">Création du compte administrateur</h3>
                
                <form id="admin-form" method="POST" action="{{ route('setup.create-admin') }}">
                    @csrf
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="admin_name" class="form-control" placeholder="Nom complet" required>
                        <label for="admin_name">Nom complet</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="admin_email" class="form-control" placeholder="Adresse e-mail" required>
                        <label for="admin_email">Adresse e-mail</label>
                    </div>

                    <div class="form-floating mb-3 password-container">
                        <input type="password" name="password" id="admin_password" class="form-control" placeholder="Mot de passe" required>
                        <label for="admin_password">Mot de passe</label>
                        <span class="password-toggle" onclick="togglePasswordVisibility('admin_password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="form-floating mb-3 password-container">
                        <input type="password" name="password_confirmation" id="admin_password_confirmation" class="form-control" placeholder="Confirmer le mot de passe" required>
                        <label for="admin_password_confirmation">Confirmer le mot de passe</label>
                        <span class="password-toggle" onclick="togglePasswordVisibility('admin_password_confirmation')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="progress">
                            <div class="progress-bar" id="password-strength" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small id="password-strength-text" class="form-text text-muted">Force du mot de passe</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="step3-prev">Précédent</button>
                        <button type="button" class="btn btn-primary" id="step3-next">Suivant</button>
                    </div>
                </form>
            </div>

            <!-- Étape 4: Finalisation -->
            <div class="step-content" id="step4-content">
                <h3 class="mb-4">Finalisation de l'installation</h3>
                
                <div class="alert alert-info">
                    <p><i class="fas fa-info-circle"></i> L'installation est presque terminée. Cliquez sur le bouton ci-dessous pour finaliser l'installation.</p>
                </div>

                <div class="mb-4">
                    <h5>Récapitulatif</h5>
                    <ul>
                        <li>Base de données: <span id="recap-db-name"></span></li>
                        <li>Administrateur: <span id="recap-admin-name"></span> (<span id="recap-admin-email"></span>)</li>
                    </ul>
                </div>

                <form id="finalize-form" method="POST" action="{{ route('setup.finalize') }}">
                    @csrf
                    <div class="mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="run_composer" id="run_composer" class="form-check-input">
                            <label for="run_composer" class="form-check-label">Installer les dépendances via Composer</label>
                            <small class="form-text text-muted d-block">Cochez cette option si vous venez d'installer l'application ou si vous avez besoin de mettre à jour les dépendances. Cette opération peut prendre plusieurs minutes.</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="step4-prev">Précédent</button>
                        <button type="submit" class="btn btn-success">Finaliser l'installation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour basculer entre les étapes
        function goToStep(step) {
            // Masquer toutes les étapes
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Afficher l'étape demandée
            document.getElementById(`step${step}-content`).classList.add('active');
            
            // Mettre à jour les indicateurs d'étape
            document.querySelectorAll('.step').forEach((indicator, index) => {
                if (index + 1 < step) {
                    indicator.classList.remove('active');
                    indicator.classList.add('completed');
                    indicator.innerHTML = '<i class="fas fa-check"></i>';
                } else if (index + 1 === step) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                    indicator.innerHTML = step;
                } else {
                    indicator.classList.remove('active', 'completed');
                    indicator.innerHTML = index + 1;
                }
            });
        }

        // Fonction pour basculer la visibilité du mot de passe
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Fonction pour vérifier la force du mot de passe
        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]+/)) strength += 25;
            if (password.match(/[A-Z]+/)) strength += 25;
            if (password.match(/[0-9]+/)) strength += 25;
            
            return strength;
        }

        // Vérification des prérequis au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Vérifier les prérequis
            fetch('{{ route("setup.check-requirements") }}')
                .then(response => response.json())
                .then(data => {
                    const requirementsList = document.getElementById('server-requirements');
                    requirementsList.innerHTML = '';
                    
                    // PHP Version
                    const phpItem = document.createElement('li');
                    if (data.php_version.status) {
                        phpItem.innerHTML = `<i class="fas fa-check success"></i> ${data.php_version.message}`;
                    } else {
                        phpItem.innerHTML = `<i class="fas fa-times danger"></i> ${data.php_version.message}`;
                    }
                    requirementsList.appendChild(phpItem);
                    
                    // Extensions
                    const extItem = document.createElement('li');
                    if (data.extensions.status) {
                        extItem.innerHTML = `<i class="fas fa-check success"></i> ${data.extensions.message}`;
                    } else {
                        extItem.innerHTML = `<i class="fas fa-times danger"></i> ${data.extensions.message}`;
                    }
                    requirementsList.appendChild(extItem);
                    
                    // Writable directories
                    const dirItem = document.createElement('li');
                    if (data.writable_dirs.status) {
                        dirItem.innerHTML = `<i class="fas fa-check success"></i> ${data.writable_dirs.message}`;
                    } else {
                        dirItem.innerHTML = `<i class="fas fa-times danger"></i> ${data.writable_dirs.message}`;
                    }
                    requirementsList.appendChild(dirItem);
                });
            
            // Événements pour la navigation entre les étapes
            document.getElementById('step1-next').addEventListener('click', () => goToStep(2));
            document.getElementById('step2-prev').addEventListener('click', () => goToStep(1));
            document.getElementById('step2-next').addEventListener('click', () => {
                // Stocker les informations de la base de données pour le récapitulatif
                document.getElementById('recap-db-name').textContent = document.getElementById('db_database').value;
                goToStep(3);
            });
            document.getElementById('step3-prev').addEventListener('click', () => goToStep(2));
            document.getElementById('step3-next').addEventListener('click', () => {
                // Stocker les informations de l'administrateur pour le récapitulatif
                document.getElementById('recap-admin-name').textContent = document.getElementById('admin_name').value;
                document.getElementById('recap-admin-email').textContent = document.getElementById('admin_email').value;
                goToStep(4);
            });
            document.getElementById('step4-prev').addEventListener('click', () => goToStep(3));
            
            // Événement pour le changement de type de connexion à la base de données
            document.getElementById('db_connection').addEventListener('change', function() {
                const connection = this.value;
                const dbConfigElements = document.querySelectorAll('.db-config');
                
                dbConfigElements.forEach(el => {
                    if (el.classList.contains(connection)) {
                        el.style.display = 'block';
                    } else {
                        el.style.display = 'none';
                    }
                });
            });
            
            // Événement pour vérifier la force du mot de passe
            document.getElementById('admin_password').addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                const progressBar = document.getElementById('password-strength');
                const strengthText = document.getElementById('password-strength-text');
                
                progressBar.style.width = `${strength}%`;
                
                if (strength < 25) {
                    progressBar.className = 'progress-bar bg-danger';
                    strengthText.textContent = 'Mot de passe très faible';
                } else if (strength < 50) {
                    progressBar.className = 'progress-bar bg-warning';
                    strengthText.textContent = 'Mot de passe faible';
                } else if (strength < 75) {
                    progressBar.className = 'progress-bar bg-info';
                    strengthText.textContent = 'Mot de passe moyen';
                } else {
                    progressBar.className = 'progress-bar bg-success';
                    strengthText.textContent = 'Mot de passe fort';
                }
            });
            
            // Soumission des formulaires via AJAX
            document.getElementById('db-config-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        goToStep(3);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
            
            document.getElementById('admin-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        goToStep(4);
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html> 