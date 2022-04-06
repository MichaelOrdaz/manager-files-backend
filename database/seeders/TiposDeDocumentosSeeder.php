<?php

namespace Database\Seeders;

use App\Models\TipoDeDocumento;
use Illuminate\Database\Seeder;

class TiposDeDocumentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoDeDocumento::create([
            'nombre' => 'Carpeta'
        ]);

        TipoDeDocumento::create([
            'nombre' => 'Archivo'
        ]);
    }
}
