<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PreguntaTipoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'preguntaTipo.show']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $teacher = Role::where('name', 'Docente')->first();
        $teacher->givePermissionTo('preguntaTipo.show');

        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('preguntaTipo.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('preguntaTipo.show');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('preguntaTipo.show');


    }
}
