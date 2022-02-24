<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ActividadPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'actividad.show']);
        Permission::create(['name' => 'actividad.update']);
        Permission::create(['name' => 'actividad.create']);
        Permission::create(['name' => 'actividad.delete']);

        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('actividad.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('actividad.show');
        $admin->givePermissionTo('actividad.update');
        $admin->givePermissionTo('actividad.create');
        $admin->givePermissionTo('actividad.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('actividad.show');
        $ControlEscolar->givePermissionTo('actividad.update');
        $ControlEscolar->givePermissionTo('actividad.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('actividad.show');
        $DepartamentoDocentes->givePermissionTo('actividad.update');
        $DepartamentoDocentes->givePermissionTo('actividad.create');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('actividad.show');
        $Docente->givePermissionTo('actividad.update');
        $Docente->givePermissionTo('actividad.create');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('actividad.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('actividad.show');

    }
}
