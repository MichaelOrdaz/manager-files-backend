<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AlumnoGrupoPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION1:   Crear permisos de un recurso
    Permission::create(['name' => 'alumnoGrupo.show']);
    Permission::create(['name' => 'alumnoGrupo.update']);
    Permission::create(['name' => 'alumnoGrupo.create']);
    Permission::create(['name' => 'alumnoGrupo.delete']);

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo(Permission::all());

    $ControlEscolar = Role::where('name','Control Escolar')->first();
    $ControlEscolar->givePermissionTo('alumnoGrupo.show');

    $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
    $DepartamentoDocentes->givePermissionTo('alumnoGrupo.show');

    $student = Role::where('name', 'Alumno')->first();
    $student->givePermissionTo('alumnoGrupo.show');

    $Docente = Role::where('name','Docente')->first();
    $Docente->givePermissionTo('alumnoGrupo.show');
    $Docente->givePermissionTo('alumnoGrupo.update');

  }
}
