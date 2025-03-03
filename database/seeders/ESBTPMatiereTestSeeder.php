<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPClasse;
use Illuminate\Support\Facades\DB;

class ESBTPMatiereTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Récupérer toutes les classes
        $classes = ESBTPClasse::all();
        
        if ($classes->isEmpty()) {
            $this->command->info('Aucune classe trouvée. Veuillez d\'abord créer des classes.');
            return;
        }
        
        // Liste des matières à créer
        $matieres = [
            [
                'name' => 'Mathematics',
                'nom' => 'Mathématiques',
                'code' => 'MATH101',
                'description' => 'Cours de mathématiques de base',
                'coefficient' => 4,
                'heures_cm' => 30,
                'heures_td' => 15,
                'heures_tp' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Physics',
                'nom' => 'Physique',
                'code' => 'PHYS101',
                'description' => 'Introduction à la physique',
                'coefficient' => 3,
                'heures_cm' => 25,
                'heures_td' => 10,
                'heures_tp' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Computer Science',
                'nom' => 'Informatique',
                'code' => 'INFO101',
                'description' => 'Principes de base de l\'informatique',
                'coefficient' => 3,
                'heures_cm' => 20,
                'heures_td' => 10,
                'heures_tp' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'English',
                'nom' => 'Anglais',
                'code' => 'ANG101',
                'description' => 'Anglais technique',
                'coefficient' => 2,
                'heures_cm' => 15,
                'heures_td' => 15,
                'heures_tp' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Civil Engineering',
                'nom' => 'Génie Civil',
                'code' => 'GC101',
                'description' => 'Introduction au génie civil',
                'coefficient' => 4,
                'heures_cm' => 30,
                'heures_td' => 10,
                'heures_tp' => 15,
                'is_active' => true,
            ],
        ];
        
        // Créer les matières
        foreach ($matieres as $matiereData) {
            foreach ($classes as $classe) {
                // Générer un code unique pour chaque classe
                $matiereData['code'] = $matiereData['code'] . '-C' . $classe->id;
                
                // Compléter les données avec les informations de la classe
                $matiereData['filiere_id'] = $classe->filiere_id;
                $matiereData['niveau_etude_id'] = $classe->niveau_etude_id;
                $matiereData['type_formation'] = 'generale';
                $matiereData['created_at'] = now();
                $matiereData['updated_at'] = now();
                
                // Créer la matière
                $matiere = ESBTPMatiere::create($matiereData);
                
                // Associer la matière à la classe
                DB::table('esbtp_classe_matiere')->insert([
                    'classe_id' => $classe->id,
                    'matiere_id' => $matiere->id,
                    'coefficient' => $matiereData['coefficient'],
                    'total_heures' => $matiereData['heures_cm'] + $matiereData['heures_td'] + $matiereData['heures_tp'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->command->info("Matière '{$matiereData['nom']}' créée et associée à la classe '{$classe->name}'");
            }
        }
    }
} 