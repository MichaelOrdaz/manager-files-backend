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
            'location' => $this->faker->file(base_path('tests/Feature/FileFaker'), storage_path('app/public/files'), true),
            'date' => $this->faker->dateTimeThisDecade()->format('Y-m-d'),
            'min_identifier' => $minIdentifier,
            'max_identifier' => $this->faker->numberBetween($minIdentifier, ($minIdentifier + 10)),
            'tags' => ([$this->faker->word(), $this->faker->word()]),
            'type_id' => DocumentType::factory(),
            'creator_id' => User::factory(),
        ];
    }
}
