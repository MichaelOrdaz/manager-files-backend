<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Admin\EstadoSeeder;
use Database\Seeders\Admin\GruposSeeder;
use Database\Seeders\Admin\DocenteSeeder;
use Database\Seeders\Admin\PeriodoSeeder;
use Database\Seeders\Admin\BajasTipoSeeder;
use Database\Seeders\Admin\MunicipioSeeder;
use Database\Seeders\Admin\ComponenteSeeder;
use Database\Seeders\Admin\ExamenTipoSeeder;
use Database\Seeders\Admin\EspecialidadSeeder;
use Database\Seeders\Admin\MaterialTipoSeeder;
use Database\Seeders\Admin\PreguntasTipoSeeder;
use Database\Seeders\Admin\DatosAcademicosSeeder;
use Database\Seeders\Admin\AspiranteStatusSeeder;
use Database\Seeders\Admin\ConfiguracionAdmisionSeeder;
use Database\Seeders\Admin\ExamenesCalificacionesStatusSeeder;

class AdministracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            EstadoSeeder::class,
            MunicipioSeeder::class,
        ]);
    }
}
