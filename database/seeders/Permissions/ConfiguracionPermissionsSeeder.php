<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ConfiguracionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'configuracion.show']);
        Permission::create(['name' => 'configuracion.update']);
        Permission::create(['name' => 'configuracion.create']);
        Permission::create(['name' => 'configuracion.delete']);

        $controlEscolar = Role::findByName('Control Escolar');
        $controlEscolar->givePermissionTo('configuracion.show');
        $controlEscolar->givePermissionTo('configuracion.update');
        $controlEscolar->givePermissionTo('configuracion.create');
        $controlEscolar->givePermissionTo('configuracion.delete');

        $alumno = Role::findByName('Alumno');
        $alumno->givePermissionTo('configuracion.show');
        
        $ddd = Role::findByName('Departamento de docentes');
        $ddd->givePermissionTo('configuracion.show');

        $docente = Role::findByName('Docente');
        $docente->givePermissionTo('configuracion.show');

        $prefecto = Role::findByName('Prefecto');
        $prefecto->givePermissionTo('configuracion.show');

        $pdf = Role::findByName('Padre de familia');
        $pdf->givePermissionTo('configuracion.show');

        $aai = Role::findByName('Aspirante a ingreso');
        $aai->givePermissionTo('configuracion.show');

        $aai = Role::findByName('Aspirante a ingreso');
        $aai->givePermissionTo('configuracion.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

    }
}
