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
                'name' => ($anneeActuelle-1) . '-' . $anneeActuelle,
                'start_date' => ($anneeActuelle-1) . '-09-15',
                'end_date' => $anneeActuelle . '-07-15',
                'is_current' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => $anneeActuelle . '-' . ($anneeActuelle+1),
                'start_date' => $anneeActuelle . '-09-15',
                'end_date' => ($anneeActuelle+1) . '-07-15',
                'is_current' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => ($anneeActuelle+1) . '-' . ($anneeActuelle+2),
                'start_date' => ($anneeActuelle+1) . '-09-15',
                'end_date' => ($anneeActuelle+2) . '-07-15',
                'is_current' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Utilisation de la méthode insertOrIgnore pour éviter les doublons
        DB::table('esbtp_annee_universitaires')->insertOrIgnore($anneesUniversitaires);
        
        $this->command->info('Les années universitaires ESBTP ont été créées avec succès.');
    }
}
