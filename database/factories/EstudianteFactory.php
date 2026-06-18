<?php

namespace Database\Factories;

use App\Models\Colegio;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition(): array
    {
        return [
            'colegio_id'    => Colegio::factory(),
            'nombre'        => $this->faker->firstName(),
            'apellido'      => $this->faker->lastName(),
            'sexo'          => $this->faker->randomElement(['Masculino', 'Femenino', 'Otro']),
            'edad'          => $this->faker->numberBetween(12, 35),
            'celular'       => '7' . $this->faker->numerify('#######'),
            'email'         => $this->faker->optional()->email(),
            'nombre_madre'  => $this->faker->optional()->name('female'),
            'celular_madre' => $this->faker->optional()->numerify('7#######'),
            'nombre_padre'  => $this->faker->optional()->name('male'),
            'celular_padre' => $this->faker->optional()->numerify('7#######'),
        ];
    }
}
