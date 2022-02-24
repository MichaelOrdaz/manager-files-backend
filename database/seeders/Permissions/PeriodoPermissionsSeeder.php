<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PeriodoPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION1:   Crear permisos de un recurso
    Permission::create(['name' => 'periodo.show']);
    Permission::create(['name' => 'periodo.update']);
    Permission::create(['name' => 'periodo.create']);
    Permission::create(['name' => 'periodo.delete']);

    //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
    $student = Role::where('name', 'Alumno')->first();
    $student->givePermissionTo(['periodo.show']);

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo(Permission::all());

    $ControlEscolar = Role::where('name','Control Escolar')->first();
    $ControlEscolar->givePermissionTo('periodo.show');
    $ControlEscolar->givePermissionTo('periodo.update');
    $ControlEscolar->givePermissionTo('periodo.create');
    $ControlEscolar->givePermissionTo('periodo.delete');

    $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
    $DepartamentoDocentes->givePermissionTo('periodo.show');
    $DepartamentoDocentes->givePermissionTo('periodo.update');
    $DepartamentoDocentes->givePermissionTo('periodo.create');
    $DepartamentoDocentes->givePermissionTo('periodo.delete');

    $Docente = Role::where('name','Docente')->first();
    $Docente->givePermissionTo('periodo.show');

    $Prefecto = Role::where('name','Prefecto')->first();
    $Prefecto->givePermissionTo('periodo.show');

    $PadreFamilia = Role::where('name','Padre de familia')->first();
    $PadreFamilia->givePermissionTo('periodo.show');

  }
}
