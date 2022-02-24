<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatosAcademicosPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'datosAcademicos.show']);
        Permission::create(['name' => 'datosAcademicos.update']);
        Permission::create(['name' => 'datosAcademicos.create']);
        Permission::create(['name' => 'datosAcademicos.delete']);

        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('datosAcademicos.show');
        $student->givePermissionTo('datosAcademicos.update');
        $student->givePermissionTo('datosAcademicos.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('datosAcademicos.show');
        $ControlEscolar->givePermissionTo('datosAcademicos.update');
        $ControlEscolar->givePermissionTo('datosAcademicos.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('datosAcademicos.show');


        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('datosAcademicos.show');
        $Docente->givePermissionTo('datosAcademicos.update');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('datosAcademicos.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('datosAcademicos.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('datosAcademicos.show');
        $AspiranteIngreso->givePermissionTo('datosAcademicos.update');
        $AspiranteIngreso->givePermissionTo('datosAcademicos.create');

    }
}
