<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EncuestaPreguntaPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION1:   Crear permisos de un recurso
    Permission::create(['name' => 'encuestaPregunta.show']);
    Permission::create(['name' => 'encuestaPregunta.update']);
    Permission::create(['name' => 'encuestaPregunta.create']);
    Permission::create(['name' => 'encuestaPregunta.delete']);

    //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
    $student = Role::where('name', 'Alumno')->first();
    $student->givePermissionTo(['encuestaPregunta.show']);

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo(Permission::all());

    $ControlEscolar = Role::where('name','Control Escolar')->first();
    $ControlEscolar->givePermissionTo('encuestaPregunta.show');
    $ControlEscolar->givePermissionTo('encuestaPregunta.update');
    $ControlEscolar->givePermissionTo('encuestaPregunta.create');
    $ControlEscolar->givePermissionTo('encuestaPregunta.delete');

    $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
    $DepartamentoDocentes->givePermissionTo('encuestaPregunta.show');
    $DepartamentoDocentes->givePermissionTo('encuestaPregunta.update');
    $DepartamentoDocentes->givePermissionTo('encuestaPregunta.create');
    $DepartamentoDocentes->givePermissionTo('encuestaPregunta.delete');

    $Docente = Role::where('name','Docente')->first();
    $Docente->givePermissionTo('encuestaPregunta.show');
    $Docente->givePermissionTo('encuestaPregunta.update');
    $Docente->givePermissionTo('encuestaPregunta.create');
    $Docente->givePermissionTo('encuestaPregunta.delete');

    $Prefecto = Role::where('name','Prefecto')->first();
    $Prefecto->givePermissionTo('encuestaPregunta.show');
    $Prefecto->givePermissionTo('encuestaPregunta.update');
    $Prefecto->givePermissionTo('encuestaPregunta.create');

    $PadreFamilia = Role::where('name','Padre de familia')->first();
    $PadreFamilia->givePermissionTo('encuestaPregunta.show');

    $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
    $AspiranteIngreso->givePermissionTo('encuestaPregunta.show');
  }
}
