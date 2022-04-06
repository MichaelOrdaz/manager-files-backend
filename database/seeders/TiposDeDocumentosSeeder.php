<?php

namespace Database\Seeders;

use App\Models\TiposDeDocumentos;
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
        TiposDeDocumentos::create([
            'nombre' => 'Carpeta'
        ]);

        TiposDeDocumentos::create([
            'nombre' => 'Archivo'
        ]);
    }
}
