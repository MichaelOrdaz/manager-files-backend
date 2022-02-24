<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TareaEnviadaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      // SECTION1:   Crear permisos de un recurso
      Permission::create(['name' => 'tareaEnviada.show']);
      Permission::create(['name' => 'tareaEnviada.update']);
      Permission::create(['name' => 'tareaEnviada.create']);
      Permission::create(['name' => 'tareaEnviada.delete']);

      //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
      $student = Role::where('name', 'Alumno')->first();
      $student->givePermissionTo('tareaEnviada.create');
      $student->givePermissionTo('tareaEnviada.update');
      $student->givePermissionTo(['tareaEnviada.show']);

      $admin = Role::where('name', 'Admin')->first();
      $admin->givePermissionTo('tareaEnviada.show');
      $admin->givePermissionTo('tareaEnviada.update');
      $admin->givePermissionTo('tareaEnviada.create');
      $admin->givePermissionTo('tareaEnviada.delete');

      $ControlEscolar = Role::where('name','Control Escolar')->first();
      $ControlEscolar->givePermissionTo('tareaEnviada.show');
      $ControlEscolar->givePermissionTo('tareaEnviada.update');
      $ControlEscolar->givePermissionTo('tareaEnviada.create');

      $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
      $DepartamentoDocentes->givePermissionTo('tareaEnviada.show');
      $DepartamentoDocentes->givePermissionTo('tareaEnviada.update');
      $DepartamentoDocentes->givePermissionTo('tareaEnviada.create');

      $Docente = Role::where('name','Docente')->first();
      $Docente->givePermissionTo('tareaEnviada.show');
      $Docente->givePermissionTo('tareaEnviada.update');
      $Docente->givePermissionTo('tareaEnviada.create');

      $Prefecto = Role::where('name','Prefecto')->first();
      $Prefecto->givePermissionTo('tareaEnviada.show');

      $PadreFamilia = Role::where('name','Padre de familia')->first();
      $PadreFamilia->givePermissionTo('tareaEnviada.show');

    }
}
