<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seeders pour les structures administratives
        $this->call([
            UFRsSeeder::class,
            FormationsSeeder::class,
            ParcoursSeeder::class,
            UniteEnseignementSeeder::class,
            ElementConstitutifSeeder::class,
            ClassroomSeeder::class,
            CourseSessionSeeder::class,
            EvaluationSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
