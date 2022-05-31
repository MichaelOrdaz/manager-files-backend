<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ViewPermissionsSeeder extends Seeder
{
  public function run ()
  {
    Permission::create(['name' => 'Dashboard', 'is_view' => '/dashboard']);
    Permission::create(['name' => 'Home', 'is_view' => '/home']);
    Permission::create(['name' => 'Collaborators', 'is_view' => '/collaborators']);
    Permission::create(['name' => 'Shared files', 'is_view' => '/shared-files']);
    Permission::create(['name' => 'Users management', 'is_view' => '/users-management']);
    Permission::create(['name' => 'Admin Dashboard', 'is_view' => '/admin-home']);
    Permission::create(['name' => 'Analyst home', 'is_view' => '/analyst']);

    $admin = Role::findByName('Admin');
    $admin->givePermissionTo('Dashboard');
    $admin->givePermissionTo('Admin Dashboard');

    $jefe = Role::findByName('Head of Department');
    $jefe->givePermissionTo('Dashboard');
    $jefe->givePermissionTo('Home');
    $jefe->givePermissionTo('Collaborators');
    $jefe->givePermissionTo('Shared files');
    $jefe->givePermissionTo('Users management');

    $analista = Role::findByName('Analyst');
    $analista->givePermissionTo('Dashboard');
  }
}
