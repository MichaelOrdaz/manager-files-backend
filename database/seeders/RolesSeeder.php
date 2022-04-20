<?php

namespace Database\Seeders;

use Database\Seeders\Permissions\RolePermissionsSeeder;
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

        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Head of Department']);
        Role::create(['name' => 'Analyst']);

        $this->call([
            VistasPermissionsSeeder::class,
            UsuarioPermissionsSeeder::class,
            RolePermissionsSeeder::class,
        ]);

    }
}
