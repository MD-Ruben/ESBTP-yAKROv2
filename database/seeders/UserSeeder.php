<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with default users.
     *
     * @return void
     */
    public function run()
    {
        // Créer un utilisateur administrateur
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@smartschool.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Créer un utilisateur enseignant
        User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@smartschool.com',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Créer un utilisateur étudiant
        User::create([
            'name' => 'Student User',
            'email' => 'student@smartschool.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Créer un utilisateur parent
        User::create([
            'name' => 'Parent User',
            'email' => 'parent@smartschool.com',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
} 