<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration de l'application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Configuration de la base de données</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-4">
                            <h4>Statut actuel de la base de données</h4>
                            @if ($dbStatus['connected'])
                                <div class="alert alert-success">
                                    <p><strong>Connecté à:</strong> {{ $dbStatus['name'] }}</p>
                                    <p><strong>Nombre de tables:</strong> {{ $dbStatus['tables_count'] }}</p>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <p>Non connecté à la base de données</p>
                                    <p><strong>Erreur:</strong> {{ $dbStatus['error'] }}</p>
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('setup.setup') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="db_connection" class="form-label">Type de connexion</label>
                                <select name="db_connection" id="db_connection" class="form-control" required>
                                    <option value="mysql">MySQL</option>
                                    <option value="pgsql">PostgreSQL</option>
                                    <option value="sqlite">SQLite</option>
                                </select>
                            </div>

                            <div class="db-config mysql pgsql">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Hôte</label>
                                    <input type="text" name="db_host" id="db_host" class="form-control" value="127.0.0.1">
                                </div>

                                <div class="mb-3">
                                    <label for="db_port" class="form-label">Port</label>
                                    <input type="text" name="db_port" id="db_port" class="form-control" value="3306">
                                </div>

                                <div class="mb-3">
                                    <label for="db_username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" name="db_username" id="db_username" class="form-control" value="root">
                                </div>

                                <div class="mb-3">
                                    <label for="db_password" class="form-label">Mot de passe</label>
                                    <input type="password" name="db_password" id="db_password" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="db_database" class="form-label">Nom de la base de données</label>
                                <input type="text" name="db_database" id="db_database" class="form-control" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="run_migrations" id="run_migrations" class="form-check-input" checked>
                                <label for="run_migrations" class="form-check-label">Exécuter les migrations</label>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="run_seeders" id="run_seeders" class="form-check-input">
                                <label for="run_seeders" class="form-check-label">Exécuter les seeders</label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Configurer la base de données</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html> 