<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'lastname' => $this->faker->lastName(),
            'second_lastname' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'image' => $this->faker->imageUrl(),
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'department_id' => $this->faker->randomElement([null, Department::factory()])
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
