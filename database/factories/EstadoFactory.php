<?php

namespace Database\Factories;

use App\Models\Estado;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstadoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Estado::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->word(),
            'activo' => $this->faker->randomElement([0,1,1]),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'activo' => 1,
            ];
        });
    }
}
