<?php

namespace Database\Seeders;

use App\Models\AccionHistorial;
use Illuminate\Database\Seeder;

class AccionHistorialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccionHistorial::create([
            'nombre' => 'Crear'
        ]);

        AccionHistorial::create([
            'nombre' => 'Eliminar'
        ]);

        AccionHistorial::create([
            'nombre' => 'Modificar'
        ]);

        AccionHistorial::create([
            'nombre' => 'Compartir'
        ]);
    }
}
