<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESBTPAnneeUniversitaireSeeder extends Seeder
{
    /**
     * Seeder pour créer les années universitaires de l'ESBTP.
     *
     * @return void
     */
    public function run()
    {
        $anneeActuelle = date('Y');
        
        $anneesUniversitaires = [
            [
                'libelle' => ($anneeActuelle-1) . '-' . $anneeActuelle,
                'date_debut' => ($anneeActuelle-1) . '-09-15',
                'date_fin' => $anneeActuelle . '-07-15',
                'est_actuelle' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => $anneeActuelle . '-' . ($anneeActuelle+1),
                'date_debut' => $anneeActuelle . '-09-15',
                'date_fin' => ($anneeActuelle+1) . '-07-15',
                'est_actuelle' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle' => ($anneeActuelle+1) . '-' . ($anneeActuelle+2),
                'date_debut' => ($anneeActuelle+1) . '-09-15',
                'date_fin' => ($anneeActuelle+2) . '-07-15',
                'est_actuelle' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Utilisation de la méthode insertOrIgnore pour éviter les doublons
        DB::table('esbtp_annee_universitaires')->insertOrIgnore($anneesUniversitaires);
        
        $this->command->info('Les années universitaires ESBTP ont été créées avec succès.');
    }
}
