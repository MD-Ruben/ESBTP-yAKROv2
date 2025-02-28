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
            SuperAdminSeeder::class,
            // Ajout des seeders pour les tables manquantes
            DepartmentsAndDesignationsSeeder::class,
            ClassesAndSectionsSeeder::class,
            SubjectsSeeder::class,
            // Seeders pour ESBTP
            ESBTPFiliereSeeder::class,
            ESBTPNiveauEtudeSeeder::class,
            ESBTPAnneeUniversitaireSeeder::class,
            // Autres seeders si nécessaire
        ]);
    }
}
