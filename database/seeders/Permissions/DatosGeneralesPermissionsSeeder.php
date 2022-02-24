<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatosGeneralesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'datosGenerales.show']);
        Permission::create(['name' => 'datosGenerales.update']);
        Permission::create(['name' => 'datosGenerales.create']);
        Permission::create(['name' => 'datosGenerales.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('datosGenerales.show');
        $student->givePermissionTo('datosGenerales.update');
        $student->givePermissionTo('datosGenerales.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('datosGenerales.show');
        $ControlEscolar->givePermissionTo('datosGenerales.update');
        $ControlEscolar->givePermissionTo('datosGenerales.create');
        $ControlEscolar->givePermissionTo('datosGenerales.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('datosGenerales.show');
        $DepartamentoDocentes->givePermissionTo('datosGenerales.update');
        $DepartamentoDocentes->givePermissionTo('datosGenerales.create');
        $DepartamentoDocentes->givePermissionTo('datosGenerales.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('datosGenerales.show');
        $Docente->givePermissionTo('datosGenerales.update');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('datosGenerales.show');
        $Prefecto->givePermissionTo('datosGenerales.update');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('datosGenerales.show');
        $PadreFamilia->givePermissionTo('datosGenerales.update');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('datosGenerales.show');
        $AspiranteIngreso->givePermissionTo('datosGenerales.update');
        $AspiranteIngreso->givePermissionTo('datosGenerales.create');
    }
}
