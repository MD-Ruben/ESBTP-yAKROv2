<?php

namespace Database\Seeders;

use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Database\Seeder;

class ESBTPAnneeUniversitaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création des années universitaires
        
        // 1. Année universitaire 2023-2024
        ESBTPAnneeUniversitaire::create([
            'name' => '2023-2024',
            'start_date' => '2023-09-01',
            'end_date' => '2024-07-31',
            'is_current' => false,
            'is_active' => true,
            'description' => 'Année universitaire 2023-2024',
        ]);
        
        // 2. Année universitaire 2024-2025 (année en cours)
        ESBTPAnneeUniversitaire::create([
            'name' => '2024-2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'is_current' => true,
            'is_active' => true,
            'description' => 'Année universitaire 2024-2025',
        ]);
        
        // 3. Année universitaire 2025-2026 (prochaine année)
        ESBTPAnneeUniversitaire::create([
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'is_current' => false,
            'is_active' => true,
            'description' => 'Année universitaire 2025-2026',
        ]);
    }
}
