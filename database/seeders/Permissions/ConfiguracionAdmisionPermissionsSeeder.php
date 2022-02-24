<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ConfiguracionAdmisionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'configuracionAdmision.show']);
        Permission::create(['name' => 'configuracionAdmision.update']);
        Permission::create(['name' => 'configuracionAdmision.create']);
        Permission::create(['name' => 'configuracionAdmision.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('configuracionAdmision.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('configuracionAdmision.show');
        $ControlEscolar->givePermissionTo('configuracionAdmision.update');
        $ControlEscolar->givePermissionTo('configuracionAdmision.create');
        $ControlEscolar->givePermissionTo('configuracionAdmision.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('configuracionAdmision.show');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('configuracionAdmision.show');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('configuracionAdmision.show');

        $aspirante = Role::where('name','Aspirante a ingreso')->first();
        $aspirante->givePermissionTo('configuracionAdmision.show');

    }
}
