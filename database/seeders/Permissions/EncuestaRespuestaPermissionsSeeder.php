<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EncuestaRespuestaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'encuestaRespuesta.show']);
        Permission::create(['name' => 'encuestaRespuesta.update']);
        Permission::create(['name' => 'encuestaRespuesta.create']);
        Permission::create(['name' => 'encuestaRespuesta.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('encuestaRespuesta.show');
        $student->givePermissionTo('encuestaRespuesta.update');
        $student->givePermissionTo('encuestaRespuesta.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('encuestaRespuesta.show');
        $admin->givePermissionTo('encuestaRespuesta.update');
        $admin->givePermissionTo('encuestaRespuesta.create');
        $admin->givePermissionTo('encuestaRespuesta.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('encuestaRespuesta.show');
        $ControlEscolar->givePermissionTo('encuestaRespuesta.update');
        $ControlEscolar->givePermissionTo('encuestaRespuesta.create');
        $ControlEscolar->givePermissionTo('encuestaRespuesta.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('encuestaRespuesta.show');
        $DepartamentoDocentes->givePermissionTo('encuestaRespuesta.update');
        $DepartamentoDocentes->givePermissionTo('encuestaRespuesta.create');
        $DepartamentoDocentes->givePermissionTo('encuestaRespuesta.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('encuestaRespuesta.show');
        $Docente->givePermissionTo('encuestaRespuesta.update');
        $Docente->givePermissionTo('encuestaRespuesta.create');
        $Docente->givePermissionTo('encuestaRespuesta.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('encuestaRespuesta.show');
        $Prefecto->givePermissionTo('encuestaRespuesta.update');
        $Prefecto->givePermissionTo('encuestaRespuesta.create');
        $Prefecto->givePermissionTo('encuestaRespuesta.delete');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('encuestaRespuesta.show');
        $PadreFamilia->givePermissionTo('encuestaRespuesta.update');
        $PadreFamilia->givePermissionTo('encuestaRespuesta.create');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('encuestaRespuesta.show');
        $AspiranteIngreso->givePermissionTo('encuestaRespuesta.update');
        $AspiranteIngreso->givePermissionTo('encuestaRespuesta.create');

    }
}
