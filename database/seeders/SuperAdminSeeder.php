<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer le rôle de superadmin s'il n'existe pas
        $role = Role::firstOrCreate(['name' => 'superadmin']);
        
        // Créer toutes les permissions nécessaires
        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-permissions',
            'manage-departments',
            'manage-teachers',
            'manage-students',
            'manage-courses',
            'manage-esbtp',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Attribuer toutes les permissions au rôle superadmin
        $role->syncPermissions(Permission::all());
        
        // Créer l'utilisateur superadmin
        $user = User::firstOrCreate(
            ['email' => 'admin@esbtp.ci'],
            [
                'name' => 'Super Admin',
                'username' => 'admin_esbtp',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        
        // Attribuer le rôle superadmin à l'utilisateur
        $user->assignRole('superadmin');
        
        $this->command->info('Superadmin user created successfully!');
    }
} 