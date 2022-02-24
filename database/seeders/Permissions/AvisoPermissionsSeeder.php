<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AvisoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'aviso.show']);
        Permission::create(['name' => 'aviso.update']);
        Permission::create(['name' => 'aviso.create']);
        Permission::create(['name' => 'aviso.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('aviso.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('aviso.show');
        $admin->givePermissionTo('aviso.update');
        $admin->givePermissionTo('aviso.create');
        $admin->givePermissionTo('aviso.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('aviso.show');
        $ControlEscolar->givePermissionTo('aviso.update');
        $ControlEscolar->givePermissionTo('aviso.create');
        $ControlEscolar->givePermissionTo('aviso.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('aviso.show');
        $DepartamentoDocentes->givePermissionTo('aviso.update');
        $DepartamentoDocentes->givePermissionTo('aviso.create');
        $DepartamentoDocentes->givePermissionTo('aviso.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('aviso.show');
        $Docente->givePermissionTo('aviso.update');
        $Docente->givePermissionTo('aviso.create');
        $Docente->givePermissionTo('aviso.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('aviso.show');
        $Prefecto->givePermissionTo('aviso.update');
        $Prefecto->givePermissionTo('aviso.create');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('aviso.show');

    }
}
