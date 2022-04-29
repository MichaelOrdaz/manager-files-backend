<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DocumentPermissionsSeeder extends Seeder
{
    public function run()
    {
      Permission::create(['name' => 'document.show']);
      Permission::create(['name' => 'document.create']);
      Permission::create(['name' => 'document.update']);
      Permission::create(['name' => 'document.delete']);

      $admin = Role::findByName('Admin');
      $admin->givePermissionTo('document.show');
      $admin->givePermissionTo('document.create');
      $admin->givePermissionTo('document.update');
      $admin->givePermissionTo('document.delete');

      $head = Role::findByName('Head of Department');
      $head->givePermissionTo([
        'document.show',
        'document.create',
        'document.update',
        'document.delete'
      ]);

      $analyst = Role::findByName('Analyst');
      $analyst->givePermissionTo([
        'document.show',
      ]);

      // los permisos del analista van a ser otorgados por usuario y no por rol, por default los analysta pueden ver sus documentos
      // Ejemplo:
      // al usuario N con rol analyst, sus permisos de documento van a ser otorgados por el head of department
      // agregar permisos
      // User::find(N)->givePermissionTo([
      //   'document.create',
      //   'document.update',
      //   'document.delete'
      // ]);
      // revocar permisos
      // User::find(N)->revokePermissionTo([
      //   'document.create',
      //   'document.update',
      //   'document.delete'
      // ]);

    }
}
