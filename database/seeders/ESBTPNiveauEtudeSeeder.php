<?php

namespace Database\Seeders;

use App\Models\ESBTPNiveauEtude;
use Illuminate\Database\Seeder;

class ESBTPNiveauEtudeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création des niveaux d'études pour le BTS
        
        // 1. Première année BTS
        ESBTPNiveauEtude::create([
            'name' => 'Première année',
            'code' => 'BTS1',
            'type' => 'BTS',
            'year' => 1,
            'description' => 'Première année du Brevet de Technicien Supérieur (BTS).',
            'is_active' => true,
        ]);
        
        // 2. Deuxième année BTS
        ESBTPNiveauEtude::create([
            'name' => 'Deuxième année',
            'code' => 'BTS2',
            'type' => 'BTS',
            'year' => 2,
            'description' => 'Deuxième année du Brevet de Technicien Supérieur (BTS).',
            'is_active' => true,
        ]);
    }
}
