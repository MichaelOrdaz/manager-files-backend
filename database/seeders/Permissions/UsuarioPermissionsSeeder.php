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
      // SECTION1:   Crear permisos de un recurso
      Permission::create(['name' => 'usuario.show']);
      Permission::create(['name' => 'usuario.update']);
      Permission::create(['name' => 'usuario.create']);
      Permission::create(['name' => 'usuario.delete']);
      Permission::create(['name' => 'usuario.import']);
      Permission::create(['name' => 'usuario.export']);
      Permission::create(['name' => 'usuario.aplicar.baja']);
      Permission::create(['name' => 'usuario.revocar.baja']);

      //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
      $student = Role::where('name', 'Alumno')->first();
      $student->givePermissionTo(['usuario.show']);

      $admin = Role::where('name', 'Admin')->first();
      $admin->givePermissionTo(Permission::all());

      $ControlEscolar = Role::where('name','Control Escolar')->first();
      $ControlEscolar->givePermissionTo('usuario.show');
      $ControlEscolar->givePermissionTo('usuario.update');
      $ControlEscolar->givePermissionTo('usuario.create');
      $ControlEscolar->givePermissionTo('usuario.delete');
      $ControlEscolar->givePermissionTo('usuario.import');
      $ControlEscolar->givePermissionTo('usuario.export');
      $ControlEscolar->givePermissionTo('usuario.aplicar.baja');
      $ControlEscolar->givePermissionTo('usuario.revocar.baja');

      $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
      $DepartamentoDocentes->givePermissionTo('usuario.show');
      $DepartamentoDocentes->givePermissionTo('usuario.update');
      $DepartamentoDocentes->givePermissionTo('usuario.create');
      $DepartamentoDocentes->givePermissionTo('usuario.delete');
      $DepartamentoDocentes->givePermissionTo('usuario.import');
      $DepartamentoDocentes->givePermissionTo('usuario.export');
      $DepartamentoDocentes->givePermissionTo('usuario.aplicar.baja');
      $DepartamentoDocentes->givePermissionTo('usuario.revocar.baja');

      $Docente = Role::where('name','Docente')->first();
      $Docente->givePermissionTo('usuario.show');
      $Docente->givePermissionTo('usuario.update');
      $Docente->givePermissionTo('usuario.create');

      $Prefecto = Role::where('name','Prefecto')->first();
      $Prefecto->givePermissionTo('usuario.show');

      $padre = Role::where('name','Padre de familia')->first();
      $padre->givePermissionTo('usuario.show');

    }
}
