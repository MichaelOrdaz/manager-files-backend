<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CalificacionParcialPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'calificacionParcial.show']);
        Permission::create(['name' => 'calificacionParcial.update']);
        Permission::create(['name' => 'calificacionParcial.create']);
        Permission::create(['name' => 'calificacionParcial.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('calificacionParcial.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('calificacionParcial.show');
        $ControlEscolar->givePermissionTo('calificacionParcial.update');
        $ControlEscolar->givePermissionTo('calificacionParcial.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('calificacionParcial.show');
        $DepartamentoDocentes->givePermissionTo('calificacionParcial.update');
        $DepartamentoDocentes->givePermissionTo('calificacionParcial.create');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('calificacionParcial.show');
        $Docente->givePermissionTo('calificacionParcial.update');
        $Docente->givePermissionTo('calificacionParcial.create');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('calificacionParcial.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('calificacionParcial.show');

    }
}
