<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\Admin\BajasTipoSeeder;
use Database\Seeders\Admin\MaterialTipoSeeder;
use Database\Seeders\Admin\PreguntasTipoSeeder;
use Database\Seeders\Admin\AspiranteStatusSeeder;
use Database\Seeders\Admin\ExamenesCalificacionesStatusSeeder;


class PrePushSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
          RolesSeeder::class,
          UserSeeder::class,
        ]);
    }
}
