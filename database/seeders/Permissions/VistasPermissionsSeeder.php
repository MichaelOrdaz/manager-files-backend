<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VistasPermissionsSeeder extends Seeder
{
  public function run ()
  {
    Permission::create(['name' => 'Dashboard', 'is_view' => '/dashboard']);
    Permission::create(['name' => 'Home', 'is_view' => '/home']);
    Permission::create(['name' => 'Collaborators', 'is_view' => '/collaborators']);
    Permission::create(['name' => 'Shared files', 'is_view' => '/shared-files']);

    $admin = Role::findByName('Admin');
    $admin->givePermissionTo('Dashboard');

    $jefe = Role::findByName('Head of Department');
    $jefe->givePermissionTo('Dashboard');
    $jefe->givePermissionTo('Home');
    $jefe->givePermissionTo('Collaborators');
    $jefe->givePermissionTo('Shared files');

    $analista = Role::findByName('Analyst');
    $analista->givePermissionTo('Dashboard');

    // $roles = Role::all();
    // foreach ($roles as $role) {
    //     $role->givePermissionTo('Dashboard');
    // }

  }
}
