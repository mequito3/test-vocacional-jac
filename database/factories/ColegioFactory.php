<?php

namespace Database\Factories;

use App\Models\Colegio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColegioFactory extends Factory
{
    protected $model = Colegio::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->unique()->company() . ' ' . $this->faker->lexify('??'),
        ];
    }
}
