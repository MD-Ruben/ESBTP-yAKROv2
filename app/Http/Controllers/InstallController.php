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
        
        // Merge the database status with the installation status
        $dbStatus = array_merge($dbStatus, [
            'match_percentage' => $installationStatus['match_percentage'],
            'can_skip_migration' => $installationStatus['match_percentage'] == 100,
            'installation_status' => $installationStatus
        ]);
        
        // Log the migration status
        \Log::info("Migration page - DB Status: " . json_encode($dbStatus));
        
        return view('install.migration', [
            'dbStatus' => $dbStatus
        ]);
    }

    /**
     * Run database migrations
     */
    public function runMigration(Request $request)
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
            
            if ($connection['status'] !== 'success') {
                return response()->json([
                    'status' => 'error',
                    'message' => $connection['message']
                ]);
            }
            
            // Get migration data to track tables being created
            $migrationData = $this->getMigrationTableNames();
            $migrationTableMap = $migrationData['migration_table_map'];
            $multiTableMigrations = $migrationData['multi_table_migrations'];
            
            // Start output buffer to capture migration output
            ob_start();
            
            // Wipe the database first to avoid migration errors
            Artisan::call('db:wipe', [
                '--force' => true
            ]);
            
            // Run migrations
            Artisan::call('migrate', [
                '--force' => true,
                '--seed' => true
            ]);
            
            // Get command output
            $output = ob_get_clean();
            
            // Process output to add information about tables being created
            $lines = explode("\n", $output);
            $enhancedOutput = [];
            
            foreach ($lines as $line) {
                $enhancedOutput[] = $line;
                
                // If this is a migration line, add information about tables being created
                if (preg_match('/Migrating: (\d+)_(\d+)_(\d+)_(\d+)_(.+)/', $line, $matches)) {
                    $migrationName = $matches[5] . '.php';
                    
                    // Check if this migration creates tables
                    if (isset($migrationTableMap[$migrationName])) {
                        $tables = $migrationTableMap[$migrationName];
                        
                        if (count($tables) === 1) {
                            $enhancedOutput[] = "Creating table: " . $tables[0];
                        } else {
                            $enhancedOutput[] = "Creating multiple tables: " . implode(', ', $tables);
                        }
                    }
                }
            }
            
            // Join the enhanced output
            $enhancedOutput = implode("\n", $enhancedOutput);
            
            // Log success
            \Log::info('Migrations completed successfully');
            
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Migrations exécutées avec succès !',
                'output' => $enhancedOutput,
                'redirect' => route('install.admin')
            ]);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Migration error: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la migration : ' . $e->getMessage(),
                'output' => ob_get_clean() ?? 'No output'
            ]);
        }
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
    public function createAdmin(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Create the admin user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Assign the admin role
            $user->assignRole('admin');
            
            // Store admin information in session for the complete page
            session([
                'admin_email' => $request->email,
                'admin_name' => $request->name,
                'admin_password' => $request->password,
                'school_name' => $request->school_name,
                'school_email' => $request->school_email,
                'school_address' => $request->school_address
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Admin user created successfully',
                'redirect' => route('install.complete')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin user creation failed: ' . $e->getMessage()
            ], 422);
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
     * Finalize the installation
     */
    public function finalize()
    {
        try {
            // Mark the application as installed
            InstallationHelper::markAsInstalled();
            
            // Clear the config cache
            Artisan::call('config:clear');
            
            // Get the admin email from session
            $adminEmail = session('admin_email');
            
            // If we have the admin email, log them in automatically
            if ($adminEmail) {
                $user = \App\Models\User::where('email', $adminEmail)->first();
                if ($user) {
                    Auth::login($user);
                }
            }
            
            // Get installation status to check migration match percentage
            $installationStatus = InstallationHelper::getInstallationStatus();
            $matchPercentage = $installationStatus['match_percentage'];
            
            // Log the installation completion status
            \Log::info("Installation completed. Migration match: {$matchPercentage}%");
            
            // Run the cleanup command in the background after sending the response
            // This will remove installation files and routes
            $response = response()->json([
                'status' => 'success',
                'message' => 'Installation terminée avec succès !',
                'redirect' => route('welcome'),
                'match_percentage' => $matchPercentage
            ]);
            
            // Register a shutdown function to run the cleanup command after the response is sent
            register_shutdown_function(function () {
                try {
                    Artisan::call('install:cleanup');
                } catch (\Exception $e) {
                    \Log::error('Error running cleanup command: ' . $e->getMessage());
                }
            });
            
            return $response;
        } catch (\Exception $e) {
            \Log::error('Error in finalize: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la finalisation de l\'installation: ' . $e->getMessage()
            ], 500);
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
     * Test database connection with provided credentials
     */
    private function testDatabaseConnection($host, $port, $username, $password, $database)
    {
        try {
            // First, try to connect to MySQL server without specifying a database
            $dsn = "mysql:host={$host};port={$port}";
            $pdo = new \PDO($dsn, $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Check if database exists
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$database}'");
            $databaseExists = $stmt->fetchColumn() !== false;
            
            if (!$databaseExists) {
                // Try to create the database
                try {
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    \Log::info("Database '{$database}' created successfully");
                    $databaseExists = true;
                } catch (\Exception $e) {
                    \Log::error("Failed to create database: " . $e->getMessage());
                    return [
                        'status' => 'error',
                        'message' => "La base de données '{$database}' n'existe pas et n'a pas pu être créée automatiquement. Erreur: " . $e->getMessage(),
                        'database_exists' => false
                    ];
                }
            }
            
            // Now try to connect to the specific database
            if ($databaseExists) {
                $dsn = "mysql:host={$host};port={$port};dbname={$database}";
                $pdo = new \PDO($dsn, $username, $password);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                // Check if tables exist
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                $tablesExist = count($tables) > 0;
                
                return [
                    'status' => 'success',
                    'message' => 'Connexion à la base de données réussie',
                    'database_exists' => true,
                    'tables_exist' => $tablesExist,
                    'tables_count' => count($tables)
                ];
            }
            
            return [
                'status' => 'success',
                'message' => 'Connexion au serveur MySQL réussie, mais la base de données n\'existe pas',
                'database_exists' => false
            ];
        } catch (\Exception $e) {
            \Log::error("Database connection error: " . $e->getMessage());
            
            // Determine the type of error
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'Access denied') !== false) {
                return [
                    'status' => 'error',
                    'message' => 'Accès refusé. Vérifiez votre nom d\'utilisateur et votre mot de passe.',
                    'error_type' => 'access_denied'
                ];
            } elseif (strpos($errorMessage, 'Unknown database') !== false) {
                return [
                    'status' => 'error',
                    'message' => "La base de données '{$database}' n'existe pas.",
                    'error_type' => 'unknown_database',
                    'database_exists' => false
                ];
            } elseif (strpos($errorMessage, 'Connection refused') !== false) {
                return [
                    'status' => 'error',
                    'message' => 'Connexion refusée. Vérifiez que le serveur MySQL est en cours d\'exécution et que l\'hôte et le port sont corrects.',
                    'error_type' => 'connection_refused'
                ];
            }
            
            return [
                'status' => 'error',
                'message' => 'Erreur de connexion à la base de données: ' . $e->getMessage(),
                'error_type' => 'general_error'
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
                $tablesInFile[] = $tableName;
            }
            
            // Then, analyze file content to find all Schema::create calls
            $content = file_get_contents($file);
            if (preg_match_all('/Schema::create\([\'"]([^\'"]+)[\'"]/', $content, $contentMatches)) {
                foreach ($contentMatches[1] as $tableName) {
                    if (!in_array($tableName, $tablesInFile)) {
                        $tablesInFile[] = $tableName;
                    }
                }
            }
            
            // Also check for createTable method which might be used in some migrations
            if (preg_match_all('/->createTable\([\'"]([^\'"]+)[\'"]/', $content, $createTableMatches)) {
                foreach ($createTableMatches[1] as $tableName) {
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
} 