<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TareaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      // SECTION1:   Crear permisos de un recurso
      Permission::create(['name' => 'tarea.show']);
      Permission::create(['name' => 'tarea.update']);
      Permission::create(['name' => 'tarea.create']);
      Permission::create(['name' => 'tarea.delete']);
      Permission::create(['name' => 'tarea.copy']);

      //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
      $student = Role::where('name', 'Alumno')->first();
      $student->givePermissionTo(['tarea.show']);

      $admin = Role::where('name', 'Admin')->first();
      $admin->givePermissionTo(Permission::all());

      $ControlEscolar = Role::where('name','Control Escolar')->first();
      $ControlEscolar->givePermissionTo('tarea.show');
      $ControlEscolar->givePermissionTo('tarea.update');
      $ControlEscolar->givePermissionTo('tarea.create');
      $ControlEscolar->givePermissionTo('tarea.copy');
      $ControlEscolar->givePermissionTo('tarea.delete');

      $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
      $DepartamentoDocentes->givePermissionTo('tarea.show');
      $DepartamentoDocentes->givePermissionTo('tarea.update');
      $DepartamentoDocentes->givePermissionTo('tarea.create');
      $DepartamentoDocentes->givePermissionTo('tarea.copy');
      $DepartamentoDocentes->givePermissionTo('tarea.delete');

      $Docente = Role::where('name','Docente')->first();
      $Docente->givePermissionTo('tarea.show');
      $Docente->givePermissionTo('tarea.update');
      $Docente->givePermissionTo('tarea.create');
      $Docente->givePermissionTo('tarea.copy');
      $Docente->givePermissionTo('tarea.delete');

      $Prefecto = Role::where('name','Prefecto')->first();
      $Prefecto->givePermissionTo('tarea.show');

      $PadreFamilia = Role::where('name','Padre de familia')->first();
      $PadreFamilia->givePermissionTo('tarea.show');
    }
}
