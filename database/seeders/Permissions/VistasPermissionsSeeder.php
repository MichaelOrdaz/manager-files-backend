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

    $admin = Role::findByName('Admin');
    $admin->givePermissionTo(Permission::all());

    $jefe = Role::findByName('Head of Department');
    $jefe->givePermissionTo('Dashboard');

    $analista = Role::findByName('Analyst');
    $analista->givePermissionTo('Dashboard');

    // $roles = Role::all();
    // foreach ($roles as $role) {
    //     $role->givePermissionTo('Dashboard');
    // }

  }
}
