<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

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
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            Log::error('Erreur de connexion à la base de données: ' . $e->getMessage());
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
                Log::info('hasAdminUser: Database not configured');
                return false;
            }
            
            if (!Schema::hasTable('users')) {
                Log::info('hasAdminUser: Users table does not exist');
                return false;
            }
            
            if (!Schema::hasTable('roles')) {
                Log::info('hasAdminUser: Roles table does not exist');
                return false;
            }
            
            if (!Schema::hasTable('model_has_roles')) {
                Log::info('hasAdminUser: model_has_roles table does not exist');
                return false;
            }
            
            // Check if any user has the admin role using the model_has_roles table
            $hasAdmin = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'admin')
                ->exists();
                
            Log::info('hasAdminUser: Admin user exists: ' . ($hasAdmin ? 'Yes' : 'No'));
            return $hasAdmin;
        } catch (\Exception $e) {
            Log::error('Error checking for admin user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get detailed installation status including migration match percentage
     * 
     * @return array
     */
    public static function getInstallationStatus()
    {
        // Vérifier si le fichier .env existe
        $envExists = file_exists(base_path('.env'));
        Log::info('Fichier .env existe: ' . ($envExists ? 'Oui' : 'Non'));

        // Vérifier si l'application est marquée comme installée
        $appInstalledFlag = env('APP_INSTALLED', false);
        Log::info('Flag APP_INSTALLED: ' . ($appInstalledFlag ? 'Oui' : 'Non'));

        // Vérifier si la base de données est configurée
        $dbConfigured = self::isDatabaseConfigured();
        Log::info('Base de données configurée: ' . ($dbConfigured ? 'Oui' : 'Non'));

        // Initialiser les compteurs et tableaux
        $migrationFilesCount = 0;
        $existingTablesCount = 0;
        $migrationTablesCount = 0;
        $matchingTablesCount = 0;
        $matchPercentage = 0;
        $missingTables = [];
        $extraTables = [];
        $allTablesExist = false;
        $esbtpTablesExist = false;
        $adminUserExists = false;
        $moduleStatus = [];
        $allRequiredTablesExist = false;

        // Vérifier si la base de données est configurée avant de continuer
        if ($dbConfigured) {
            try {
                // Obtenir les tables existantes
                $existingTables = self::getExistingTables();
                $existingTablesCount = count($existingTables);
                Log::info('Nombre de tables existantes: ' . $existingTablesCount);
                
                // Obtenir les tables de migration
                $migrationTables = self::getMigrationTableNames();
                $migrationTablesCount = count($migrationTables);
                Log::info('Nombre de tables de migration: ' . $migrationTablesCount);
                
                // Obtenir les fichiers de migration
                $migrationFiles = self::getMigrationFiles();
                $migrationFilesCount = count($migrationFiles);
                Log::info('Nombre de fichiers de migration: ' . $migrationFilesCount);
                
                // Calculer les tables correspondantes
                $matchingTables = array_intersect($migrationTables, $existingTables);
                $matchingTablesCount = count($matchingTables);
                Log::info('Nombre de tables correspondantes: ' . $matchingTablesCount);
                
                // Calculer le pourcentage de correspondance
                $matchPercentage = $migrationTablesCount > 0 ? round(($matchingTablesCount / $migrationTablesCount) * 100) : 0;
                Log::info('Pourcentage de correspondance: ' . $matchPercentage . '%');
                
                // Déterminer les tables manquantes et supplémentaires
                $missingTables = array_diff($migrationTables, $existingTables);
                $extraTables = array_diff($existingTables, $migrationTables);
                
                // Vérifier si toutes les tables existent
                $allTablesExist = count($missingTables) === 0 && $migrationTablesCount > 0;
                Log::info('Toutes les tables existent: ' . ($allTablesExist ? 'Oui' : 'Non'));
                
                // Vérifier si un utilisateur admin existe
                $adminUserExists = self::hasAdminUser();
                Log::info('Utilisateur admin existe: ' . ($adminUserExists ? 'Oui' : 'Non'));
                
                // Vérifier si toutes les tables ESBTP existent
                $esbtpTablesExist = self::allESBTPTablesExist();
                Log::info('Tables ESBTP existent: ' . ($esbtpTablesExist ? 'Oui' : 'Non'));
                
                // Vérifier le statut des modules par catégorie
                $moduleStatus = self::checkTablesByCategory();
                $allModulesComplete = true;
                
                foreach ($moduleStatus['categories'] as $category => $info) {
                    Log::info("Module {$category}: " . ($info['complete'] ? 'Complet' : 'Incomplet') . " ({$info['percentage']}%)");
                    if (!$info['complete']) {
                        $allModulesComplete = false;
                    }
                }
                
                Log::info('Tous les modules sont complets: ' . ($allModulesComplete ? 'Oui' : 'Non'));
                
                // Vérifier toutes les tables requises
                $allRequiredTablesStatus = self::checkAllRequiredTables();
                $allRequiredTablesExist = $allRequiredTablesStatus['all_exist'];
                Log::info('Toutes les tables requises existent: ' . ($allRequiredTablesExist ? 'Oui' : 'Non'));
                
            } catch (\Exception $e) {
                Log::error('Erreur lors de la vérification du statut d\'installation: ' . $e->getMessage());
            }
        }

        // Déterminer si l'installation est complète
        $installed = $envExists && 
                    ($appInstalledFlag || 
                    ($dbConfigured && 
                        (($matchPercentage >= 90 && $adminUserExists) || 
                         ($allModulesComplete && $adminUserExists))));

        // Retourner le statut d'installation
        $status = [
            'installed' => $installed,
            'env_exists' => $envExists,
            'app_installed_flag' => $appInstalledFlag,
            'db_configured' => $dbConfigured,
            'required_tables_exist' => $allTablesExist,
            'migration_files_count' => $migrationFilesCount,
            'existing_tables_count' => $existingTablesCount,
            'migration_tables_count' => $migrationTablesCount,
            'matching_tables_count' => $matchingTablesCount,
            'match_percentage' => $matchPercentage,
            'missing_tables' => $missingTables,
            'extra_tables' => $extraTables,
            'all_tables_exist' => $allTablesExist,
            'admin_user_exists' => $adminUserExists,
            'esbtp_tables_exist' => $esbtpTablesExist,
            'modules_complete' => $allModulesComplete,
            'all_required_tables_exist' => $allRequiredTablesExist,
            'module_status' => $moduleStatus['categories']
        ];

            return $status;
        }

    /**
     * Normaliser un nom de table pour la vérification de correspondance
     * Cette fonction prend en charge les variations de noms ESBTP
     * 
     * @param string $tableName
     * @return string
     */
    public static function normalizeTableName($tableName)
    {
        // Normaliser les tables esbtp pour assurer une correspondance correcte
        if (strpos($tableName, 'esbtp_') === 0 || strpos($tableName, 'e_s_b_t_p_') === 0) {
            return str_replace('e_s_b_t_p_', 'esbtp_', $tableName);
        }
        
        return $tableName;
    }

    /**
     * Extraction améliorée des noms de tables depuis les fichiers de migration
     * 
     * @return array
     */
    public static function getExpectedTables()
    {
        $migrationFiles = glob(database_path('migrations/*.php'));
        $tableNames = [];
        
        foreach ($migrationFiles as $file) {
            $filename = basename($file);
            
            // Extraire le nom de la table à partir du nom du fichier
            if (preg_match('/create_(.+)_table\.php$/', $filename, $matches)) {
                $tableName = $matches[1];
                $tableName = self::normalizeTableName($tableName);
                $tableNames[] = $tableName;
            }
            
            // Analyser le contenu du fichier pour trouver tous les appels Schema::create
            $content = file_get_contents($file);
            if (preg_match_all('/Schema::create\([\'"]([^\'"]+)[\'"]/', $content, $contentMatches)) {
                foreach ($contentMatches[1] as $tableName) {
                    $tableName = self::normalizeTableName($tableName);
                    if (!in_array($tableName, $tableNames)) {
                        $tableNames[] = $tableName;
                    }
                }
            }
        }
        
        // Log pour le débogage
        Log::info('Tables attendues: ' . implode(', ', $tableNames));
        
        return $tableNames;
    }

    /**
     * Vérifier si toutes les tables ESBTP existent
     * 
     * @return bool
     */
    public static function allESBTPTablesExist()
    {
        try {
            if (!self::isDatabaseConfigured()) {
                Log::info("allESBTPTablesExist: La base de données n'est pas configurée");
                return false;
            }
            
            $esbtpTables = [
                'esbtp_filieres',
                'esbtp_niveau_etudes',
                'esbtp_annee_universitaires',
                'esbtp_inscriptions',
                'esbtp_etudiants',
                'esbtp_parents',
                'esbtp_etudiant_parent',
                'esbtp_paiements',
                'esbtp_classes',
                'esbtp_matieres',
                'esbtp_unites_enseignement',
                'esbtp_salles'
            ];
            
            foreach ($esbtpTables as $table) {
                $exists = Schema::hasTable($table);
                Log::info("Table {$table} existe: " . ($exists ? 'Oui' : 'Non'));
                
                if (!$exists) {
                    Log::info("Table ESBTP manquante: {$table}");
                    return false;
                }
            }
            
            Log::info("Toutes les tables ESBTP existent");
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification des tables ESBTP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si toutes les tables requises existent dans la base de données
     * Cette fonction parcourt tous les fichiers de migration pour extraire les noms de tables
     * et vérifie leur existence
     * 
     * @return array
     */
    public static function checkAllRequiredTables()
    {
        try {
            if (!self::isDatabaseConfigured()) {
                Log::info("checkAllRequiredTables: La base de données n'est pas configurée");
                return [
                    'all_exist' => false,
                    'missing_tables' => [],
                    'existing_tables' => [],
                    'total_tables' => 0,
                    'missing_count' => 0,
                    'existing_count' => 0
                ];
            }
            
            // Extraire les tables attendues de tous les fichiers de migration
            $expectedTables = self::getExpectedTables();
            
            $existingTables = [];
            $missingTables = [];
            
            // Vérifier l'existence de chaque table
            foreach ($expectedTables as $table) {
                $exists = Schema::hasTable($table);
                Log::info("Table {$table} existe: " . ($exists ? 'Oui' : 'Non'));
                
                if ($exists) {
                    $existingTables[] = $table;
                } else {
                    $missingTables[] = $table;
                }
            }
            
            $result = [
                'all_exist' => count($missingTables) === 0,
                'missing_tables' => $missingTables,
                'existing_tables' => $existingTables,
                'total_tables' => count($expectedTables),
                'missing_count' => count($missingTables),
                'existing_count' => count($existingTables)
            ];
            
            // Journalisation du résultat
            Log::info("Vérification des tables requises: " . count($existingTables) . "/" . count($expectedTables) . " tables existent");
            if (count($missingTables) > 0) {
                Log::info("Tables manquantes: " . implode(', ', $missingTables));
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification des tables requises: " . $e->getMessage());
            return [
                'all_exist' => false,
                'missing_tables' => [],
                'existing_tables' => [],
                'total_tables' => 0,
                'missing_count' => 0,
                'existing_count' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier les tables par catégorie (modules)
     * Cette fonction vérifie l'existence des tables regroupées par module
     * 
     * @return array
     */
    public static function checkTablesByCategory()
    {
        try {
            if (!self::isDatabaseConfigured()) {
                Log::info("checkTablesByCategory: La base de données n'est pas configurée");
                return [
                    'categories' => [],
                    'all_complete' => false
                ];
            }
            
            // Définir les catégories de tables (modules)
            $categories = [
                'esbtp' => [
                    'name' => 'ESBTP',
                    'description' => 'Module ESBTP (École Supérieure BTP)',
                    'tables' => [
                        'esbtp_filieres',
                        'esbtp_niveau_etudes',
                        'esbtp_annee_universitaires',
                        'esbtp_inscriptions',
                        'esbtp_etudiants',
                        'esbtp_parents',
                        'esbtp_etudiant_parent',
                        'esbtp_paiements',
                        'esbtp_classes',
                        'esbtp_matieres',
                        'esbtp_unites_enseignement',
                        'esbtp_salles'
                    ]
                ],
                'core' => [
                    'name' => 'Noyau',
                    'description' => 'Tables fondamentales du système',
                    'tables' => [
                        'users',
                        'roles',
                        'permissions',
                        'model_has_roles',
                        'model_has_permissions',
                        'role_has_permissions',
                        'migrations'
                    ]
                ],
                'school' => [
                    'name' => 'École',
                    'description' => 'Gestion académique',
                    'tables' => [
                        'departments',
                        'students',
                        'teachers',
                        'courses',
                        'attendances',
                        'school_classes',
                        'class_courses'
                    ]
                ],
                'admin' => [
                    'name' => 'Administration',
                    'description' => 'Gestion administrative',
                    'tables' => [
                        'notifications',
                        'personal_access_tokens',
                        'sessions'
                    ]
                ]
            ];
            
            $results = [];
            $allComplete = true;
            
            // Vérifier chaque catégorie
            foreach ($categories as $key => $category) {
                $missing = [];
                $existing = [];
                
                foreach ($category['tables'] as $table) {
                    $exists = Schema::hasTable($table);
                    
                    if ($exists) {
                        $existing[] = $table;
                    } else {
                        $missing[] = $table;
                        $allComplete = false;
                    }
                }
                
                $results[$key] = [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'total' => count($category['tables']),
                    'existing' => $existing,
                    'missing' => $missing,
                    'complete' => count($missing) === 0,
                    'percentage' => count($category['tables']) > 0 
                        ? round((count($existing) / count($category['tables'])) * 100) 
                        : 0
                ];
                
                Log::info("Catégorie {$category['name']}: {$results[$key]['percentage']}% complet");
            }
            
            return [
                'categories' => $results,
                'all_complete' => $allComplete
            ];
        } catch (\Exception $e) {
            Log::error("Erreur lors de la vérification des tables par catégorie: " . $e->getMessage());
            return [
                'categories' => [],
                'all_complete' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Récupère la liste des tables existantes dans la base de données
     *
     * @return array
     */
    public static function getExistingTables()
    {
        try {
            if (!self::isDatabaseConfigured()) {
                Log::warning('Impossible de récupérer les tables existantes: la base de données n\'est pas configurée');
                return [];
            }

            $tables = [];
            $results = DB::select('SHOW TABLES');
            
            if ($results) {
                foreach ($results as $result) {
                    $tables[] = reset($result); // Récupère la première valeur de l'objet
                }
            }
            
            Log::info('Tables existantes récupérées: ' . count($tables));
            return $tables;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des tables existantes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les noms des tables à partir des fichiers de migration
     *
     * @return array
     */
    public static function getMigrationTableNames()
    {
        try {
            $migrationFiles = self::getMigrationFiles();
            $tableNames = [];
            
            foreach ($migrationFiles as $file) {
                $content = file_get_contents($file);
                
                // Recherche des motifs de création de table
                if (preg_match('/Schema::create\([\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                    $tableName = $matches[1];
                    
                    // Normalisation des noms de tables ESBTP (suppression des underscores)
                    if (strpos($tableName, 'esbtp_') === 0) {
                        $tableNames[] = $tableName;
                    } else {
                        $tableNames[] = $tableName;
                    }
                }
            }
            
            Log::info('Noms de tables de migration récupérés: ' . count($tableNames));
            return $tableNames;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des noms de tables de migration: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère les fichiers de migration
     *
     * @return array
     */
    public static function getMigrationFiles()
    {
        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*.php');
        
        Log::info('Fichiers de migration récupérés: ' . count($files));
        return $files;
    }

    /**
     * Vérifie que les données de base ESBTP sont présentes dans la base de données
     * 
     * @return array Tableau contenant le statut et les détails des données manquantes
     */
    public static function checkESBTPData()
    {
        try {
            // Résultat par défaut
            $result = [
                'success' => true,
                'filieres' => true,
                'niveaux' => true,
                'annees' => true,
                'etudiants' => true,
                'parents' => true,
                'paiements' => true,
                'missing_data' => []
            ];

            // Vérifier les filières
            $filieresCount = DB::table('esbtp_filieres')->count();
            if ($filieresCount < 6) { // 2 filières principales + 4 options pour Génie Civil
                $result['success'] = false;
                $result['filieres'] = false;
                $result['missing_data'][] = 'Filières ESBTP';
            }

            // Vérifier les niveaux d'études
            $niveauxCount = DB::table('esbtp_niveau_etudes')->count();
            if ($niveauxCount < 2) { // 2 niveaux (BTS1, BTS2)
                $result['success'] = false;
                $result['niveaux'] = false;
                $result['missing_data'][] = 'Niveaux d\'études ESBTP';
            }

            // Vérifier les années universitaires
            $anneesCount = DB::table('esbtp_annee_universitaires')->count();
            if ($anneesCount < 1) { // Au moins une année universitaire
                $result['success'] = false;
                $result['annees'] = false;
                $result['missing_data'][] = 'Années universitaires ESBTP';
            }
            
            // Vérifier la table étudiants
            if (Schema::hasTable('esbtp_etudiants')) {
                \Log::info('Table esbtp_etudiants existe');
            } else {
                $result['success'] = false;
                $result['etudiants'] = false;
                $result['missing_data'][] = 'Table étudiants ESBTP';
                \Log::warning('Table esbtp_etudiants n\'existe pas');
            }
            
            // Vérifier la table parents
            if (Schema::hasTable('esbtp_parents')) {
                \Log::info('Table esbtp_parents existe');
            } else {
                $result['success'] = false;
                $result['parents'] = false;
                $result['missing_data'][] = 'Table parents ESBTP';
                \Log::warning('Table esbtp_parents n\'existe pas');
            }
            
            // Vérifier la table paiements
            if (Schema::hasTable('esbtp_paiements')) {
                \Log::info('Table esbtp_paiements existe');
            } else {
                $result['success'] = false;
                $result['paiements'] = false;
                $result['missing_data'][] = 'Table paiements ESBTP';
                \Log::warning('Table esbtp_paiements n\'existe pas');
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la vérification des données ESBTP: ' . $e->getMessage());
            return [
                'success' => false,
                'filieres' => false,
                'niveaux' => false,
                'annees' => false,
                'etudiants' => false,
                'parents' => false,
                'paiements' => false,
                'missing_data' => ['Données ESBTP (erreur de connexion)'],
                'error' => $e->getMessage()
            ];
        }
    }
} 