<?php

namespace Database\Factories;

use App\Models\ESBTPBulletin;
use App\Models\ESBTPEtudiant;
use App\Models\ESBTPClasse;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class ESBTPBulletinFactory extends Factory
{
    protected $model = ESBTPBulletin::class;

    public function definition()
    {
        $moyenne = $this->faker->randomFloat(2, 0, 20);

        return [
            'etudiant_id' => ESBTPEtudiant::factory(),
            'classe_id' => ESBTPClasse::factory(),
            'periode' => $this->faker->randomElement(['semestre1', 'semestre2']),
            'annee_universitaire_id' => ESBTPAnneeUniversitaire::factory(),
            'moyenne_generale' => $moyenne,
            'rang' => $this->faker->numberBetween(1, 50),
            'effectif_classe' => 50,
            'mention' => $this->determinerMention($moyenne),
            'decision' => $moyenne >= 10 ? 'Admis(e)' : 'Ajourné(e)',
            'absences_justifiees' => $this->faker->numberBetween(0, 20),
            'absences_non_justifiees' => $this->faker->numberBetween(0, 10),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    private function determinerMention($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Très Bien';
        } elseif ($moyenne >= 14) {
            return 'Bien';
        } elseif ($moyenne >= 12) {
            return 'Assez Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }
}
