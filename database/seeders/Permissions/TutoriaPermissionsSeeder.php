<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TutoriaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'tutoria.show']);
        Permission::create(['name' => 'tutoria.update']);
        Permission::create(['name' => 'tutoria.create']);
        Permission::create(['name' => 'tutoria.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('tutoria.show');
        $student->givePermissionTo('tutoria.update');
        $student->givePermissionTo('tutoria.create');
        $student->givePermissionTo('tutoria.delete');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());


        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('tutoria.show');
        $ControlEscolar->givePermissionTo('tutoria.update');
        $ControlEscolar->givePermissionTo('tutoria.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('tutoria.show');
        $DepartamentoDocentes->givePermissionTo('tutoria.update');
        $DepartamentoDocentes->givePermissionTo('tutoria.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('tutoria.show');
        $Docente->givePermissionTo('tutoria.update');
        $Docente->givePermissionTo('tutoria.create');
        $Docente->givePermissionTo('tutoria.delete');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('tutoria.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('tutoria.show');

    }
}
