<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GrupoPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION1:   Crear permisos de un recurso
    Permission::create(['name' => 'grupo.show']);
    Permission::create(['name' => 'grupo.update']);
    Permission::create(['name' => 'grupo.create']);
    Permission::create(['name' => 'grupo.delete']);

    $student = Role::where('name', 'Alumno')->first();
    $student->givePermissionTo(['grupo.show']);

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo(Permission::all());

    $ControlEscolar = Role::where('name','Control Escolar')->first();
    $ControlEscolar->givePermissionTo('grupo.show');
    $ControlEscolar->givePermissionTo('grupo.update');
    $ControlEscolar->givePermissionTo('grupo.create');
    $ControlEscolar->givePermissionTo('grupo.delete');

    $DepartamentoDocentes = Role::where('name','=','Departamento de docentes')->first();
    $DepartamentoDocentes->givePermissionTo('grupo.show');
    $DepartamentoDocentes->givePermissionTo('grupo.update');
    $DepartamentoDocentes->givePermissionTo('grupo.create');
    $DepartamentoDocentes->givePermissionTo('grupo.delete');

    $Docente = Role::where('name','=','Docente')->first();
    $Docente->givePermissionTo('grupo.show');

    $Prefecto = Role::where('name','Prefecto')->first();
    $Prefecto->givePermissionTo('grupo.show');

    $PadreFamilia = Role::where('name','Padre de familia')->first();
    $PadreFamilia->givePermissionTo('grupo.show');

  }
}
