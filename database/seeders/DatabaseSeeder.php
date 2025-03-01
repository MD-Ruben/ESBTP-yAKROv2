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
        $this->call([
            RoleSeeder::class,
            // Ne pas inclure de seeder pour SuperAdmin - à créer lors de l'installation
            // Seeders pour ESBTP
            ESBTPFiliereSeeder::class,
            ESBTPNiveauEtudeSeeder::class,
            ESBTPFormationSeeder::class,
            ESBTPMatiereSeeder::class,
            ESBTPClasseSeeder::class,
        ]);
    }
}
