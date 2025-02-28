<?php

namespace Database\Seeders;

use App\Models\ESBTPFiliere;
use Illuminate\Database\Seeder;

class ESBTPFiliereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création des filières principales
        
        // 1. Génie Civil
        $genieCivil = ESBTPFiliere::create([
            'name' => 'GÉNIE CIVIL',
            'code' => 'GC',
            'description' => 'Formation en génie civil couvrant les domaines de la construction, des travaux publics, de l\'urbanisme et de la topographie.',
            'is_active' => true,
            'parent_id' => null, // Filière principale
        ]);
        
        // 2. Mine - Géologie - Pétrole
        $mineGeologiePetrole = ESBTPFiliere::create([
            'name' => 'MINE - GÉOLOGIE - PÉTROLE',
            'code' => 'MGP',
            'description' => 'Formation dans les domaines des mines, de la géologie et du pétrole.',
            'is_active' => true,
            'parent_id' => null, // Filière principale
        ]);
        
        // Création des options (sous-filières) pour le Génie Civil
        
        // 1. Option Bâtiments
        ESBTPFiliere::create([
            'name' => 'BÂTIMENT',
            'code' => 'GC-BAT',
            'description' => 'Option Bâtiment du Génie Civil, spécialisée dans la conception et la construction de bâtiments.',
            'is_active' => true,
            'parent_id' => $genieCivil->id, // Sous-filière de Génie Civil
        ]);
        
        // 2. Option Travaux Publics
        ESBTPFiliere::create([
            'name' => 'TRAVAUX PUBLICS',
            'code' => 'GC-TP',
            'description' => 'Option Travaux Publics du Génie Civil, spécialisée dans la conception et la construction d\'infrastructures publiques.',
            'is_active' => true,
            'parent_id' => $genieCivil->id, // Sous-filière de Génie Civil
        ]);
        
        // 3. Option Géomètre Topographe
        ESBTPFiliere::create([
            'name' => 'GÉOMÈTRE-TOPOGRAPHE',
            'code' => 'GC-GT',
            'description' => 'Option Géomètre-Topographe du Génie Civil, spécialisée dans les relevés topographiques et les mesures de terrain.',
            'is_active' => true,
            'parent_id' => $genieCivil->id, // Sous-filière de Génie Civil
        ]);
        
        // 4. Option Urbanisme
        ESBTPFiliere::create([
            'name' => 'URBANISME',
            'code' => 'GC-URB',
            'description' => 'Option Urbanisme du Génie Civil, spécialisée dans la planification et l\'aménagement urbain.',
            'is_active' => true,
            'parent_id' => $genieCivil->id, // Sous-filière de Génie Civil
        ]);
    }
}
