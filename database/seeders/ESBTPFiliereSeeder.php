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
                'code' => 'BAT',
                'name' => 'Building',
                'description' => 'Training in building construction and design',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CE',
                'name' => 'Civil Engineering',
                'description' => 'Training in civil engineering techniques and methods',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MGP',
                'name' => 'Management and Project',
                'description' => 'Training in construction project management',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PW',
                'name' => 'Public Works',
                'description' => 'Training in public infrastructure design and implementation',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TOP',
                'name' => 'Topography',
                'description' => 'Training in topographic surveys and measurement techniques',
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