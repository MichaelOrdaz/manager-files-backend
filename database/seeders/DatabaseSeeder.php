<?php

namespace Database\Seeders;

use App\Models\PreguntaTipo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            UserSeeder::class,

            ResourceSeeder::class,
            AdministracionSeeder::class,

            // DesarrolloSeeder::class,
        ]);
    }
}
