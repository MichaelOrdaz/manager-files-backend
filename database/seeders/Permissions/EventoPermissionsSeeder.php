<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EventoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'evento.show']);
        Permission::create(['name' => 'evento.update']);
        Permission::create(['name' => 'evento.create']);
        Permission::create(['name' => 'evento.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('evento.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('evento.show');
        $admin->givePermissionTo('evento.update');
        $admin->givePermissionTo('evento.create');
        $admin->givePermissionTo('evento.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('evento.show');
        $ControlEscolar->givePermissionTo('evento.update');
        $ControlEscolar->givePermissionTo('evento.create');
        $ControlEscolar->givePermissionTo('evento.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('evento.show');
        $DepartamentoDocentes->givePermissionTo('evento.update');
        $DepartamentoDocentes->givePermissionTo('evento.create');
        $DepartamentoDocentes->givePermissionTo('evento.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('evento.show');
        $Docente->givePermissionTo('evento.update');
        $Docente->givePermissionTo('evento.create');
        $Docente->givePermissionTo('evento.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('evento.show');
        $Prefecto->givePermissionTo('evento.update');
        $Prefecto->givePermissionTo('evento.create');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('evento.show');

    }
}
