<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VistasPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION: Create permissions for role
    Permission::create(['name' => 'Dashboard', 'is_view' => '/dashboard']);

    $admin = Role::findByName('Administrador');
    $admin->givePermissionTo(Permission::all());

    $jefe = Role::findByName('Jefe de departamento');
    $jefe->givePermissionTo('Dashboard');

    $analista = Role::findByName('Analista');
    $analista->givePermissionTo('Dashboard');

    // $roles = Role::all();
    // foreach ($roles as $role) {
    //     $role->givePermissionTo('Dashboard');
    // }

  }
}
