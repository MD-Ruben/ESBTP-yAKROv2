<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPNiveauEtude;
use Illuminate\Support\Facades\DB;

class ESBTPMatiereNiveauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Vider la table pivot avant d'ajouter de nouvelles données
        DB::table('esbtp_matiere_niveau')->truncate();

        // Récupérer toutes les matières et tous les niveaux d'études
        $matieres = ESBTPMatiere::all();
        $niveauxEtudes = ESBTPNiveauEtude::all();

        // Si nous n'avons pas de matières ou de niveaux d'études, arrêter l'exécution
        if ($matieres->isEmpty() || $niveauxEtudes->isEmpty()) {
            $this->command->info('Aucune matière ou niveau d\'études trouvé. Impossible de créer des relations.');
            return;
        }

        // Pour chaque matière, associer au moins un niveau d'études
        foreach ($matieres as $matiere) {
            // Pour simplifier, nous allons associer chaque matière à un ou deux niveaux d'études aléatoires
            $niveauxToAttach = $niveauxEtudes->random(rand(1, min(2, $niveauxEtudes->count())));
            
            foreach ($niveauxToAttach as $niveau) {
                // Générer un coefficient aléatoire entre 1 et 5
                $coefficient = rand(10, 50) / 10;
                
                // Générer un nombre d'heures aléatoire entre 20 et 60
                $heuresCours = rand(20, 60);
                
                // Créer l'entrée dans la table pivot
                DB::table('esbtp_matiere_niveau')->insert([
                    'matiere_id' => $matiere->id,
                    'niveau_etude_id' => $niveau->id,
                    'coefficient' => $coefficient,
                    'heures_cours' => $heuresCours,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $this->command->info("Matière '{$matiere->name}' associée au niveau '{$niveau->name}' avec coefficient {$coefficient} et {$heuresCours} heures.");
            }
        }
        
        $this->command->info('Toutes les relations entre matières et niveaux d\'études ont été créées avec succès.');
    }
}
