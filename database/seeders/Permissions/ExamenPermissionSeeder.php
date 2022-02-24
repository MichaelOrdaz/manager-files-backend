<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExamenPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'examen.show']);
        Permission::create(['name' => 'examen.update']);
        Permission::create(['name' => 'examen.create']);
        Permission::create(['name' => 'examen.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('examen.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('examen.show');
        $ControlEscolar->givePermissionTo('examen.update');
        $ControlEscolar->givePermissionTo('examen.create');
        $ControlEscolar->givePermissionTo('examen.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('examen.show');
        $DepartamentoDocentes->givePermissionTo('examen.update');
        $DepartamentoDocentes->givePermissionTo('examen.create');
        $DepartamentoDocentes->givePermissionTo('examen.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('examen.show');
        $Docente->givePermissionTo('examen.update');
        $Docente->givePermissionTo('examen.create');
        $Docente->givePermissionTo('examen.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('examen.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('examen.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('examen.show');
    }
}
