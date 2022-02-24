<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExamenesBancoPreguntasPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'examenesBancoPreguntas.show']);
        Permission::create(['name' => 'examenesBancoPreguntas.update']);
        Permission::create(['name' => 'examenesBancoPreguntas.create']);
        Permission::create(['name' => 'examenesBancoPreguntas.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('examenesBancoPreguntas.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('examenesBancoPreguntas.show');
        $admin->givePermissionTo('examenesBancoPreguntas.update');
        $admin->givePermissionTo('examenesBancoPreguntas.create');
        $admin->givePermissionTo('examenesBancoPreguntas.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('examenesBancoPreguntas.show');
        $ControlEscolar->givePermissionTo('examenesBancoPreguntas.update');
        $ControlEscolar->givePermissionTo('examenesBancoPreguntas.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('examenesBancoPreguntas.show');
        $DepartamentoDocentes->givePermissionTo('examenesBancoPreguntas.update');
        $DepartamentoDocentes->givePermissionTo('examenesBancoPreguntas.create');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('examenesBancoPreguntas.show');
        $Docente->givePermissionTo('examenesBancoPreguntas.update');
        $Docente->givePermissionTo('examenesBancoPreguntas.create');

    }
}
