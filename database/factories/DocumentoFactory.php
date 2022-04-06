<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Documento;
use App\Models\TipoDeDocumento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->text(255),
            'ubicacion' => $this->faker->url(),
            'tipo_id' => TipoDeDocumento::factory(),
            'creador_id' => User::factory(),
            'antecesor_id' => Documento::factory(),
            'departamento_id' => Departamento::factory(),
        ];
    }
}
