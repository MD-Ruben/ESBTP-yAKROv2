<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    /**
     * Affiche la page d'installation initiale
     */
    public function index()
    {
        // Vérifier si l'installation a déjà été effectuée
        if ($this->isInstalled()) {
            return redirect('/');
        }

        return view('setup.index');
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
            // Exécuter les migrations
            Artisan::call('migrate', ['--force' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Migrations exécutées avec succès',
                'output' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors des migrations: ' . $e->getMessage()
            ], 500);
        }
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
            // Vérifier si la colonne 'role' existe dans la table users
            if (!Schema::hasColumn('users', 'role')) {
                // Ajouter la colonne role si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->enum('role', ['admin', 'teacher', 'student', 'parent'])->default('student')->after('password');
                });
            }

            // Vérifier si la colonne 'is_active' existe dans la table users
            if (!Schema::hasColumn('users', 'is_active')) {
                // Ajouter la colonne is_active si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->boolean('is_active')->default(true)->after('role');
                });
            }

            // Vérifier si la colonne 'profile_image' existe dans la table users
            if (!Schema::hasColumn('users', 'profile_image')) {
                // Ajouter la colonne profile_image si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->string('profile_image')->nullable()->after('is_active');
                });
            }

            // Vérifier si la colonne 'phone' existe dans la table users
            if (!Schema::hasColumn('users', 'phone')) {
                // Ajouter la colonne phone si elle n'existe pas
                Schema::table('users', function ($table) {
                    $table->string('phone', 20)->nullable()->after('profile_image');
                });
            }

            // Créer l'utilisateur administrateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'is_active' => true,
            ]);

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
            if (Schema::hasTable('users')) {
                $adminExists = DB::table('users')->where('role', 'admin')->exists();
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
            // Exécuter les seeders
            Artisan::call('db:seed', ['--force' => true]);
            
            // Optimiser l'application
            Artisan::call('optimize:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Installation terminée avec succès',
                'redirect' => route('login')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la finalisation: ' . $e->getMessage()
            ], 500);
        }
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
            return true;
        } catch (\Exception $e) {
            return false;
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
} 