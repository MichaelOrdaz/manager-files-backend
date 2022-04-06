<?php

namespace Database\Factories;

use App\Models\AccionHistorial;
use App\Models\Documento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistorialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'documento_id' => Documento::factory(),
            'user_id' => User::factory(),
            'accion_id' => AccionHistorial::factory(),
        ];
    }
}
