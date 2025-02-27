<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Appel des seeders dans l'ordre approprié
        // D'abord les rôles et permissions, puis les utilisateurs
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
