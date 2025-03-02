<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ESBTPFormationSeeder extends Seeder
{
    /**
     * Seeder pour créer les types de formations de l'ESBTP.
     *
     * @return void
     */
    public function run()
    {
        $formations = [
            [
                'code' => 'FG',
                'name' => 'Formation Générale',
                'description' => 'Ensemble des matières de formation générale communes à toutes les filières',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FTP',
                'name' => 'Formation Technologique et Professionnelle',
                'description' => 'Ensemble des matières spécifiques à la formation technologique et professionnelle',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Utilisation de la méthode insertOrIgnore pour éviter les doublons
        DB::table('esbtp_formations')->insertOrIgnore($formations);
        
        $this->command->info('Les types de formations ESBTP ont été créés avec succès.');
    }
}
