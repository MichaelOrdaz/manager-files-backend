<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      Permission::create(['name' => 'role.show']);

      $admin = Role::findByName('Admin');
      $admin->givePermissionTo('role.show');

      $head = Role::findByName('Head of Department');
      $head->givePermissionTo('role.show');

      $analyst = Role::findByName('Analyst');
      $analyst->givePermissionTo('role.show');
    }
}
