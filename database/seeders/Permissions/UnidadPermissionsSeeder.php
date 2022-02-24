<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UnidadPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      // SECTION1:   Crear permisos de un recurso
      Permission::create(['name' => 'unidad.show']);
      Permission::create(['name' => 'unidad.update']);
      Permission::create(['name' => 'unidad.create']);
      Permission::create(['name' => 'unidad.delete']);

      //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
      $student = Role::where('name', 'Alumno')->first();
      $student->givePermissionTo('unidad.show');

      $admin = Role::where('name', 'Admin')->first();
      $admin->givePermissionTo(Permission::all());

      $ControlEscolar = Role::where('name','Control Escolar')->first();
      $ControlEscolar->givePermissionTo('unidad.show');
      $ControlEscolar->givePermissionTo('unidad.update');
      $ControlEscolar->givePermissionTo('unidad.create');
      $ControlEscolar->givePermissionTo('unidad.delete');

      $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
      $DepartamentoDocentes->givePermissionTo('unidad.show');
      $DepartamentoDocentes->givePermissionTo('unidad.update');
      $DepartamentoDocentes->givePermissionTo('unidad.create');
      $DepartamentoDocentes->givePermissionTo('unidad.delete');

      $Docente = Role::where('name','Docente')->first();
      $Docente->givePermissionTo('unidad.show');

      $Prefecto = Role::where('name','Prefecto')->first();
      $Prefecto->givePermissionTo('unidad.show');

      $PadreFamilia = Role::where('name','Padre de familia')->first();
      $PadreFamilia->givePermissionTo('unidad.show');
    }
}
