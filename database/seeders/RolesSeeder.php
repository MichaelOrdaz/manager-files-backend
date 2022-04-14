<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

use Database\Seeders\Permissions\VistasPermissionsSeeder;
use Database\Seeders\Permissions\UsuarioPermissionsSeeder;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Jefe de departamento']);
        Role::create(['name' => 'Analista']);

        $this->call([
            VistasPermissionsSeeder::class,
            UsuarioPermissionsSeeder::class,
        ]);

    }
}
