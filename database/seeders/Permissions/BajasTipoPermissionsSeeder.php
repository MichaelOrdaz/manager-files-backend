<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BajasTipoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'bajaTipo.show']);
        Permission::create(['name' => 'bajaTipo.update']);
        Permission::create(['name' => 'bajaTipo.create']);
        Permission::create(['name' => 'bajaTipo.delete']);

        $admin = Role::findByName('Admin');
        $admin->givePermissionTo('bajaTipo.show');
        $admin->givePermissionTo('bajaTipo.update');
        $admin->givePermissionTo('bajaTipo.create');
        $admin->givePermissionTo('bajaTipo.delete');

        $ControlEscolar = Role::findByName('Control Escolar');
        $ControlEscolar->givePermissionTo('bajaTipo.show');
        $ControlEscolar->givePermissionTo('bajaTipo.update');
        $ControlEscolar->givePermissionTo('bajaTipo.create');
        $ControlEscolar->givePermissionTo('bajaTipo.delete');

        $DepartamentoDocentes = Role::findByName('Departamento de docentes');
        $DepartamentoDocentes->givePermissionTo('bajaTipo.show');
        $DepartamentoDocentes->givePermissionTo('bajaTipo.update');
        $DepartamentoDocentes->givePermissionTo('bajaTipo.create');
        $DepartamentoDocentes->givePermissionTo('bajaTipo.delete');
    }
}
