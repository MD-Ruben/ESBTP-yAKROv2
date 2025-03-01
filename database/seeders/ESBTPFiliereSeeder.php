<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESBTPFiliereSeeder extends Seeder
{
    /**
     * Seeder pour créer les filières de l'ESBTP selon les spécifications.
     *
     * @return void
     */
    public function run()
    {
        $filieres = [
            [
                'code' => 'GC-BAT',
                'libelle' => 'Génie Civil option BÂTIMENT',
                'description' => 'Formation en conception, construction et rénovation de bâtiments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GC-TP',
                'libelle' => 'Génie Civil option TRAVAUX PUBLICS',
                'description' => 'Formation en conception et réalisation d\'infrastructures de transport et d\'aménagement',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GC-URB',
                'libelle' => 'Génie Civil option URBANISME',
                'description' => 'Formation en planification urbaine et aménagement du territoire',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GC-GT',
                'libelle' => 'Génie Civil option GÉOMÈTRE-TOPOGRAPHE',
                'description' => 'Formation en relevés topographiques, cartographie et géomatique',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MGP',
                'libelle' => 'MINE - GÉOLOGIE - PÉTROLE',
                'description' => 'Formation en exploitation minière, géologie et ingénierie pétrolière',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Utilisation de la méthode insertOrIgnore pour éviter les doublons
        DB::table('esbtp_filieres')->insertOrIgnore($filieres);

        $this->command->info('Les filières ESBTP ont été créées avec succès.');
    }
}
