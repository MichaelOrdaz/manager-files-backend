<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EspecialidadPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'especialidad.show']);
        Permission::create(['name' => 'especialidad.update']);
        Permission::create(['name' => 'especialidad.create']);
        Permission::create(['name' => 'especialidad.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('especialidad.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('especialidad.show');
        $admin->givePermissionTo('especialidad.update');
        $admin->givePermissionTo('especialidad.create');
        $admin->givePermissionTo('especialidad.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('especialidad.show');
        $ControlEscolar->givePermissionTo('especialidad.update');
        $ControlEscolar->givePermissionTo('especialidad.create');
        $ControlEscolar->givePermissionTo('especialidad.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('especialidad.show');
        $DepartamentoDocentes->givePermissionTo('especialidad.update');
        $DepartamentoDocentes->givePermissionTo('especialidad.create');
        $DepartamentoDocentes->givePermissionTo('especialidad.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('especialidad.show');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('especialidad.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('especialidad.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('especialidad.show');

    }
}
