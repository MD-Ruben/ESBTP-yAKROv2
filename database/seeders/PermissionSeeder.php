<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Créer les permissions
        $permissions = [
            'view_students',
            'create_students',
            'edit_students',
            'delete_students'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assigner toutes les permissions au superAdmin
        $superAdmin = Role::findByName('superAdmin');
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
        }

        // Vider le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
