<?php

namespace Database\Seeders\Permissions;

use App\Helpers\Dixa;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SharedPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      //permisos que sirven para los documentos compartidos
      Permission::create(['name' => Dixa::PERMISSION_TO_READ_SHARED_DOCUMENT]);
      Permission::create(['name' => Dixa::PERMISSION_TO_WRITE_SHARED_DOCUMENT]);
      //permisos para administrar los permisos de los documentos compartidos
      Permission::create(['name' => 'share.permissions.show']);//permiso para visualizar los permisos de arriba
      Permission::create(['name' => 'share.permissions.create']);//permiso para asignar los permisos de arriba
      Permission::create(['name' => 'share.permissions.update']);//permiso para actualizar los permisos de arriba
      Permission::create(['name' => 'share.permissions.delete']);//permiso para borrar los permisos de arriba

      $admin = Role::findByName('Admin');
      $admin->givePermissionTo('share.permissions.show');
      $admin->givePermissionTo('share.permissions.create');
      $admin->givePermissionTo('share.permissions.update');
      $admin->givePermissionTo('share.permissions.delete');
      
      $jefe = Role::findByName('Head of Department');
      $jefe->givePermissionTo('share.permissions.show');
      $jefe->givePermissionTo('share.permissions.create');
      $jefe->givePermissionTo('share.permissions.update');
      $jefe->givePermissionTo('share.permissions.delete');

    }
}
