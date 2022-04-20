<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsuarioPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      Permission::create(['name' => 'user.show']);
      Permission::create(['name' => 'user.update']);
      Permission::create(['name' => 'user.create']);
      Permission::create(['name' => 'user.delete']);

      $admin = Role::findByName('Admin');
      $admin->givePermissionTo('user.show');
      $admin->givePermissionTo('user.update');
      $admin->givePermissionTo('user.create');
      $admin->givePermissionTo('user.delete');

      $jefe = Role::findByName('Head of Department');
      $jefe->givePermissionTo('user.show');
      $jefe->givePermissionTo('user.update');

      $analista = Role::findByName('Analyst');
      $analista->givePermissionTo('user.show');
    }
}
