<?php

namespace Database\Factories;

use App\Models\ESBTPResultatMatiere;
use App\Models\ESBTPBulletin;
use App\Models\ESBTPMatiere;
use Illuminate\Database\Eloquent\Factories\Factory;

class ESBTPResultatMatiereFactory extends Factory
{
    protected $model = ESBTPResultatMatiere::class;

    public function definition()
    {
        $moyenne = $this->faker->randomFloat(2, 0, 20);

        return [
            'bulletin_id' => ESBTPBulletin::factory(),
            'matiere_id' => ESBTPMatiere::factory(),
            'moyenne' => $moyenne,
            'coefficient' => $this->faker->numberBetween(1, 4),
            'rang' => $this->faker->numberBetween(1, 50),
            'appreciation' => $this->determinerAppreciation($moyenne),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    private function determinerAppreciation($moyenne)
    {
        if ($moyenne >= 16) {
            return 'Excellent';
        } elseif ($moyenne >= 14) {
            return 'Très Bien';
        } elseif ($moyenne >= 12) {
            return 'Bien';
        } elseif ($moyenne >= 10) {
            return 'Passable';
        } else {
            return 'Insuffisant';
        }
    }
}
