<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExamenTipoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'examenTipo.show']);
        Permission::create(['name' => 'examenTipo.update']);
        Permission::create(['name' => 'examenTipo.create']);
        Permission::create(['name' => 'examenTipo.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('examenTipo.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('examenTipo.show');
        $ControlEscolar->givePermissionTo('examenTipo.create');
        $ControlEscolar->givePermissionTo('examenTipo.update');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('examenTipo.show');
        $DepartamentoDocentes->givePermissionTo('examenTipo.create');
        $DepartamentoDocentes->givePermissionTo('examenTipo.update');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('examenTipo.show');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('examenTipo.show');


        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('examenTipo.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('examenTipo.show');

    }
}
