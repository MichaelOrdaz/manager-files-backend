<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ConferenciaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'conferencia.show']);
        Permission::create(['name' => 'conferencia.update']);
        Permission::create(['name' => 'conferencia.create']);
        Permission::create(['name' => 'conferencia.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('conferencia.show');
        $student->givePermissionTo('conferencia.update');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('conferencia.show');
        $ControlEscolar->givePermissionTo('conferencia.update');
        $ControlEscolar->givePermissionTo('conferencia.create');
        $ControlEscolar->givePermissionTo('conferencia.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('conferencia.show');
        $DepartamentoDocentes->givePermissionTo('conferencia.update');
        $DepartamentoDocentes->givePermissionTo('conferencia.create');
        $DepartamentoDocentes->givePermissionTo('conferencia.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('conferencia.show');
        $Docente->givePermissionTo('conferencia.update');
        $Docente->givePermissionTo('conferencia.create');
        $Docente->givePermissionTo('conferencia.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('conferencia.show');
        $Prefecto->givePermissionTo('conferencia.update');
        $Prefecto->givePermissionTo('conferencia.create');
        $Prefecto->givePermissionTo('conferencia.delete');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('conferencia.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('conferencia.show');
        $AspiranteIngreso->givePermissionTo('conferencia.update');

    }
}
