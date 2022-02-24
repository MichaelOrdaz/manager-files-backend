<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExamenesCalificacionesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'examenCalificacion.show']);
        Permission::create(['name' => 'examenCalificacion.update']);
        Permission::create(['name' => 'examenCalificacion.create']);
        Permission::create(['name' => 'examenCalificacion.delete']);

        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('examenCalificacion.show');
        $student->givePermissionTo('examenCalificacion.update');
        $student->givePermissionTo('examenCalificacion.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('examenCalificacion.show');
        $ControlEscolar->givePermissionTo('examenCalificacion.update');
        $ControlEscolar->givePermissionTo('examenCalificacion.create');
        $ControlEscolar->givePermissionTo('examenCalificacion.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('examenCalificacion.show');
        $DepartamentoDocentes->givePermissionTo('examenCalificacion.update');
        $DepartamentoDocentes->givePermissionTo('examenCalificacion.create');
        $DepartamentoDocentes->givePermissionTo('examenCalificacion.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('examenCalificacion.show');
        $Docente->givePermissionTo('examenCalificacion.update');
        $Docente->givePermissionTo('examenCalificacion.create');
        $Docente->givePermissionTo('examenCalificacion.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('examenCalificacion.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('examenCalificacion.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('examenCalificacion.show');
    }
}
