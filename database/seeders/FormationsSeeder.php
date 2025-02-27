<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formation;
use App\Models\UFR;
use App\Models\Department;
use App\Models\User;

class FormationsSeeder extends Seeder
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
        
        // Récupérer les UFRs
        $ufrSFA = UFR::where('code', 'SFA')->first();
        $ufrSESA = UFR::where('code', 'SESA')->first();
        $ufrSHS = UFR::where('code', 'SHS')->first();
        $ufrBTP = UFR::where('code', 'BTP')->first();
        
        // Récupérer les départements
        $deptMaths = Department::where('name', 'Mathématiques')->first();
        $deptSciences = Department::where('name', 'Sciences')->first();
        
        // Création des formations
        // Les formations sont comme des recettes de cuisine
        // Chacune a ses ingrédients (cours) et sa durée de préparation (années d'études)
        $formations = [
            // Formations de l'UFR SFA
            [
                'code' => 'INFO-L',
                'name' => 'Licence en Informatique',
                'description' => 'Formation en informatique générale, algorithmique et programmation',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrSFA ? $ufrSFA->id : 1,
                'department_id' => $deptSciences ? $deptSciences->id : null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'MATH-L',
                'name' => 'Licence en Mathématiques',
                'description' => 'Formation en mathématiques fondamentales et appliquées',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrSFA ? $ufrSFA->id : 1,
                'department_id' => $deptMaths ? $deptMaths->id : null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-M',
                'name' => 'Master en Informatique',
                'description' => 'Formation avancée en informatique, spécialisation en développement logiciel ou réseaux',
                'level' => 'Master',
                'duration' => 2,
                'ufr_id' => $ufrSFA ? $ufrSFA->id : 1,
                'department_id' => $deptSciences ? $deptSciences->id : null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Formations de l'UFR SESA
            [
                'code' => 'ECO-L',
                'name' => 'Licence en Économie',
                'description' => 'Formation en économie générale et appliquée',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrSESA ? $ufrSESA->id : 2,
                'department_id' => null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'GEST-L',
                'name' => 'Licence en Gestion',
                'description' => 'Formation en gestion d\'entreprise et management',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrSESA ? $ufrSESA->id : 2,
                'department_id' => null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Formations de l'UFR SHS
            [
                'code' => 'SOCIO-L',
                'name' => 'Licence en Sociologie',
                'description' => 'Formation en sociologie et études sociales',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrSHS ? $ufrSHS->id : 3,
                'department_id' => null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // Formations de l'UFR BTP
            [
                'code' => 'GC-L',
                'name' => 'Licence en Génie Civil',
                'description' => 'Formation en génie civil et construction',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrBTP ? $ufrBTP->id : 7,
                'department_id' => null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'ARCH-L',
                'name' => 'Licence en Architecture',
                'description' => 'Formation en architecture et design',
                'level' => 'Licence',
                'duration' => 3,
                'ufr_id' => $ufrBTP ? $ufrBTP->id : 7,
                'department_id' => null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'GC-M',
                'name' => 'Master en Génie Civil',
                'description' => 'Formation avancée en génie civil, spécialisation en structures ou hydraulique',
                'level' => 'Master',
                'duration' => 2,
                'ufr_id' => $ufrBTP ? $ufrBTP->id : 7,
                'department_id' => null,
                'coordinator_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        ];

        foreach ($formations as $formation) {
            Formation::create($formation);
        }

        $this->command->info('Formations créées avec succès!');
    }
} 