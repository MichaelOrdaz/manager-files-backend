<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TemasPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'tema.show']);
        Permission::create(['name' => 'tema.update']);
        Permission::create(['name' => 'tema.create']);
        Permission::create(['name' => 'tema.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('tema.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('tema.show');
        $admin->givePermissionTo('tema.update');
        $admin->givePermissionTo('tema.create');
        $admin->givePermissionTo('tema.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('tema.show');
        $ControlEscolar->givePermissionTo('tema.update');
        $ControlEscolar->givePermissionTo('tema.create');
        $ControlEscolar->givePermissionTo('tema.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('tema.show');
        $DepartamentoDocentes->givePermissionTo('tema.update');
        $DepartamentoDocentes->givePermissionTo('tema.create');
        $DepartamentoDocentes->givePermissionTo('tema.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('tema.show');
        $Docente->givePermissionTo('tema.update');
        $Docente->givePermissionTo('tema.create');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('tema.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('tema.show');
    }
}
