<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatosFamiliaresPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'datosFamiliares.show']);
        Permission::create(['name' => 'datosFamiliares.update']);
        Permission::create(['name' => 'datosFamiliares.create']);
        Permission::create(['name' => 'datosFamiliares.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('datosFamiliares.show');
        $student->givePermissionTo('datosFamiliares.update');
        $student->givePermissionTo('datosFamiliares.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('datosFamiliares.show');
        $ControlEscolar->givePermissionTo('datosFamiliares.update');
        $ControlEscolar->givePermissionTo('datosFamiliares.create');
        $ControlEscolar->givePermissionTo('datosFamiliares.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('datosFamiliares.show');


        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('datosFamiliares.show');
        $Docente->givePermissionTo('datosFamiliares.create');
        $Docente->givePermissionTo('datosFamiliares.update');
        $Docente->givePermissionTo('datosFamiliares.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('datosFamiliares.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('datosFamiliares.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('datosFamiliares.show');
        $AspiranteIngreso->givePermissionTo('datosFamiliares.update');
        $AspiranteIngreso->givePermissionTo('datosFamiliares.create');

    }
}
