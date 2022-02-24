<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EncuestaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'encuesta.show']);
        Permission::create(['name' => 'encuesta.update']);
        Permission::create(['name' => 'encuesta.create']);
        Permission::create(['name' => 'encuesta.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('encuesta.show');
        $student->givePermissionTo('encuesta.update');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('encuesta.show');
        $ControlEscolar->givePermissionTo('encuesta.update');
        $ControlEscolar->givePermissionTo('encuesta.create');
        $ControlEscolar->givePermissionTo('encuesta.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('encuesta.show');
        $DepartamentoDocentes->givePermissionTo('encuesta.update');
        $DepartamentoDocentes->givePermissionTo('encuesta.create');
        $DepartamentoDocentes->givePermissionTo('encuesta.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('encuesta.show');
        $Docente->givePermissionTo('encuesta.update');
        $Docente->givePermissionTo('encuesta.create');
        $Docente->givePermissionTo('encuesta.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('encuesta.show');
        $Prefecto->givePermissionTo('encuesta.update');
        $Prefecto->givePermissionTo('encuesta.create');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('encuesta.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('encuesta.show');
    }
}
