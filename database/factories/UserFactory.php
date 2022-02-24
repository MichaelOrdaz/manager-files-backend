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
            'email_verified_at' => now(),
            'password' => Hash::make('12345'),
            'remember_token' => Str::random(10),
            'firebase_uid' => $this->faker->uuid(),
            'activo' => 1,
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

    public function correoAspirante()
    {
        return $this->state(function (array $attributes) {
            return [
                'email' => 'aspirante' . $this->faker->bothify('_???_####') . '@test-puller.mx',
            ];
        }); 
    }


}
