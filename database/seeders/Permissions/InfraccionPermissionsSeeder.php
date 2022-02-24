<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class InfraccionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'infraccion.show']);
        Permission::create(['name' => 'infraccion.update']);
        Permission::create(['name' => 'infraccion.create']);
        Permission::create(['name' => 'infraccion.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('infraccion.show');
        $student->givePermissionTo('infraccion.update');
        $student->givePermissionTo('infraccion.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('infraccion.show');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('infraccion.show');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('infraccion.show');
        $Docente->givePermissionTo('infraccion.create');
        $Docente->givePermissionTo('infraccion.update');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('infraccion.show');
        $Prefecto->givePermissionTo('infraccion.create');
        $Prefecto->givePermissionTo('infraccion.update');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('infraccion.show');

    }
}
