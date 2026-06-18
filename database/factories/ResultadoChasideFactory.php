<?php

namespace Database\Factories;

use App\Models\Estudiante;
use App\Models\ResultadoChaside;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResultadoChasideFactory extends Factory
{
    protected $model = ResultadoChaside::class;

    public function definition(): array
    {
        $areas     = ['C', 'H', 'A', 'S', 'I', 'D', 'E'];
        $principal = $this->faker->randomElement($areas);
        $secundaria = $this->faker->randomElement(array_values(array_diff($areas, [$principal])));

        $respuestas = [];
        for ($i = 1; $i <= 98; $i++) {
            $respuestas[$i] = $this->faker->boolean();
        }

        return [
            'estudiante_id'   => Estudiante::factory(),
            'puntaje_c'       => $this->faker->numberBetween(0, 14),
            'puntaje_h'       => $this->faker->numberBetween(0, 14),
            'puntaje_a'       => $this->faker->numberBetween(0, 14),
            'puntaje_s'       => $this->faker->numberBetween(0, 14),
            'puntaje_i'       => $this->faker->numberBetween(0, 14),
            'puntaje_d'       => $this->faker->numberBetween(0, 14),
            'puntaje_e'       => $this->faker->numberBetween(0, 14),
            'area_principal'  => $principal,
            'area_secundaria' => $secundaria,
            'respuestas_json' => $respuestas,
            'share_token'     => Str::random(32),
        ];
    }
}
