<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermisosPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION1:   Crear permisos de un recurso
    Permission::create(['name' => 'permiso.show']);
    Permission::create(['name' => 'permiso.update']);
    Permission::create(['name' => 'permiso.create']);
    Permission::create(['name' => 'permiso.delete']);

    //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo('permiso.show');
    $admin->givePermissionTo('permiso.update');

    $ControlEscolar = Role::where('name','Control Escolar')->first();
    $ControlEscolar->givePermissionTo('permiso.show');
    $ControlEscolar->givePermissionTo('permiso.update');

    $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
    $DepartamentoDocentes->givePermissionTo('permiso.show');
    $DepartamentoDocentes->givePermissionTo('permiso.update');

    $teacher = Role::where('name', 'Docente')->first();
    $teacher->givePermissionTo('permiso.show');
    $teacher->givePermissionTo('permiso.update');
  }
}
