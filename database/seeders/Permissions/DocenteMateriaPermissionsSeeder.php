<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DocenteMateriaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SECTION1:   Crear permisos de un recurso
        Permission::create(['name' => 'docenteMateria.show']);
        Permission::create(['name' => 'docenteMateria.update']);
        Permission::create(['name' => 'docenteMateria.create']);
        Permission::create(['name' => 'docenteMateria.delete']);

        //SECTION2 Asignar permisos a un rol correspondiente y a un usuario directamente
        $student = Role::where('name', 'Alumno')->first();
        $student->givePermissionTo('docenteMateria.show');

        $admin = Role::where('name', 'Admin')->first();
        $admin->givePermissionTo(Permission::all());

        $ControlEscolar = Role::where('name','Control Escolar')->first();
        $ControlEscolar->givePermissionTo('docenteMateria.show');
        $ControlEscolar->givePermissionTo('docenteMateria.create');
        $ControlEscolar->givePermissionTo('docenteMateria.delete');

        $DepartamentoDocentes = Role::where('name','Departamento de docentes')->first();
        $DepartamentoDocentes->givePermissionTo('docenteMateria.show');
        $DepartamentoDocentes->givePermissionTo('docenteMateria.update');
        $DepartamentoDocentes->givePermissionTo('docenteMateria.create');
        $DepartamentoDocentes->givePermissionTo('docenteMateria.delete');

        $Docente = Role::where('name','Docente')->first();
        $Docente->givePermissionTo('docenteMateria.show');
        $Docente->givePermissionTo('docenteMateria.update');
        $Docente->givePermissionTo('docenteMateria.create');
        $Docente->givePermissionTo('docenteMateria.delete');

    }
}
