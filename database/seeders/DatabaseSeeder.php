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
        // Note: SuperAdminSeeder n'est pas appelé ici car il sera exécuté
        // pendant le processus d'installation via l'interface utilisateur

        // Seeders du système et base de l'application
        $this->call([
            ESBTPRoleSeeder::class,           // Création des rôles et permissions
            ESBTPFiliereSeeder::class,
            ESBTPMatiereSeeder::class,
        ]);

        // Seeders pour les données de référence ESBTP
        $this->call([
            ESBTPAnneeUniversitaireSeeder::class,  // Années universitaires
            ESBTPNiveauEtudeSeeder::class,         // Niveaux d'études (BTS 1, BTS 2)
            ESBTPMatiereNiveauSeeder::class,       // Relations matières-niveaux
        ]);

        $this->call(ESBTPTestDataSeeder::class);
    }
}
