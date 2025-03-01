<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use App\Helpers\InstallationHelper;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class InstallController extends Controller
{
    /**
     * Display the installation welcome page
     */
    public function index()
    {
        // Vérifier si l'application est déjà installée ET s'il existe un utilisateur admin
        $installationStatus = InstallationHelper::getInstallationStatus();
        $hasAdminUser = InstallationHelper::hasAdminUser();
        
        // Journaliser l'état pour le débogage
        \Log::info("InstallController index - Installation status: " . 
                  ($installationStatus['installed'] ? "Installed" : "Not installed") . 
                  ", Match: {$installationStatus['match_percentage']}%, Admin user: " . 
                  ($hasAdminUser ? "Yes" : "No"));
        
        // Ne rediriger vers login que si l'application est installée ET qu'un admin existe
        if ($installationStatus['installed'] && $hasAdminUser) {
            \Log::info("InstallController index - Redirecting to login (installed and admin exists)");
            return redirect('/login');
        }

        return view('install.welcome');
    }

    /**
     * Display the database configuration page
     */
    public function database()
    {
        // Vérifier si l'application est déjà installée ET s'il existe un utilisateur admin
        $installationStatus = InstallationHelper::getInstallationStatus();
        $hasAdminUser = InstallationHelper::hasAdminUser();
        
        // Ne rediriger vers login que si l'application est installée ET qu'un admin existe
        if ($installationStatus['installed'] && $hasAdminUser) {
            \Log::info("InstallController database - Redirecting to login (installed and admin exists)");
            return redirect('/login');
        }

        return view('install.database');
    }

    /**
     * Process the database configuration
     */
    public function setupDatabase(Request $request)
    {
        // Validate the request
        $request->validate([
            'host' => 'required',
            'port' => 'required',
            'database' => 'required',
            'username' => 'required',
            'password' => 'nullable',
        ]);

        try {
            // Debug information
            \Log::info('Setup database request received');
            \Log::info('Host: ' . $request->host);
            \Log::info('Database: ' . $request->database);
            
            // Test the database connection
            $connection = $this->testDatabaseConnection(
                $request->host,
                $request->port,
                $request->username,
                $request->password,
                $request->database
            );

            if ($connection['status'] === 'success') {
                // Update the .env file with database credentials
                $this->updateEnvironmentFile([
                    'DB_HOST' => $request->host,
                    'DB_PORT' => $request->port,
                    'DB_DATABASE' => $request->database,
                    'DB_USERNAME' => $request->username,
                    'DB_PASSWORD' => $request->password,
                ]);

                // Clear config cache and regenerate config cache
                Artisan::call('config:clear');
                
                // Store database configuration in session to ensure it's available
                session([
                    'db_configured' => true,
                    'db_host' => $request->host,
                    'db_port' => $request->port,
                    'db_database' => $request->database,
                    'db_username' => $request->username,
                    'db_password' => $request->password,
                ]);
                
                // Debug information
                \Log::info('Database connection successful');
                \Log::info('Session db_configured set to: ' . (session('db_configured') ? 'true' : 'false'));

                return response()->json([
                    'status' => 'success',
                    'message' => 'Database connection successful',
                    'database_exists' => $connection['database_exists'],
                    'tables_exist' => $connection['tables_exist'] ?? false,
                    'redirect' => route('install.migration')
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $connection['message']
            ], 422);
        } catch (Exception $e) {
            // Debug information
            \Log::error('Database connection failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the migration page
     */
    public function migration()
    {
        // Vérifier si l'application est déjà installée ET s'il existe un utilisateur admin
        $installationStatus = InstallationHelper::getInstallationStatus();
        $hasAdminUser = InstallationHelper::hasAdminUser();
        
        // Ne rediriger vers login que si l'application est installée ET qu'un admin existe
        if ($installationStatus['installed'] && $hasAdminUser) {
            \Log::info("InstallController migration - Redirecting to login (installed and admin exists)");
            return redirect('/login');
        }

        // Check if database is configured
        if (!session('db_configured')) {
            return redirect()->route('install.database')
                ->with('error', 'Veuillez configurer la base de données avant de continuer.');
        }
        
        // Check database status
        $dbStatus = $this->checkDatabaseStatus();
        
        // Get installation status to check migration match percentage
        $installationStatus = InstallationHelper::getInstallationStatus();
        
        // Check table status by module (category)
        $moduleStatus = InstallationHelper::checkTablesByCategory();
        
        // Get complete table status
        $allTablesStatus = InstallationHelper::checkAllRequiredTables();
        
        // Merge all status information
        $dbStatus = array_merge($dbStatus, [
            'match_percentage' => $installationStatus['match_percentage'],
            'can_skip_migration' => $installationStatus['match_percentage'] == 100,
            'installation_status' => $installationStatus,
            'module_status' => $moduleStatus,
            'all_tables_status' => $allTablesStatus
        ]);
        
        // Log the migration status
        \Log::info("Migration page - DB Status: " . json_encode($dbStatus));
        
        return view('install.migration', [
            'dbStatus' => $dbStatus
        ]);
    }

    /**
     * Exécute les migrations pour créer les tables dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function runMigration(Request $request)
    {
        try {
            $databaseExists = session('database_exists', false);
            $databaseCreated = session('database_created', false);
            $forceMigrate = $request->has('forceMigrate') ? (bool)$request->forceMigrate : false;
            $runSeeders = $request->has('runSeeders') ? (bool)$request->runSeeders : true; // Option pour exécuter les seeders
            $runESBTPSeeders = $request->has('runESBTPSeeders') ? (bool)$request->runESBTPSeeders : true; // Option pour les seeders ESBTP
            
            \Log::info("Migration lancée avec les options - Force: " . ($forceMigrate ? 'Oui' : 'Non') . 
                      ", Seeders: " . ($runSeeders ? 'Oui' : 'Non') . 
                      ", Seeders ESBTP: " . ($runESBTPSeeders ? 'Oui' : 'Non'));
            
            // Vérifier si on a une connexion à la base de données
            $dbConfigured = InstallationHelper::isDatabaseConfigured();
            if (!$dbConfigured) {
                \Log::error("Erreur de migration: Base de données non configurée");
                return response()->json([
                    'status' => 'error',
                    'message' => 'La base de données n\'est pas correctement configurée. Veuillez revenir à l\'étape précédente.'
                ]);
            }
            
            // Si la base de données n'existe pas, on la crée automatiquement
            if (!$databaseExists && !$databaseCreated) {
                \Log::info("La base de données n'existe pas ou n'a pas encore été créée. Tentative de création...");
                
                $dbConfig = config('database.connections.mysql');
                $created = $this->createDatabase($dbConfig['host'], $dbConfig['port'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);
                
                if (!$created) {
                    \Log::error("Échec de la création automatique de la base de données");
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Impossible de créer automatiquement la base de données. Veuillez la créer manuellement.'
                    ]);
                }
                
                \Log::info("Base de données créée avec succès");
                $databaseCreated = true;
                session(['database_created' => true]);
            }
            
            // Si la base vient d'être créée ou si force_migrate est vrai, exécuter les migrations
            if ($databaseCreated || $forceMigrate) {
                \Log::info("Exécution des migrations...");
                
                // Clear cache avant la migration
                \Artisan::call('config:clear');
                \Artisan::call('cache:clear');
                
                // Si force_migrate est vrai et que la base n'a pas été créée dans cette session, faire un wipe
                if ($forceMigrate && !$databaseCreated) {
                    \Log::info("Option force_migrate activée. Suppression de toutes les tables...");
                    \Artisan::call('db:wipe');
                }
                
                // Exécuter les migrations
                \Artisan::call('migrate', ['--force' => true]);
                $migrationOutput = \Artisan::output();
                \Log::info("Résultat des migrations: " . $migrationOutput);
                
                // Exécuter les seeders si l'option est activée
                if ($runSeeders) {
                    \Log::info("Exécution des seeders principaux...");
                    \Artisan::call('db:seed', [
                        '--class' => 'RoleSeeder',
                        '--force' => true
                    ]);
                    
                    // Exécuter les seeders ESBTP si l'option est activée
                    if ($runESBTPSeeders) {
                        \Log::info("Exécution des seeders ESBTP...");
                        
                        // Exécuter chaque seeder ESBTP individuellement pour une meilleure gestion des erreurs
                        $esbtpSeeders = [
                            'ESBTPFiliereSeeder',
                            'ESBTPNiveauEtudeSeeder',
                            'ESBTPAnneeUniversitaireSeeder'
                        ];
                        
                        foreach ($esbtpSeeders as $seeder) {
                            try {
                                \Artisan::call('db:seed', [
                                    '--class' => $seeder,
                                    '--force' => true
                                ]);
                                \Log::info("Seeder {$seeder} exécuté avec succès");
                            } catch (\Exception $e) {
                                \Log::error("Erreur lors de l'exécution du seeder {$seeder}: " . $e->getMessage());
                            }
                        }
                        
                        // Vérifier que les données ESBTP ont été correctement créées
                        $esbtpDataCheck = InstallationHelper::checkESBTPData();
                        session(['esbtp_data_check' => $esbtpDataCheck]);
                        
                        if (!$esbtpDataCheck['success']) {
                            \Log::warning("Certaines données ESBTP n'ont pas été correctement créées: " . 
                                         implode(', ', $esbtpDataCheck['missing_data']));
                        }
                    }
                }
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Migrations exécutées avec succès' . ($databaseCreated ? ' et base de données créée automatiquement' : ''),
                'database_created' => $databaseCreated,
                'esbtp_seeded' => $runESBTPSeeders,
                'esbtp_data_check' => session('esbtp_data_check', ['success' => true])
            ]);
        } catch (\Exception $e) {
            \Log::error("Erreur lors de l'exécution des migrations: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'exécution des migrations: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Vérifie l'état des migrations pour déterminer si on peut sauter cette étape
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkMigrations()
    {
        // Vérifier l'état de l'installation
        $installationStatus = InstallationHelper::getInstallationStatus();
        
        // Vérifier l'état des modules
        $moduleStatus = InstallationHelper::checkTablesByCategory();
        
        // Vérifier toutes les tables requises
        $allTablesStatus = InstallationHelper::checkAllRequiredTables();
        
        // Déterminer si on peut sauter la migration
        $canSkipMigration = false;
        
        // Critères pour autoriser le skip:
        // 1. Plus de 95% des tables sont créées
        // 2. OU tous les modules critiques sont complets (core, admin, user, school)
        if ($installationStatus['match_percentage'] >= 95) {
            $canSkipMigration = true;
            \Log::info("Migration skip autorisé par pourcentage: {$installationStatus['match_percentage']}%");
        } elseif (
            isset($moduleStatus['categories']['core']) && $moduleStatus['categories']['core']['complete'] &&
            isset($moduleStatus['categories']['admin']) && $moduleStatus['categories']['admin']['complete'] &&
            isset($moduleStatus['categories']['user']) && $moduleStatus['categories']['user']['complete'] &&
            isset($moduleStatus['categories']['school']) && $moduleStatus['categories']['school']['complete']
        ) {
            $canSkipMigration = true;
            \Log::info("Migration skip autorisé par modules critiques complets");
        }
        
        // Détails sur les tables manquantes
        $missingTables = [];
        if (!empty($allTablesStatus['missing_tables'])) {
            $missingTables = $allTablesStatus['missing_tables'];
        }
        
        // Retourner le résultat
        return response()->json([
            'can_skip_migration' => $canSkipMigration,
            'match_percentage' => $installationStatus['match_percentage'],
            'modules_status' => $moduleStatus,
            'all_tables_status' => [
                'missing_tables_count' => count($missingTables),
                'missing_tables' => $missingTables
            ],
            'message' => $canSkipMigration 
                ? 'La base de données est suffisamment complète pour continuer.' 
                : 'Des tables importantes sont manquantes. Veuillez exécuter les migrations.'
        ]);
    }

    /**
     * Display the admin creation page
     */
    public function admin()
    {
        // Vérifier si l'application est déjà installée ET s'il existe un utilisateur admin
        $installationStatus = InstallationHelper::getInstallationStatus();
        $hasAdminUser = InstallationHelper::hasAdminUser();
        
        // Ne rediriger vers login que si l'application est installée ET qu'un admin existe
        if ($installationStatus['installed'] && $hasAdminUser) {
            \Log::info("InstallController admin - Redirecting to login (installed and admin exists)");
            return redirect('/login');
        }

        // Check if database is migrated
        if (!$this->isDatabaseMigrated()) {
            return redirect()->route('install.migration')
                ->with('error', 'Please run the migrations first');
        }

        return view('install.admin');
    }

    /**
     * Create the admin user
     */
    public function setupAdmin(Request $request)
    {
        try {
            $validated = $request->validate([
            'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

            // Génération du nom d'utilisateur à partir de l'email si non fourni
            if (empty($validated['username'])) {
                $validated['username'] = explode('@', $validated['email'])[0];
            }
            
            // Création de l'utilisateur admin
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);
            
            // Attribuer le rôle admin
            $user->assignRole('admin');
            
            // Enregistrer les informations dans la session
            session(['admin_name' => $validated['name']]);
            session(['admin_email' => $validated['email']]);
            session(['admin_username' => $validated['username']]);
            session(['admin_password' => $validated['password']]);
            
            Log::info('Administrateur créé avec succès: ' . $validated['email']);
            
            return redirect()->route('install.complete')->with('success', 'Administrateur créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'administrateur: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Erreur lors de la création de l\'administrateur: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the completion page
     */
    public function complete()
    {
        // Vérifier si l'application est déjà installée ET s'il existe un utilisateur admin
        $installationStatus = InstallationHelper::getInstallationStatus();
        $hasAdminUser = InstallationHelper::hasAdminUser();
        
        // Ne rediriger vers login que si l'application est installée ET qu'un admin existe
        if ($installationStatus['installed'] && $hasAdminUser) {
            \Log::info("InstallController complete - Redirecting to login (installed and admin exists)");
            return redirect('/login');
        }

        // Check if admin user exists
        if (!InstallationHelper::hasAdminUser()) {
            return redirect()->route('install.admin')
                ->with('error', 'Please create an admin user first');
        }

        // Generate application key if not already generated
        if (env('APP_KEY') == '') {
            Artisan::call('key:generate');
        }

        return view('install.complete');
    }

    /**
     * Finalise l'installation et redirige vers le tableau de bord.
     *
     * @return \Illuminate\Http\Response
     */
    public function finalize()
    {
        try {
            // Vérifier si un superadmin existe
            if (!InstallationHelper::hasAdminUser()) {
                Log::error('Tentative de finalisation sans création d\'administrateur');
                return redirect()->route('install.admin')
                    ->withErrors(['error' => 'Vous devez créer un compte superadmin avant de finaliser l\'installation.']);
            }
            
            // Vérifier les données ESBTP
            $esbtpDataStatus = InstallationHelper::checkESBTPData();
            if (!$esbtpDataStatus['success']) {
                Log::warning('Des données ESBTP sont manquantes: ' . implode(', ', $esbtpDataStatus['missing_data']));
                session([
                    'esbtp_warning' => true,
                    'esbtp_missing_data' => $esbtpDataStatus['missing_data']
                ]);
            }
            
            // Marquer l'application comme installée
            if (!InstallationHelper::markAsInstalled()) {
                Log::error('Impossible de marquer l\'application comme installée');
                throw new \Exception('Impossible de marquer l\'application comme installée');
            }

            // Récupérer les informations d'identification de l'administrateur depuis la session
            $adminCredentials = [
                'username' => session('admin_username'),
                'email' => session('admin_email'),
                'password' => session('admin_password')
            ];
            
            // Si l'authentification réussit, rediriger vers le dashboard
            if (Auth::attempt([
                    filter_var($adminCredentials['username'], FILTER_VALIDATE_EMAIL) 
                        ? 'email' 
                        : 'username' => $adminCredentials['username'],
                    'password' => $adminCredentials['password']
                ])) {
                Log::info('L\'administrateur a été connecté automatiquement: ' . session('admin_email'));
                return redirect()->route('dashboard');
            } else {
                // Si l'authentification échoue, nous redirigeons vers la page de connexion
                Log::warning('Échec de la connexion automatique de l\'administrateur');
                return redirect()->route('login')
                    ->with('message', 'Installation terminée. Veuillez vous connecter avec vos identifiants administrateur.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la finalisation de l\'installation: ' . $e->getMessage());
            return redirect()->route('install.migration')
                ->withErrors(['error' => 'Erreur lors de la finalisation de l\'installation: ' . $e->getMessage()]);
        }
    }

    /**
     * Check if the database is migrated
     */
    private function isDatabaseMigrated()
    {
        try {
            return Schema::hasTable('users') && Schema::hasTable('roles');
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Test the database connection
     *
     * @param string $host Hôte de la base de données
     * @param string $port Port de la base de données
     * @param string $username Nom d'utilisateur de la base de données
     * @param string $password Mot de passe de la base de données
     * @param string $database Nom de la base de données
     * @return array Retourne un tableau associatif avec le statut de la connexion et d'autres informations
     */
    private function testDatabaseConnection($host, $port, $username, $password, $database)
    {
        // Créer le DSN pour la connexion sans spécifier de base de données
        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        
        try {
            // Effacer les sessions précédentes
            session()->forget(['db_connection_error', 'database_created']);
            
            // Essayer de se connecter au serveur MySQL sans spécifier de base de données
            $pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
            
            // Vérifier si la base de données existe
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$database}'");
            $databaseExists = $stmt->rowCount() > 0;
            
            if (!$databaseExists) {
                // La base de données n'existe pas, on mémorise qu'elle devra être créée
                \Log::info("Base de données '{$database}' introuvable. Elle sera créée lors de la migration.");
                session(['database_exists' => false]);
                
                return [
                    'status' => 'success',
                    'message' => 'Connexion au serveur réussie. La base de données sera créée lors de la migration.',
                    'database_exists' => false,
                    'tables_exist' => false
                ];
            }
            
            // Tenter de se connecter à la base de données spécifique
            $dsnWithDb = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
            $pdoWithDb = new \PDO($dsnWithDb, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]);
            
            // Vérifier si des tables existent dans la base de données
            $stmt = $pdoWithDb->query("SHOW TABLES");
            $tablesExist = $stmt->rowCount() > 0;
            
            // Mémoriser l'état de la base de données en session
            session([
                'database_exists' => true,
                'tables_exist' => $tablesExist
            ]);
            
            // La connexion est établie et la base existe
            return [
                'status' => 'success',
                'message' => 'Connexion à la base de données réussie.',
                'database_exists' => true,
                'tables_exist' => $tablesExist
            ];
            
        } catch (\Exception $e) {
            // Journaliser l'erreur de connexion
            $errorMessage = "Erreur de connexion à la base de données: " . $e->getMessage();
            \Log::error($errorMessage);
            
            // Stocker l'erreur en session pour l'afficher à l'utilisateur
            session(['db_connection_error' => $e->getMessage()]);
            
            return [
                'status' => 'error',
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Get table names from migration files by analyzing their content
     */
    private function getMigrationTableNames()
    {
        $migrationFiles = glob(database_path('migrations/*.php'));
        $tableNames = [];
        $migrationTableMap = [];
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            $tablesInFile = [];
            
            // First, try to extract table name from filename using regex
            if (preg_match('/create_(.+)_table\.php$/', $filename, $matches)) {
                $tableName = $matches[1];
                // Normaliser le nom de la table en supprimant les underscores dans le préfixe pour les tables ESBTP
                if (strpos($tableName, 'esbtp_') === 0 || strpos($tableName, 'e_s_b_t_p_') === 0) {
                    $tableName = str_replace('e_s_b_t_p_', 'esbtp_', $tableName);
                }
                
                $tablesInFile[] = $tableName;
            }
            
            // Then, analyze file content to find all Schema::create calls
            $content = file_get_contents($file);
            if (preg_match_all('/Schema::create\([\'"]([^\'"]+)[\'"]/', $content, $contentMatches)) {
                foreach ($contentMatches[1] as $tableName) {
                    // Normaliser le nom de la table en supprimant les underscores dans le préfixe pour les tables ESBTP
                    if (strpos($tableName, 'esbtp_') === 0 || strpos($tableName, 'e_s_b_t_p_') === 0) {
                        $tableName = str_replace('e_s_b_t_p_', 'esbtp_', $tableName);
                    }
                    
                    if (!in_array($tableName, $tablesInFile)) {
                        $tablesInFile[] = $tableName;
                    }
                }
            }
            
            // Also check for createTable method which might be used in some migrations
            if (preg_match_all('/->createTable\([\'"]([^\'"]+)[\'"]/', $content, $createTableMatches)) {
                foreach ($createTableMatches[1] as $tableName) {
                    // Normaliser le nom de la table en supprimant les underscores dans le préfixe pour les tables ESBTP
                    if (strpos($tableName, 'esbtp_') === 0 || strpos($tableName, 'e_s_b_t_p_') === 0) {
                        $tableName = str_replace('e_s_b_t_p_', 'esbtp_', $tableName);
                    }
                    
                    if (!in_array($tableName, $tablesInFile)) {
                        $tablesInFile[] = $tableName;
                    }
                }
            }
            
            // Add tables found in this file to our collections
            foreach ($tablesInFile as $tableName) {
                if (!in_array($tableName, $tableNames)) {
                    $tableNames[] = $tableName;
                }
                
                if (!isset($migrationTableMap[$filename])) {
                    $migrationTableMap[$filename] = [];
                }
                $migrationTableMap[$filename][] = $tableName;
            }
        }
        
        // Log for debugging
        \Log::info('Migration files found: ' . count($migrationFiles));
        \Log::info('Table names extracted: ' . count($tableNames));
        \Log::info('Table names: ' . implode(', ', $tableNames));
        
        // Also log migrations with multiple tables
        $multiTableMigrations = array_filter($migrationTableMap, function($tables) {
            return count($tables) > 1;
        });
        
        if (count($multiTableMigrations) > 0) {
            foreach ($multiTableMigrations as $file => $tables) {
                \Log::info("Migration {$file} creates multiple tables: " . implode(', ', $tables));
            }
        }
        
        return [
            'table_names' => $tableNames,
            'migration_table_map' => $migrationTableMap,
            'migration_files' => $migrationFiles,
            'multi_table_migrations' => $multiTableMigrations
        ];
    }

    /**
     * Check database connection and status
     */
    private function checkDatabaseStatus()
    {
        try {
            // Get database configuration from session
            $host = session('db_host', env('DB_HOST'));
            $port = session('db_port', env('DB_PORT'));
            $database = session('db_database', env('DB_DATABASE'));
            $username = session('db_username', env('DB_USERNAME'));
            $password = session('db_password', env('DB_PASSWORD'));
            
            // Test database connection
            $connection = $this->testDatabaseConnection($host, $port, $username, $password, $database);
            
            if ($connection['status'] === 'success') {
                // Get migration table names
                $migrationData = $this->getMigrationTableNames();
                $migrationTables = $migrationData['table_names'];
                $migrationTableMap = $migrationData['migration_table_map'];
                $migrationFiles = $migrationData['migration_files'];
                $multiTableMigrations = $migrationData['multi_table_migrations'];
                
                // Get existing tables
                $existingTables = [];
                $tables = DB::select('SHOW TABLES');
                foreach ($tables as $table) {
                    $tableName = reset($table); // Get the first value from the object
                    $existingTables[] = $tableName;
                }
                
                // Calculate match percentage
                $migrationTablesCount = count($migrationTables);
                $existingTablesCount = count($existingTables);
                
                // Find missing tables (in migrations but not in database)
                $missingTables = array_diff($migrationTables, $existingTables);
                
                // Find extra tables (in database but not in migrations)
                $extraTables = array_diff($existingTables, $migrationTables);
                
                // Calculate match percentage
                $matchingTables = array_intersect($migrationTables, $existingTables);
                $matchCount = count($matchingTables);
                $matchPercentage = ($migrationTablesCount > 0) 
                    ? round(($matchCount / $migrationTablesCount) * 100, 2) 
                    : 0;
                
                // Log detailed information for debugging
                \Log::info("Database status check:");
                \Log::info("- Migration tables: {$migrationTablesCount}");
                \Log::info("- Existing tables: {$existingTablesCount}");
                \Log::info("- Matching tables: {$matchCount}");
                \Log::info("- Match percentage: {$matchPercentage}%");
                \Log::info("- Missing tables: " . implode(', ', $missingTables));
                \Log::info("- Extra tables: " . implode(', ', $extraTables));
                
                // Determine if all required tables exist
                $allTablesExist = empty($missingTables);
                
                return [
                    'connected' => true,
                    'database_exists' => $connection['database_exists'],
                    'tables_exist' => $connection['tables_exist'] ?? false,
                    'all_tables_exist' => $allTablesExist,
                    'migration_tables_count' => $migrationTablesCount,
                    'existing_tables_count' => $existingTablesCount,
                    'matching_tables_count' => $matchCount,
                    'match_percentage' => $matchPercentage,
                    'missing_tables' => $missingTables,
                    'extra_tables' => $extraTables,
                    'migration_files_count' => count($migrationFiles),
                    'multi_table_migrations_count' => count($multiTableMigrations)
                ];
            }
            
            return [
                'connected' => false,
                'message' => $connection['message']
            ];
        } catch (Exception $e) {
            return [
                'connected' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update the environment file
     */
    private function updateEnvironmentFile($data)
    {
        $path = base_path('.env');

        if (File::exists($path)) {
            $content = File::get($path);

            foreach ($data as $key => $value) {
                // If the key exists, replace it
                if (strpos($content, $key . '=') !== false) {
                    $content = preg_replace('/' . $key . '=(.*)/', $key . '=' . $value, $content);
                } else {
                    // Otherwise, add it
                    $content .= "\n" . $key . '=' . $value;
                }
            }

            File::put($path, $content);
        } else {
            // If .env doesn't exist, create it from .env.example
            $example = base_path('.env.example');
            if (File::exists($example)) {
                $content = File::get($example);

                foreach ($data as $key => $value) {
                    $content = preg_replace('/' . $key . '=(.*)/', $key . '=' . $value, $content);
                }

                File::put($path, $content);
            }
        }
    }

    /**
     * Crée une base de données si elle n'existe pas.
     *
     * @param  string  $host
     * @param  string  $port
     * @param  string  $username
     * @param  string  $password
     * @param  string  $database
     * @return bool
     */
    private function createDatabase($host, $port, $username, $password, $database)
    {
        try {
            // Connexion sans spécifier de base de données
            $pdo = new \PDO("mysql:host={$host};port={$port}", $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Échapper le nom de la base de données
            $database = $pdo->quote($database);
            
            // Créer la base de données si elle n'existe pas
            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$database}");
            
            \Log::info("Base de données {$database} créée avec succès");
            return true;
        } catch (\Exception $e) {
            \Log::error("Erreur lors de la création de la base de données: " . $e->getMessage());
            return false;
        }
    }
} 