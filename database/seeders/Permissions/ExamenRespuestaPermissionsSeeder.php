<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExamenRespuestaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'examenRespuesta.show']);
        Permission::create(['name' => 'examenRespuesta.update']);
        Permission::create(['name' => 'examenRespuesta.create']);
        Permission::create(['name' => 'examenRespuesta.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('examenRespuesta.show');
        $student->givePermissionTo('examenRespuesta.update');
        $student->givePermissionTo('examenRespuesta.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('examenRespuesta.show');
        $ControlEscolar->givePermissionTo('examenRespuesta.update');
        $ControlEscolar->givePermissionTo('examenRespuesta.create');
        $ControlEscolar->givePermissionTo('examenRespuesta.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('examenRespuesta.show');
        $DepartamentoDocentes->givePermissionTo('examenRespuesta.update');
        $DepartamentoDocentes->givePermissionTo('examenRespuesta.create');
        $DepartamentoDocentes->givePermissionTo('examenRespuesta.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('examenRespuesta.show');
        $Docente->givePermissionTo('examenRespuesta.update');
        $Docente->givePermissionTo('examenRespuesta.create');
        $Docente->givePermissionTo('examenRespuesta.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('examenRespuesta.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('examenRespuesta.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('examenRespuesta.show');
        $AspiranteIngreso->givePermissionTo('examenRespuesta.create');
        $AspiranteIngreso->givePermissionTo('examenRespuesta.update');
    }
}
