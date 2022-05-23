<?php

namespace Database\Seeders;

use Database\Seeders\Permissions\DeparmentPermissionsSeeder;
use Database\Seeders\Permissions\DocumentPermissionsSeeder;
use Database\Seeders\Permissions\RolePermissionsSeeder;
use Database\Seeders\Permissions\SharedPermissionsSeeder;
use Database\Seeders\Permissions\UserPermissionsSeeder;
use Database\Seeders\Permissions\ViewPermissionsSeeder;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


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
            ViewPermissionsSeeder::class,
            UserPermissionsSeeder::class,
            RolePermissionsSeeder::class,
            DeparmentPermissionsSeeder::class,
            DocumentPermissionsSeeder::class,
            SharedPermissionsSeeder::class
        ]);

    }
}
