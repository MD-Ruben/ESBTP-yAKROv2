<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ESBTPTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création d'une filière de test
        $filiereId = DB::table('esbtp_filieres')->insertGetId([
            'name' => 'Génie Civil',
            'code' => 'GC',
            'description' => 'Formation en génie civil',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Création d'une formation de test
        $formationId = DB::table('esbtp_formations')->insertGetId([
            'name' => 'Formation Générale',
            'code' => 'FG',
            'description' => 'Formation générale standard',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Création d'un niveau d'étude de test
        $niveauId = DB::table('esbtp_niveau_etudes')->insertGetId([
            'name' => 'BTS 1ère année',
            'code' => 'BTS1',
            'description' => 'Première année de BTS',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Création d'une année universitaire de test
        $anneeId = DB::table('esbtp_annee_universitaires')->insertGetId([
            'name' => '2023-2024',
            'start_date' => '2023-09-01',
            'end_date' => '2024-07-31',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Vérification de la structure de la table esbtp_classes
        $tableColumns = DB::getSchemaBuilder()->getColumnListing('esbtp_classes');
        $hasFormationColumn = in_array('formation_id', $tableColumns);

        // Création d'une classe de test
        $classData = [
            'name' => 'BTS 1 Génie Civil',
            'code' => 'BTS1-GC',
            'filiere_id' => $filiereId,
            'niveau_etude_id' => $niveauId,
            'annee_universitaire_id' => $anneeId,
            'capacity' => 30,
            'description' => 'Classe de première année de BTS en Génie Civil',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        // Ajout de formation_id uniquement si la colonne existe
        if ($hasFormationColumn) {
            $classData['formation_id'] = $formationId;
        }

        DB::table('esbtp_classes')->insert($classData);

        $this->command->info('Données de test créées avec succès!');
    }
}
