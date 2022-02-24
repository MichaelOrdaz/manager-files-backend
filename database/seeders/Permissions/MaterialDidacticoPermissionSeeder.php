<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MaterialDidacticoPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'materialDidactico.show']);
        Permission::create(['name' => 'materialDidactico.update']);
        Permission::create(['name' => 'materialDidactico.create']);
        Permission::create(['name' => 'materialDidactico.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('materialDidactico.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('materialDidactico.show');
        $ControlEscolar->givePermissionTo('materialDidactico.update');
        $ControlEscolar->givePermissionTo('materialDidactico.create');
        $ControlEscolar->givePermissionTo('materialDidactico.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('materialDidactico.show');
        $DepartamentoDocentes->givePermissionTo('materialDidactico.update');
        $DepartamentoDocentes->givePermissionTo('materialDidactico.create');
        $DepartamentoDocentes->givePermissionTo('materialDidactico.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('materialDidactico.show');
        $Docente->givePermissionTo('materialDidactico.update');
        $Docente->givePermissionTo('materialDidactico.create');
        $Docente->givePermissionTo('materialDidactico.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('materialDidactico.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('materialDidactico.show');
    }
}
