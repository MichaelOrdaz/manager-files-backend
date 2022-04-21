<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Action::create([
            'name' => 'Crear'
        ]);

        Action::create([
            'name' => 'Eliminar'
        ]);

        Action::create([
            'name' => 'Modificar'
        ]);

        Action::create([
            'name' => 'Compartir'
        ]);
    }
}
