<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComponentePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'componente.show']);
        Permission::create(['name' => 'componente.update']);
        Permission::create(['name' => 'componente.create']);
        Permission::create(['name' => 'componente.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('componente.show');
        $admin->givePermissionTo('componente.update');
        $admin->givePermissionTo('componente.create');
        $admin->givePermissionTo('componente.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('componente.show');
        $ControlEscolar->givePermissionTo('componente.update');
        $ControlEscolar->givePermissionTo('componente.create');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('componente.show');
        $DepartamentoDocentes->givePermissionTo('componente.update');
        $DepartamentoDocentes->givePermissionTo('componente.create');
        $DepartamentoDocentes->givePermissionTo('componente.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('componente.show');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('componente.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('componente.show');

    }
}
