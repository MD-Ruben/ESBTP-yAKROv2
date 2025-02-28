<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallationHelper
{
    /**
     * Check if the application is installed
     * 
     * This function checks multiple conditions to determine if the application
     * has been properly installed:
     * 1. The .env file exists
     * 2. The APP_INSTALLED flag is set to true in .env
     * 3. Database connection is working
     * 4. Required tables exist in the database
     * 5. Migration tables match existing tables
     * 
     * @return bool
     */
    public static function isInstalled(): bool
    {
        $status = self::getInstallationStatus();
        return $status['installed'];
    }

    /**
     * Mark the application as installed in the .env file
     * 
     * This function updates the .env file to set APP_INSTALLED=true
     * 
     * @return bool
     */
    public static function markAsInstalled(): bool
    {
        try {
            $envPath = base_path('.env');
            
            if (File::exists($envPath)) {
                $envContent = File::get($envPath);
                
                // Check if APP_INSTALLED already exists in .env
                if (strpos($envContent, 'APP_INSTALLED') !== false) {
                    // Replace existing APP_INSTALLED value
                    $envContent = preg_replace(
                        '/APP_INSTALLED=(true|false|null)/',
                        'APP_INSTALLED=true',
                        $envContent
                    );
                } else {
                    // Add APP_INSTALLED=true to .env
                    $envContent .= "\nAPP_INSTALLED=true\n";
                }
                
                // Write updated content back to .env file
                File::put($envPath, $envContent);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Check if the database is properly configured
     * 
     * @return bool
     */
    public static function isDatabaseConfigured(): bool
    {
        try {
            // Debug information
            \Log::info('Checking if database is configured');
            
            // Check if we have database configuration in session
            if (session('db_configured') === true) {
                \Log::info('Database configuration found in session');
                return true;
            }
            
            // Check if we can connect to the database
            $connection = DB::connection()->getPdo();
            \Log::info('Database connection successful');
            return true;
        } catch (\Exception $e) {
            \Log::error('Database connection failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the number of tables in the database
     * 
     * @return int
     */
    public static function getTableCount(): int
    {
        try {
            if (!self::isDatabaseConfigured()) {
                return 0;
            }
            
            $tables = DB::select('SHOW TABLES');
            return count($tables);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Check if an admin user exists
     * 
     * @return bool
     */
    public static function hasAdminUser(): bool
    {
        try {
            if (!self::isDatabaseConfigured()) {
                \Log::info('hasAdminUser: Database not configured');
                return false;
            }
            
            if (!Schema::hasTable('users')) {
                \Log::info('hasAdminUser: Users table does not exist');
                return false;
            }
            
            if (!Schema::hasTable('roles')) {
                \Log::info('hasAdminUser: Roles table does not exist');
                return false;
            }
            
            if (!Schema::hasTable('model_has_roles')) {
                \Log::info('hasAdminUser: model_has_roles table does not exist');
                return false;
            }
            
            // Check if any user has the admin role using the model_has_roles table
            $hasAdmin = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'admin')
                ->exists();
                
            \Log::info('hasAdminUser: Admin user exists: ' . ($hasAdmin ? 'Yes' : 'No'));
            return $hasAdmin;
        } catch (\Exception $e) {
            \Log::error('Error checking for admin user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get detailed installation status including migration match percentage
     * 
     * @return array
     */
    public static function getInstallationStatus(): array
    {
        $status = [
            'installed' => false,
            'env_exists' => false,
            'app_installed_flag' => false,
            'db_configured' => false,
            'required_tables_exist' => false,
            'migration_files_count' => 0,
            'existing_tables_count' => 0,
            'migration_tables_count' => 0,
            'matching_tables_count' => 0,
            'match_percentage' => 0,
            'missing_tables' => [],
            'extra_tables' => []
        ];
        
        // Check if .env file exists
        if (File::exists(base_path('.env'))) {
            $status['env_exists'] = true;
        } else {
            \Log::info('Installation status: .env file does not exist');
            return $status;
        }

        // Check if APP_INSTALLED is set to true in .env
        if (env('APP_INSTALLED') === true) {
            $status['app_installed_flag'] = true;
        } else {
            \Log::info('Installation status: APP_INSTALLED is not true');
            return $status;
        }

        // Check database connection
        try {
            DB::connection()->getPdo();
            $status['db_configured'] = true;
        } catch (\Exception $e) {
            \Log::info('Installation status: Database connection failed - ' . $e->getMessage());
            return $status;
        }

        // Basic required tables that must exist
        $requiredTables = ['users', 'migrations', 'roles', 'permissions'];
        $allRequiredTablesExist = true;
        
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $allRequiredTablesExist = false;
                $status['missing_tables'][] = $table;
                \Log::info('Installation status: Required table missing - ' . $table);
            }
        }
        
        $status['required_tables_exist'] = $allRequiredTablesExist;
        
        // Check if migration tables match existing tables
        try {
            // Get all migration files
            $migrationFiles = glob(database_path('migrations/*.php'));
            $status['migration_files_count'] = count($migrationFiles);
            
            $migrationTableNames = [];
            
            // Extract table names from migration files by analyzing their content
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
                
                // Add tables found in this file to our collection
                foreach ($tablesInFile as $tableName) {
                    if (!in_array($tableName, $migrationTableNames)) {
                        $migrationTableNames[] = $tableName;
                    }
                }
            }
            
            $status['migration_tables_count'] = count($migrationTableNames);
            
            // Get all existing tables
            $existingTables = [];
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                // Utiliser array_values pour convertir l'objet en tableau et accéder au premier élément
                $tableName = array_values((array)$table)[0];
                $existingTables[] = $tableName;
            }
            
            $status['existing_tables_count'] = count($existingTables);
            
            // Find matching tables
            $matchingTables = array_intersect($migrationTableNames, $existingTables);
            $status['matching_tables_count'] = count($matchingTables);
            
            // Calculate match percentage
            $status['match_percentage'] = ($status['migration_tables_count'] > 0) 
                ? round(($status['matching_tables_count'] / $status['migration_tables_count']) * 100) 
                : 0;
            
            // Missing tables (in migrations but not in database)
            $status['missing_tables'] = array_diff($migrationTableNames, $existingTables);
            
            // Extra tables (in database but not in migrations)
            $status['extra_tables'] = array_diff($existingTables, $migrationTableNames);
            
            // Log detailed information for debugging
            \Log::info("Installation status: Migration files: {$status['migration_files_count']}");
            \Log::info("Installation status: Migration tables: {$status['migration_tables_count']}, Existing tables: {$status['existing_tables_count']}");
            \Log::info("Installation status: Matching tables: {$status['matching_tables_count']}/{$status['migration_tables_count']} ({$status['match_percentage']}%)");
            
            if (count($status['missing_tables']) > 0) {
                \Log::info("Installation status: Missing tables: " . implode(', ', $status['missing_tables']));
            }
            
            // Vérifier s'il existe un admin
            $hasAdminUser = self::hasAdminUser();
            \Log::info("Installation status: Admin user exists: " . ($hasAdminUser ? 'Yes' : 'No'));
            
            // Application is considered installed if:
            // 1. .env file exists
            // 2. APP_INSTALLED flag is true
            // 3. Database connection works
            // 4. Required tables exist OR match percentage is 100%
            // 5. At least 70% of migration tables exist
            // 6. Has at least one admin user
            $status['installed'] = $status['env_exists'] && 
                                  $status['app_installed_flag'] && 
                                  $status['db_configured'] && 
                                  $status['required_tables_exist'] && 
                                  $status['match_percentage'] >= 70 &&
                                  $hasAdminUser;
            
        } catch (\Exception $e) {
            \Log::error('Installation status: Error comparing migration tables - ' . $e->getMessage());
            // Continue with basic checks if this advanced check fails
            // Mais vérifier quand même s'il existe un admin
            $hasAdminUser = self::hasAdminUser();
            \Log::info("Installation status (fallback): Admin user exists: " . ($hasAdminUser ? 'Yes' : 'No'));
            
            $status['installed'] = $status['env_exists'] && 
                                  $status['app_installed_flag'] && 
                                  $status['db_configured'] && 
                                  $status['required_tables_exist'] &&
                                  $hasAdminUser;
        }

        return $status;
    }
} 