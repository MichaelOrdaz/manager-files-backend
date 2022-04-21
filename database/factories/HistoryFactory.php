<?php

namespace Database\Factories;

use App\Models\Action;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'document_id' => Document::factory(),
            'user_id' => User::factory(),
            'action_id' => Action::factory(),
        ];
    }
}
