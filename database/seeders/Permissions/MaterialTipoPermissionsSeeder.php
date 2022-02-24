<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MaterialTipoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'materialTipo.show']);
        Permission::create(['name' => 'materialTipo.update']);
        Permission::create(['name' => 'materialTipo.create']);
        Permission::create(['name' => 'materialTipo.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('materialTipo.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo('materialTipo.show');
        $admin->givePermissionTo('materialTipo.update');
        $admin->givePermissionTo('materialTipo.create');
        $admin->givePermissionTo('materialTipo.delete');

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('materialTipo.show');
        $ControlEscolar->givePermissionTo('materialTipo.update');
        $ControlEscolar->givePermissionTo('materialTipo.create');
        $ControlEscolar->givePermissionTo('materialTipo.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('materialTipo.show');
        $DepartamentoDocentes->givePermissionTo('materialTipo.update');
        $DepartamentoDocentes->givePermissionTo('materialTipo.create');
        $DepartamentoDocentes->givePermissionTo('materialTipo.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('materialTipo.show');

        $Prefecto = Role::where('name','Prefecto')->first();
        $Prefecto->givePermissionTo('materialTipo.show');

        $PadreFamilia = Role::where('name','Padre de familia')->first();
        $PadreFamilia->givePermissionTo('materialTipo.show');

    }
}
