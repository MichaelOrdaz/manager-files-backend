<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'nombre' => $this->faker->name(),
            'paterno' => $this->faker->lastName(),
            'materno' => $this->faker->lastName(),
            'celular' => $this->faker->phoneNumber(),
            'imagen' => $this->faker->imageUrl(),
            'password' => Hash::make('12345'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function password1_5()
    {
        return $this->state(function (array $attributes) {
            return [
                'password' => Hash::make('12345'),
            ];
        });
    }
}
