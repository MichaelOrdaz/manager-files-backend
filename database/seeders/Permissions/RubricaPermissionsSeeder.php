<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RubricaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'rubrica.show']);
        Permission::create(['name' => 'rubrica.update']);
        Permission::create(['name' => 'rubrica.create']);
        Permission::create(['name' => 'rubrica.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('rubrica.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('rubrica.show');
        $admin->givePermissionTo('rubrica.update');
        $admin->givePermissionTo('rubrica.create');
        $admin->givePermissionTo('rubrica.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('rubrica.show');
        $ControlEscolar->givePermissionTo('rubrica.update');
        $ControlEscolar->givePermissionTo('rubrica.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('rubrica.show');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('rubrica.show');
        $Docente->givePermissionTo('rubrica.update');
        $Docente->givePermissionTo('rubrica.create');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('rubrica.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('rubrica.show');

    }
}
