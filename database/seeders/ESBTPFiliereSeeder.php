<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESBTPFiliereSeeder extends Seeder
{
    /**
     * Seeder pour créer les filières de l'ESBTP.
     *
     * @return void
     */
    public function run()
    {
        $filieres = [
            [
                'code' => 'GC-BAT',
                'name' => 'Génie civil option BATIMENT',
                'description' => 'Formation en génie civil spécialisée dans le bâtiment',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GC-TP',
                'name' => 'Génie civil option TRAVAUX PUBLICS',
                'description' => 'Formation en génie civil spécialisée dans les travaux publics',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GC-URB',
                'name' => 'Génie civil option URBANISM',
                'description' => 'Formation en génie civil spécialisée dans l\'urbanisme',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GC-GT',
                'name' => 'Génie civil option GEOMETRE-TOPOGRAPHE',
                'description' => 'Formation en génie civil spécialisée dans la topographie et la géométrie',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MGP',
                'name' => 'MINE - GEOLOGIE - PETROLE',
                'description' => 'Formation dans les domaines de la mine, de la géologie et du pétrole',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Utilisation de la méthode insertOrIgnore pour éviter les doublons
        DB::table('esbtp_filieres')->insertOrIgnore($filieres);
        
        $this->command->info('Les filières ESBTP ont été créées avec succès.');
    }
} 