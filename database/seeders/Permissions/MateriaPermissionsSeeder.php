<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MateriaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'materia.show']);
        Permission::create(['name' => 'materia.update']);
        Permission::create(['name' => 'materia.create']);
        Permission::create(['name' => 'materia.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('materia.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('materia.show');
        $admin->givePermissionTo('materia.update');
        $admin->givePermissionTo('materia.create');
        $admin->givePermissionTo('materia.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('materia.show');
        $ControlEscolar->givePermissionTo('materia.update');
        $ControlEscolar->givePermissionTo('materia.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('materia.show');
        $DepartamentoDocentes->givePermissionTo('materia.update');
        $DepartamentoDocentes->givePermissionTo('materia.create');
        $DepartamentoDocentes->givePermissionTo('materia.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('materia.show');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('materia.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('materia.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('materia.show');

    }
}
