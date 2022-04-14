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
      Permission::create(['name' => 'usuario.show']);
      Permission::create(['name' => 'usuario.update']);
      Permission::create(['name' => 'usuario.create']);
      Permission::create(['name' => 'usuario.delete']);

      $admin = Role::findByName('Administrador');
      $admin->givePermissionTo('usuario.show');
      $admin->givePermissionTo('usuario.update');
      $admin->givePermissionTo('usuario.create');
      $admin->givePermissionTo('usuario.delete');

      $jefe = Role::findByName('Jefe de departamento');
      $jefe->givePermissionTo('usuario.show');

      $analista = Role::findByName('Analista');
      $analista->givePermissionTo('usuario.show');
    }
}
