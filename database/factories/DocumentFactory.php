<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Document;
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
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->text(255),
            'location' => $this->faker->url(),
            'type_id' => DocumentType::factory(),
            'creator_id' => User::factory(),
            'parent_id' => Document::factory(),
            'department_id' => Department::factory(),
        ];
    }
}
