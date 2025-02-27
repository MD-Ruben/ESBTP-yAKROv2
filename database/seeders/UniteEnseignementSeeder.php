<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UniteEnseignement;
use App\Models\Formation;
use App\Models\Parcours;
use App\Models\User;

class UniteEnseignementSeeder extends Seeder
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
        
        // Récupérer les formations et parcours
        $formationInfoL = Formation::where('code', 'INFO-L')->first();
        $parcoursGL = Parcours::where('code', 'INFO-GL')->first();
        $parcoursSR = Parcours::where('code', 'INFO-SR')->first();
        $parcoursIA = Parcours::where('code', 'INFO-IA')->first();
        
        // Création des unités d'enseignement
        // Les UE sont comme des chapitres d'un livre
        // Chacune contient des connaissances spécifiques sur un sujet
        $unites = [
            // UE communes à tous les parcours de Licence Informatique (Semestre 1)
            [
                'code' => 'INFO-L1-S1-ALGO',
                'name' => 'Algorithmique et Programmation 1',
                'description' => 'Introduction aux algorithmes et à la programmation',
                'credits' => 6,
                'semester' => 1,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L1-S1-MATH',
                'name' => 'Mathématiques pour l\'informatique',
                'description' => 'Bases mathématiques pour l\'informatique: logique, ensembles, algèbre',
                'credits' => 6,
                'semester' => 1,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L1-S1-ARCHI',
                'name' => 'Architecture des ordinateurs',
                'description' => 'Introduction à l\'architecture matérielle des ordinateurs',
                'credits' => 4,
                'semester' => 1,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L1-S1-LANG',
                'name' => 'Langues et Communication',
                'description' => 'Anglais technique et techniques de communication',
                'credits' => 4,
                'semester' => 1,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // UE communes à tous les parcours de Licence Informatique (Semestre 2)
            [
                'code' => 'INFO-L1-S2-ALGO',
                'name' => 'Algorithmique et Programmation 2',
                'description' => 'Structures de données et algorithmes avancés',
                'credits' => 6,
                'semester' => 2,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L1-S2-BDD',
                'name' => 'Bases de données',
                'description' => 'Conception et utilisation des bases de données relationnelles',
                'credits' => 6,
                'semester' => 2,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L1-S2-WEB',
                'name' => 'Développement Web',
                'description' => 'Introduction au développement web: HTML, CSS, JavaScript',
                'credits' => 4,
                'semester' => 2,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L1-S2-PROJ',
                'name' => 'Projet de programmation',
                'description' => 'Réalisation d\'un projet de programmation en équipe',
                'credits' => 4,
                'semester' => 2,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => null, // Commun à tous les parcours
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // UE spécifiques au parcours Génie Logiciel (Semestre 3)
            [
                'code' => 'INFO-L2-S3-GL-POO',
                'name' => 'Programmation Orientée Objet',
                'description' => 'Concepts et pratiques de la programmation orientée objet',
                'credits' => 6,
                'semester' => 3,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => $parcoursGL ? $parcoursGL->id : 1,
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L2-S3-GL-WEBAV',
                'name' => 'Développement Web Avancé',
                'description' => 'Frameworks et développement web côté serveur',
                'credits' => 6,
                'semester' => 3,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => $parcoursGL ? $parcoursGL->id : 1,
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // UE spécifiques au parcours Systèmes et Réseaux (Semestre 3)
            [
                'code' => 'INFO-L2-S3-SR-SYS',
                'name' => 'Systèmes d\'exploitation',
                'description' => 'Principes et fonctionnement des systèmes d\'exploitation',
                'credits' => 6,
                'semester' => 3,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => $parcoursSR ? $parcoursSR->id : 2,
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L2-S3-SR-RES',
                'name' => 'Réseaux informatiques',
                'description' => 'Principes et protocoles des réseaux informatiques',
                'credits' => 6,
                'semester' => 3,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => $parcoursSR ? $parcoursSR->id : 2,
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            
            // UE spécifiques au parcours Intelligence Artificielle (Semestre 3)
            [
                'code' => 'INFO-L2-S3-IA-INTRO',
                'name' => 'Introduction à l\'Intelligence Artificielle',
                'description' => 'Concepts fondamentaux de l\'intelligence artificielle',
                'credits' => 6,
                'semester' => 3,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => $parcoursIA ? $parcoursIA->id : 3,
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
            [
                'code' => 'INFO-L2-S3-IA-STAT',
                'name' => 'Statistiques et Probabilités pour l\'IA',
                'description' => 'Bases statistiques pour l\'intelligence artificielle',
                'credits' => 6,
                'semester' => 3,
                'formation_id' => $formationInfoL ? $formationInfoL->id : 1,
                'parcours_id' => $parcoursIA ? $parcoursIA->id : 3,
                'responsible_id' => null,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        ];

        foreach ($unites as $unite) {
            UniteEnseignement::create($unite);
        }

        $this->command->info('Unités d\'enseignement créées avec succès!');
    }
} 