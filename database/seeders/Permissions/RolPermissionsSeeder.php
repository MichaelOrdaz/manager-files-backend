<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolPermissionsSeeder extends Seeder
{
  public function run ()
  {

    define('PERMISO','consultar roles');
    // SECTION1:   Crear permisos de un recurso
    Permission::create(['name' => PERMISO ]);

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo(Permission::all());

    $ControlEscolar = Role::where('name','Control Escolar')->first();
    $ControlEscolar->givePermissionTo(PERMISO);

    $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
    $DepartamentoDocentes->givePermissionTo(PERMISO);

  }
}
