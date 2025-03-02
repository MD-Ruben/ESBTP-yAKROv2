<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AddPermissionToSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esbtp:add-permission-superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ajoute la permission view own profile au rôle superAdmin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Ajout de la permission "view own profile" au rôle superAdmin...');
        
        // Réinitialiser les caches des rôles et permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Obtenir le rôle superAdmin
        $role = Role::where('name', 'superAdmin')->first();
        
        if (!$role) {
            $this->error('Le rôle superAdmin n\'existe pas!');
            return 1;
        }
        
        // Vérifier si la permission existe
        $permission = Permission::where('name', 'view own profile')->first();
        
        if (!$permission) {
            $this->info('La permission "view own profile" n\'existe pas. Création...');
            $permission = Permission::create(['name' => 'view own profile']);
        }
        
        // Ajouter la permission au rôle s'il ne l'a pas déjà
        if ($role->hasPermissionTo('view own profile')) {
            $this->info('Le rôle superAdmin a déjà la permission "view own profile".');
        } else {
            $role->givePermissionTo('view own profile');
            $this->info('Permission "view own profile" ajoutée avec succès au rôle superAdmin!');
        }
        
        return 0;
    }
}
