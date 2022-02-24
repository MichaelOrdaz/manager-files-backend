<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExamenPreguntaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'examenPregunta.show']);
        Permission::create(['name' => 'examenPregunta.update']);
        Permission::create(['name' => 'examenPregunta.create']);
        Permission::create(['name' => 'examenPregunta.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('examenPregunta.show');
        $student->givePermissionTo('examenPregunta.update');
        $student->givePermissionTo('examenPregunta.create');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('examenPregunta.show');
        $ControlEscolar->givePermissionTo('examenPregunta.update');
        $ControlEscolar->givePermissionTo('examenPregunta.create');
        $ControlEscolar->givePermissionTo('examenPregunta.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('examenPregunta.show');
        $DepartamentoDocentes->givePermissionTo('examenPregunta.update');
        $DepartamentoDocentes->givePermissionTo('examenPregunta.create');
        $DepartamentoDocentes->givePermissionTo('examenPregunta.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('examenPregunta.show');
        $Docente->givePermissionTo('examenPregunta.update');
        $Docente->givePermissionTo('examenPregunta.create');
        $Docente->givePermissionTo('examenPregunta.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('examenPregunta.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('examenPregunta.show');

        $AspiranteIngreso = Role::where('name','Aspirante a ingreso')->first();
        $AspiranteIngreso->givePermissionTo('examenPregunta.show');
    }
}
