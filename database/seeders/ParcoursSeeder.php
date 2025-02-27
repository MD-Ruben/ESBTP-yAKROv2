<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parcours;
use App\Models\Formation;
use App\Models\User;

class ParcoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer un utilisateur administrateur pour l'attribution
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : null;
        
        // Récupérer les formations
        $formationInfoL = Formation::where('code', 'INFO-L')->first();
        $formationInfoM = Formation::where('code', 'INFO-M')->first();
        $formationMathL = Formation::where('code', 'MATH-L')->first();
        $formationGCL = Formation::where('code', 'GC-L')->first();
        $formationGCM = Formation::where('code', 'GC-M')->first();
        
        // Création des parcours
        // Les parcours sont comme des chemins dans une forêt
        // Chacun mène à une destination spécifique (spécialisation)
        $parcours = [
            // Parcours pour la Licence en Informatique
            [
                'code' => 'INFO-GL',
                'name' => 'Génie Logiciel',
                'description' => 'Spécialisation en développement et architecture logicielle',
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-SR',
                'name' => 'Systèmes et Réseaux',
                'description' => 'Spécialisation en administration systèmes et réseaux',
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-IA',
                'name' => 'Intelligence Artificielle',
                'description' => 'Spécialisation en algorithmes d\'IA et apprentissage automatique',
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Parcours pour le Master en Informatique
            [
                'code' => 'INFO-M-GL',
                'name' => 'Génie Logiciel Avancé',
                'description' => 'Spécialisation avancée en architecture et qualité logicielle',
                'formation_id' => $formationInfoM ? $formationInfoM->id : 3,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-M-IA',
                'name' => 'Intelligence Artificielle et Data Science',
                'description' => 'Spécialisation avancée en IA, big data et analyse prédictive',
                'formation_id' => $formationInfoM ? $formationInfoM->id : 3,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Parcours pour la Licence en Mathématiques
            [
                'code' => 'MATH-STAT',
                'name' => 'Statistiques et Probabilités',
                'description' => 'Spécialisation en analyse statistique et modèles probabilistes',
                'formation_id' => $formationMathL ? $formationMathL->id : 2,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'MATH-ANAL',
                'name' => 'Analyse Mathématique',
                'description' => 'Spécialisation en analyse fonctionnelle et équations différentielles',
                'formation_id' => $formationMathL ? $formationMathL->id : 2,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Parcours pour la Licence en Génie Civil
            [
                'code' => 'GC-STRUCT',
                'name' => 'Structures et Matériaux',
                'description' => 'Spécialisation en conception et calcul de structures',
                'formation_id' => $formationGCL ? $formationGCL->id : 7,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'GC-HYDR',
                'name' => 'Hydraulique et Environnement',
                'description' => 'Spécialisation en gestion des ressources en eau et environnement',
                'formation_id' => $formationGCL ? $formationGCL->id : 7,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Parcours pour le Master en Génie Civil
            [
                'code' => 'GC-M-STRUCT',
                'name' => 'Structures Avancées',
                'description' => 'Spécialisation avancée en conception et calcul de structures complexes',
                'formation_id' => $formationGCM ? $formationGCM->id : 9,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'GC-M-GEOTECH',
                'name' => 'Géotechnique',
                'description' => 'Spécialisation avancée en mécanique des sols et fondations',
                'formation_id' => $formationGCM ? $formationGCM->id : 9,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        ];

        foreach ($parcours as $p) {
            Parcours::create($p);
        }

        $this->command->info('Parcours créés avec succès!');
    }
} 