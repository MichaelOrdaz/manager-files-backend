<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AsistenciaAlumnoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'asistenciaAlumno.show']);
        Permission::create(['name' => 'asistenciaAlumno.update']);
        Permission::create(['name' => 'asistenciaAlumno.create']);
        Permission::create(['name' => 'asistenciaAlumno.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('asistenciaAlumno.show');
        $student->givePermissionTo('asistenciaAlumno.update');
        $student->givePermissionTo('asistenciaAlumno.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('asistenciaAlumno.show');
        $ControlEscolar->givePermissionTo('asistenciaAlumno.update');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('asistenciaAlumno.show');
        $DepartamentoDocentes->givePermissionTo('asistenciaAlumno.update');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('asistenciaAlumno.show');
        $Docente->givePermissionTo('asistenciaAlumno.update');
        $Docente->givePermissionTo('asistenciaAlumno.create');
        $Docente->givePermissionTo('asistenciaAlumno.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('asistenciaAlumno.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('asistenciaAlumno.show');

    }
}
