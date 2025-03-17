<?php

namespace Database\Factories;

use App\Models\ESBTPEvaluation;
use App\Models\ESBTPMatiere;
use App\Models\ESBTPAnneeUniversitaire;
use Illuminate\Database\Eloquent\Factories\Factory;

class ESBTPEvaluationFactory extends Factory
{
    protected $model = ESBTPEvaluation::class;

    public function definition()
    {
        return [
            'matiere_id' => ESBTPMatiere::factory(),
            'type' => $this->faker->randomElement(['Devoir', 'Examen', 'TP', 'Projet']),
            'date_evaluation' => $this->faker->dateTimeBetween('-1 year', '+1 month'),
            'coefficient' => $this->faker->numberBetween(1, 4),
            'bareme' => 20,
            'periode' => $this->faker->randomElement(['semestre1', 'semestre2']),
            'annee_universitaire_id' => ESBTPAnneeUniversitaire::factory(),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
