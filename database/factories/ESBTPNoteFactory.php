<?php

namespace Database\Factories;

use App\Models\ESBTPNote;
use App\Models\ESBTPEvaluation;
use App\Models\ESBTPEtudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ESBTPNoteFactory extends Factory
{
    protected $model = ESBTPNote::class;

    public function definition()
    {
        return [
            'evaluation_id' => ESBTPEvaluation::factory(),
            'etudiant_id' => ESBTPEtudiant::factory(),
            'valeur' => $this->faker->randomFloat(2, 0, 20),
            'observation' => $this->faker->optional()->sentence,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
