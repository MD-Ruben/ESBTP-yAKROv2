<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateSuperAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Vérifier si l'utilisateur admin existe déjà
        $adminExists = DB::table('users')->where('email', 'admin@esbtp.ci')->exists();
        
        if (!$adminExists) {
            // Créer l'utilisateur super admin
            $userId = DB::table('users')->insertGetId([
                'name' => 'Super Admin',
                'email' => 'admin@esbtp.ci',
                'password' => Hash::make('admin123'),  // Mot de passe temporaire à changer lors de la première connexion
                'role' => 'superAdmin',
                'username' => 'admin',  // Ajout du champ username
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Si la table des rôles existe, attribuer le rôle superAdmin
            if (Schema::hasTable('roles')) {
                $superAdminRole = DB::table('roles')->where('name', 'superAdmin')->first();
                
                if ($superAdminRole) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $superAdminRole->id,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ]);
                    
                    \Log::info('Rôle superAdmin attribué à l\'utilisateur admin@esbtp.ci');
                }
            }
            
            \Log::info('Utilisateur Super Admin créé avec succès.');
        } else {
            \Log::info('L\'utilisateur Super Admin existe déjà.');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Ne pas supprimer l'utilisateur superadmin lors d'un rollback
        // car cela pourrait causer des problèmes d'accès à l'application
    }
} 