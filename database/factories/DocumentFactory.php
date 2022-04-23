<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $minIdentifier = $this->faker->randomNumber(5, true);
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->text(255),
            'location' => $this->faker->randomElement(['public/faker', 'public/random', 'public/faker/first']),
            'date' => $this->faker->dateTimeThisDecade()->format('Y-m-d'),
            'min_identifier' => $minIdentifier,
            'max_identifier' => $this->faker->numberBetween($minIdentifier),
            'tags' => $this->faker->randomElements(),
            'type_id' => DocumentType::factory(),
            'creator_id' => User::factory(),
            'department_id' => Department::factory(),
        ];
    }
}
