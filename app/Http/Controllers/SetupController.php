<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Symfony\Component\Process\Process;
use Exception;

class SetupController extends Controller
{
    /**
     * Affiche la page d'installation initiale
     */
    public function index()
    {
        // Vérifier si le fichier .env existe
        $envExists = file_exists(base_path('.env'));
        
        // Vérifier si la connexion à la base de données fonctionne
        $dbStatus = $this->checkDatabaseConnection();
        $dbConnected = $dbStatus['connected'] ?? false;
        
        // Vérifier si des utilisateurs existent dans la base de données
        $usersExist = false;
        if ($dbConnected && Schema::hasTable('users')) {
            try {
                $usersExist = DB::table('users')->count() > 0;
            } catch (\Exception $e) {
                $usersExist = false;
            }
        }
        
        // Si tout est configuré, rediriger vers login
        if ($envExists && $dbConnected && $usersExist) {
            return redirect()->route('login');
        }
        
        return view('setup.index', [
            'dbStatus' => $dbStatus,
            'envExists' => $envExists,
            'dbConnected' => $dbConnected,
            'usersExist' => $usersExist
        ]);
    }

    /**
     * Exécute les migrations de base de données
     */
    public function migrate(Request $request)
    {
        // Vérifier si l'installation a déjà été effectuée
        if ($this->isInstalled()) {
            return redirect('/');
        }

        try {
            // Vider le cache des configurations
            Artisan::call('config:clear');
            
            // Vérifier si les packages nécessaires sont installés
            if (!class_exists('Spatie\Permission\PermissionServiceProvider')) {
                throw new \Exception("Le package Spatie/Permission n'est pas installé correctement. Veuillez exécuter 'composer require spatie/laravel-permission'.");
            }
            
            // Publier les fichiers de configuration de Spatie d'abord
            Artisan::call('vendor:publish', [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider',
                '--force' => true
            ]);
            
            // Exécuter les migrations dans un ordre spécifique
            $output = [];
            $output[] = "Démarrage des migrations...\n";
            
            // Liste des migrations dans l'ordre de dépendance
            $migrations = [
                '2025_02_27_200001_create_users_table.php',
                '2025_02_27_200003_create_permission_tables.php',
                '2025_02_27_200004_create_departments_table.php',
                '2025_02_27_200005_create_laboratories_table.php',
                '2025_02_27_200006_create_ufrs_table.php',
                '2025_02_27_200007_create_formations_table.php',
                '2025_02_27_200008_create_parcours_table.php',
                '2025_02_27_200009_create_students_table.php',
                '2025_02_27_200010_create_teachers_table.php',
                '2025_02_27_200014_create_grades_table.php',
                '2025_02_27_200012_create_unite_enseignements_table.php',
                '2025_02_27_200013_create_element_constitutifs_table.php',
                '2025_02_27_200013_create_evaluations_table.php',
                '2025_02_27_215802_create_courses_table.php',
                '2025_02_27_220141_create_school_classes_table.php',
                '2025_02_27_220239_create_class_courses_table.php',
                '2025_02_27_214309_create_certificates_table.php',
                '2025_02_27_214355_create_certificate_types_table.php',
                '2025_02_27_215343_create_notifications_table.php',
                '2025_02_27_215606_create_messages_table.php',
                '2025_02_27_215803_create_attendances_table.php'
            ];
            
            // Supprimer toutes les tables existantes
            Schema::disableForeignKeyConstraints();
            
            // Faire un rollback de toutes les migrations existantes
            $output[] = "Annulation des migrations existantes...";
            Artisan::call('migrate:reset', ['--force' => true]);
            $output[] = Artisan::output();
            
            // Exécuter une migration fraîche pour les tables de base
            $output[] = "Installation des tables fondamentales...";
            Artisan::call('migrate', [
                '--path' => "database/migrations/2025_02_27_200001_create_users_table.php",
                '--force' => true
            ]);
            $output[] = Artisan::output();
            
            Artisan::call('migrate', [
                '--path' => "database/migrations/2025_02_27_200003_create_permission_tables.php",
                '--force' => true
            ]);
            $output[] = Artisan::output();
            
            // Exécuter les migrations restantes une par une
            foreach ($migrations as $migration) {
                // Sauter les deux premières migrations déjà exécutées
                if ($migration == '2025_02_27_200001_create_users_table.php' || 
                    $migration == '2025_02_27_200003_create_permission_tables.php') {
                    continue;
                }
                
                try {
                    $migrationName = str_replace('.php', '', $migration);
                    $output[] = "Migration: {$migrationName}";
                    
                    // Désactiver les contraintes de clé étrangère avant chaque migration
                    Schema::disableForeignKeyConstraints();
                    
                    Artisan::call('migrate', [
                        '--path' => "database/migrations/{$migration}",
                        '--force' => true
                    ]);
                    
                    // Réactiver les contraintes après la migration
                    Schema::enableForeignKeyConstraints();
                    
                    $migrationOutput = Artisan::output();
                    $output[] = $migrationOutput;
                    
                    // Vérifier si la table a été créée
                    $tableName = $this->getTableNameFromMigration($migration);
                    if ($tableName && !Schema::hasTable($tableName)) {
                        $output[] = "Avertissement: La table {$tableName} n'a pas été créée après la migration";
                    }
                } catch (\Exception $e) {
                    $output[] = "Erreur: " . $e->getMessage();
                    
                    // Continuer malgré les erreurs, mais enregistrer l'erreur
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        // Si l'erreur est que la table existe déjà, on continue
                        $output[] = "Table déjà existante, poursuite de l'installation...";
                        continue;
                    } else {
                        $output[] = "Échec de la migration {$migration}, poursuite avec la suivante...";
                    }
                }
            }
            
            // Exécuter les seeders malgré les erreurs
            $output[] = "\nExécution des seeders...";
            try {
                Artisan::call('db:seed', ['--force' => true]);
                $output[] = Artisan::output();
            } catch (\Exception $e) {
                $output[] = "Erreur lors des seeders: " . $e->getMessage();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Migrations exécutées avec succès',
                'output' => implode("\n", $output)
            ]);
        } catch (\Exception $e) {
            // Récupérer plus de détails sur l'erreur
            $error = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
            
            // Vérifier l'état de la base de données
            try {
                $existingTables = Schema::getAllTables();
                $error['existing_tables'] = array_map(function($table) {
                    return array_values((array) $table)[0];
                }, $existingTables);
                
                // Vérifier les contraintes de clé étrangère
                $foreignKeys = DB::select("
                    SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");
                $error['foreign_keys'] = $foreignKeys;
                
            } catch (\Exception $e2) {
                $error['db_status'] = 'Impossible de lister les tables: ' . $e2->getMessage();
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors des migrations',
                'error' => $error,
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extraire le nom de la table à partir du nom du fichier de migration
     */
    private function getTableNameFromMigration($migration)
    {
        // Extraire le nom de la table à partir du nom du fichier de migration
        preg_match('/create_(.+)_table\.php$/', $migration, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Crée un utilisateur administrateur
     */
    public function createAdmin(Request $request)
    {
        // Vérifier si l'installation a déjà été effectuée
        if ($this->isInstalled()) {
            return redirect('/');
        }

        // Valider les données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Vérifier si la colonne 'is_active' existe dans la table users
            if (!Schema::hasColumn('users', 'is_active')) {
                // Ajouter la colonne is_active si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->boolean('is_active')->default(true);
                });
            }

            // Vérifier si la colonne 'profile_image' existe dans la table users
            if (!Schema::hasColumn('users', 'profile_image')) {
                // Ajouter la colonne profile_image si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->string('profile_image')->nullable();
                });
            }

            // Vérifier si la colonne 'phone' existe dans la table users
            if (!Schema::hasColumn('users', 'phone')) {
                // Ajouter la colonne phone si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->string('phone', 20)->nullable();
                });
            }

            // Créer l'utilisateur administrateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => true,
                'email_verified_at' => now()
            ]);

            // Créer le rôle super-admin s'il n'existe pas
            $role = Role::firstOrCreate(['name' => 'super-admin']);
            
            // Assigner le rôle à l'utilisateur
            $user->assignRole($role);

            // Marquer l'application comme installée
            $this->markAsInstalled();

            return response()->json([
                'success' => true,
                'message' => 'Administrateur créé avec succès',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'administrateur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie si l'application est déjà installée
     */
    private function isInstalled()
    {
        // Vérifier si le fichier d'installation existe
        if (file_exists(storage_path('app/installed'))) {
            return true;
        }

        // Vérifier si un administrateur existe déjà
        try {
            if (Schema::hasTable('users') && Schema::hasTable('roles') && Schema::hasTable('model_has_roles')) {
                $adminExists = DB::table('model_has_roles')
                    ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                    ->where('roles.name', 'super-admin')
                    ->exists();
                    
                if ($adminExists) {
                    // Créer le fichier d'installation si un admin existe mais pas le fichier
                    file_put_contents(storage_path('app/installed'), date('Y-m-d H:i:s'));
                    return true;
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, considérer que l'application n'est pas installée
            return false;
        }

        return false;
    }

    /**
     * Finalise l'installation
     */
    public function finalize()
    {
        // Vérifier si l'installation a déjà été effectuée
        if ($this->isInstalled()) {
            return redirect('/');
        }

        try {
            // Vérifier si la connexion à la base de données fonctionne
            $dbStatus = $this->checkDatabaseConnection();
            if (!$dbStatus['connected']) {
                throw new \Exception("Impossible de se connecter à la base de données. Veuillez vérifier vos paramètres de connexion.");
            }
            
            // Vérifier si les tables nécessaires existent, sinon exécuter les migrations
            if (!Schema::hasTable('users')) {
                Artisan::call('migrate', ['--force' => true]);
            }
            
            // Exécuter les seeders
            Artisan::call('db:seed', ['--force' => true]);
            
            // Installer les dépendances via Composer si nécessaire
            if (!file_exists(base_path('vendor/autoload.php')) || request()->has('run_composer')) {
                // Utiliser Process pour exécuter composer install
                $process = new Process(['composer', 'install', '--no-interaction', '--no-dev', '--prefer-dist']);
                $process->setWorkingDirectory(base_path());
                $process->setTimeout(300); // 5 minutes
                $process->run();
                
                if (!$process->isSuccessful()) {
                    throw new \Exception('Erreur lors de l\'installation des dépendances: ' . $process->getErrorOutput());
                }
            }
            
            // Vérifier si une clé d'application existe, sinon en générer une
            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }
            
            // Optimiser l'application
            Artisan::call('optimize:clear');
            
            // Marquer l'application comme installée
            $this->markAsInstalled();
            
            return response()->json([
                'success' => true,
                'message' => 'Installation terminée avec succès',
                'redirect' => route('login')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la finalisation: ' . $e->getMessage(),
                'details' => $this->getDetailedErrorInfo($e)
            ], 500);
        }
    }

    /**
     * Obtient des informations détaillées sur l'erreur
     */
    private function getDetailedErrorInfo(\Exception $e)
    {
        $details = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        
        // Vérifier si c'est une erreur de base de données
        if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
            try {
                $dbStatus = $this->checkDatabaseConnection();
                $details['database_status'] = $dbStatus;
                
                // Vérifier si la base de données existe
                if ($dbStatus['connected']) {
                    $details['tables'] = Schema::getAllTables();
                }
            } catch (\Exception $dbException) {
                $details['database_error'] = $dbException->getMessage();
            }
        }
        
        return $details;
    }

    /**
     * Vérifie les prérequis système et renvoie les résultats
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkRequirements()
    {
        $requirements = [
            'php_version' => [
                'status' => version_compare(PHP_VERSION, '8.0.0', '>='),
                'message' => 'PHP version ' . PHP_VERSION . ' (requis: 8.0+)'
            ],
            'database' => [
                'status' => $this->checkDatabaseConnection(),
                'message' => $this->checkDatabaseConnection() ? 'Connexion à la base de données réussie' : 'Impossible de se connecter à la base de données'
            ],
            'writable_dirs' => [
                'status' => $this->checkWritableDirectories(),
                'message' => $this->checkWritableDirectories() ? 'Dossiers accessibles en écriture' : 'Certains dossiers ne sont pas accessibles en écriture'
            ],
            'extensions' => [
                'status' => $this->checkPhpExtensions(),
                'message' => $this->checkPhpExtensions() ? 'Extensions PHP requises installées' : 'Certaines extensions PHP requises sont manquantes'
            ]
        ];

        return response()->json($requirements);
    }

    /**
     * Vérifie la connexion à la base de données
     * 
     * @return bool
     */
    private function checkDatabaseConnection()
    {
        try {
            DB::connection()->getPdo();
            
            return [
                'connected' => true,
                'name' => DB::connection()->getDatabaseName(),
                'tables_count' => count(Schema::getAllTables())
            ];
        } catch (Exception $e) {
            return [
                'connected' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifie si les dossiers nécessaires sont accessibles en écriture
     * 
     * @return bool
     */
    private function checkWritableDirectories()
    {
        $directories = [
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('bootstrap/cache')
        ];

        foreach ($directories as $directory) {
            if (!is_writable($directory)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Vérifie si les extensions PHP requises sont installées
     * 
     * @return bool
     */
    private function checkPhpExtensions()
    {
        $required = ['pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
        
        foreach ($required as $extension) {
            if (!extension_loaded($extension)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Marque l'application comme installée
     */
    private function markAsInstalled()
    {
        // Créer un fichier pour indiquer que l'installation est terminée
        file_put_contents(storage_path('app/installed'), date('Y-m-d H:i:s'));
    }

    /**
     * Configure la connexion à la base de données
     */
    public function setup(Request $request)
    {
        $request->validate([
            'db_connection' => 'required|in:mysql,pgsql,sqlite',
            'db_host' => 'required_unless:db_connection,sqlite',
            'db_port' => 'required_unless:db_connection,sqlite',
            'db_database' => 'required',
            'db_username' => 'required_unless:db_connection,sqlite',
            'db_password' => 'nullable',
        ]);
        
        try {
            // Sauvegarder les anciennes configurations au cas où
            $oldConfig = [
                'DB_CONNECTION' => env('DB_CONNECTION'),
                'DB_HOST' => env('DB_HOST'),
                'DB_PORT' => env('DB_PORT'),
                'DB_DATABASE' => env('DB_DATABASE'),
                'DB_USERNAME' => env('DB_USERNAME'),
                'DB_PASSWORD' => env('DB_PASSWORD'),
            ];

            // Mettre à jour le fichier .env
            $this->updateEnvironmentFile($request);
            
            // Vider le cache de configuration
            \Artisan::call('config:clear');
            
            // Tester la connexion
            try {
                \DB::connection()->getPdo();
                
                // Si la connexion réussit, créer la base de données si nécessaire
                if ($request->db_connection === 'mysql') {
                    $this->createDatabaseIfNotExists(
                        $request->db_host,
                        $request->db_port,
                        $request->db_username,
                        $request->db_password,
                        $request->db_database
                    );
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion à la base de données établie avec succès'
                ]);
                
            } catch (\Exception $e) {
                // En cas d'échec, restaurer les anciennes configurations
                $this->restoreEnvironmentFile($oldConfig);
                \Artisan::call('config:clear');
                
                throw $e;
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion à la base de données',
                'error' => $e->getMessage(),
                'details' => $this->getDetailedErrorInfo($e)
            ], 500);
        }
    }

    /**
     * Restaure les anciennes configurations dans le fichier .env
     */
    private function restoreEnvironmentFile($oldConfig)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $env = file_get_contents($path);
            
            foreach ($oldConfig as $key => $value) {
                $env = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}=" . (strpos($value, ' ') !== false ? '"'.$value.'"' : $value),
                    $env
                );
            }
            
            file_put_contents($path, $env);
        }
    }

    /**
     * Met à jour le fichier .env avec les nouvelles configurations
     */
    private function updateEnvironmentFile(Request $request)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $env = file_get_contents($path);
            
            $env = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=' . $request->db_connection, $env);
            
            if ($request->db_connection !== 'sqlite') {
                $env = preg_replace('/DB_HOST=.*/', 'DB_HOST=' . $request->db_host, $env);
                $env = preg_replace('/DB_PORT=.*/', 'DB_PORT=' . $request->db_port, $env);
                $env = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . $request->db_username, $env);
                $env = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . $request->db_password, $env);
            }
            
            $env = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . $request->db_database, $env);
            
            file_put_contents($path, $env);
        }
    }
    
    /**
     * Crée la base de données si elle n'existe pas
     */
    private function createDatabaseIfNotExists($host, $port, $username, $password, $database)
    {
        try {
            // Connexion à MySQL sans spécifier de base de données
            $pdo = new \PDO(
                "mysql:host={$host};port={$port};charset=utf8",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            
            // Vérifier si la base de données existe
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$database}'");
            $dbExists = $stmt->fetchColumn();
            
            // Créer la base de données si elle n'existe pas
            if (!$dbExists) {
                $pdo->exec("CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        } catch (\Exception $e) {
            throw new \Exception("Impossible de créer la base de données: " . $e->getMessage());
        }
    }
} 