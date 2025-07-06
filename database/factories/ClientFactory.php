<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'      => fake()->company(), // Genera un nombre de compañía falso
            'rif'       => fake()->unique()->numerify('J-########-#'), // Genera un RIF único
            'state_id'  => State::inRandomOrder()->first()->id, // Asigna un estado aleatorio
            'type'      => fake()->randomElement(['residencial', 'comercial', 'industrial']),
        ];
    }
}