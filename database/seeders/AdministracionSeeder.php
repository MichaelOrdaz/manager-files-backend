<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Admin\EstadoSeeder;
use Database\Seeders\Admin\MunicipioSeeder;

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
