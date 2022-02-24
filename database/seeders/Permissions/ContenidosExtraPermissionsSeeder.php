<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ContenidosExtraPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'contenidosExtra.show']);
        Permission::create(['name' => 'contenidosExtra.update']);
        Permission::create(['name' => 'contenidosExtra.create']);
        Permission::create(['name' => 'contenidosExtra.delete']);

        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('contenidosExtra.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('contenidosExtra.show');
        $ControlEscolar->givePermissionTo('contenidosExtra.update');
        $ControlEscolar->givePermissionTo('contenidosExtra.create');
        $ControlEscolar->givePermissionTo('contenidosExtra.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('contenidosExtra.show');
        $DepartamentoDocentes->givePermissionTo('contenidosExtra.update');
        $DepartamentoDocentes->givePermissionTo('contenidosExtra.create');
        $DepartamentoDocentes->givePermissionTo('contenidosExtra.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('contenidosExtra.show');
        $Docente->givePermissionTo('contenidosExtra.update');
        $Docente->givePermissionTo('contenidosExtra.create');
        $Docente->givePermissionTo('contenidosExtra.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('contenidosExtra.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('contenidosExtra.show');

    }
}
