<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeparmentPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      Permission::create(['name' => 'department.show']);

      $admin = Role::findByName('Admin');
      $admin->givePermissionTo('department.show');

      $head = Role::findByName('Head of Department');
      $head->givePermissionTo('department.show');

      $analyst = Role::findByName('Analyst');
      $analyst->givePermissionTo('department.show');
    }
}
