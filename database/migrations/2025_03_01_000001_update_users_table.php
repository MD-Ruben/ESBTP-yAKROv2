<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Exécuter les migrations.
     */
    public function up(): void
    {
        // Instead of renaming columns, we'll add new columns and copy data
        
        // First, add all the new columns
        Schema::table('users', function (Blueprint $table) {
            // Add user_type if role doesn't exist
            if (!Schema::hasColumn('users', 'role') && !Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->default('student')->after('email')->comment('Type d\'utilisateur: superadmin, secretary, teacher, student, parent');
            }
            
            // Add profile_photo if profile_image doesn't exist
            if (!Schema::hasColumn('users', 'profile_image') && !Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable();
            }
            
            // Ajouter les colonnes pour les informations personnelles si elles n'existent pas
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('state');
            }
            
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('zip_code');
            }
            
            if (!Schema::hasColumn('users', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('country');
            }
            
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('birth_date');
            }
            
            // Ajouter les colonnes pour le suivi de connexion
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('last_login_at');
            }
            
            // Ajouter les colonnes pour le suivi des modifications
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('last_login_ip')
                      ->references('id')->on('users')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('users', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->after('created_by')
                      ->references('id')->on('users')->nullOnDelete();
            }
            
            // Ajouter la suppression douce si elle n'existe pas
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
        
        // Copy data from old columns to new ones if needed
        if (Schema::hasColumn('users', 'role') && Schema::hasColumn('users', 'user_type')) {
            DB::statement('UPDATE users SET user_type = role WHERE user_type IS NULL');
        }
        
        if (Schema::hasColumn('users', 'profile_image') && Schema::hasColumn('users', 'profile_photo')) {
            DB::statement('UPDATE users SET profile_photo = profile_image WHERE profile_photo IS NULL');
        }
        
        // Drop old columns if they exist and we have the new ones
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role') && Schema::hasColumn('users', 'user_type')) {
                $table->dropColumn('role');
            }
            
            if (Schema::hasColumn('users', 'profile_image') && Schema::hasColumn('users', 'profile_photo')) {
                $table->dropColumn('profile_image');
            }
        });
    }

    /**
     * Annuler les migrations.
     */
    public function down(): void
    {
        // We can't easily restore the exact previous state, but we can add back the old columns
        Schema::table('users', function (Blueprint $table) {
            // Add back role if it doesn't exist but user_type does
            if (!Schema::hasColumn('users', 'role') && Schema::hasColumn('users', 'user_type')) {
                $table->string('role')->nullable()->after('email');
                DB::statement('UPDATE users SET role = user_type WHERE role IS NULL');
            }
            
            // Add back profile_image if it doesn't exist but profile_photo does
            if (!Schema::hasColumn('users', 'profile_image') && Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_image')->nullable();
                DB::statement('UPDATE users SET profile_image = profile_photo WHERE profile_image IS NULL');
            }
        });
    }
}; 