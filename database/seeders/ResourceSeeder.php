<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuracion::create([
            'nombre' => 'imagen inicio de sesion',
            'descripcion' => 'imagen inicio de sesion',
            'tipo' => 'image',
            'valor' => '',
            'valor_default' => 'public/Configuraciones/logo.png',
        ]);

        Configuracion::create([
          'nombre' => 'imagen menu lateral',
          'descripcion' => 'imagen menu lateral',
          'tipo' => 'image',
          'valor' => '',
          'valor_default' => 'public/Configuraciones/logo.png',
        ]);

        Configuracion::create([
          'nombre' => 'firma kardex',
          'descripcion' => 'firma kardex',
          'tipo' => 'image',
          'valor' => '',
          'valor_default' => 'public/Configuraciones/sinfirma.png',
        ]);

        Configuracion::create([
          'nombre' => 'firma del director',
          'descripcion' => 'firma del director',
          'tipo' => 'image',
          'valor' => '',
          'valor_default' => 'public/Configuraciones/sinfirma.png',
        ]);

        Configuracion::create([
          'nombre' => 'revisor de kardex',
          'descripcion' => 'revisor de kardex',
          'tipo' => 'image',
          'valor' => '',
          'valor_default' => 'public/Configuraciones/sinfirma.png',
        ]);
    }
}
