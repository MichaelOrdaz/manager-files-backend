<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MarcaDeTiempoPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'timestamp.show']);
        Permission::create(['name' => 'timestamp.update']);
        Permission::create(['name' => 'timestamp.create']);
        Permission::create(['name' => 'timestamp.delete']);

        $alumno = Role::where('name', 'Alumno')->first();
        $alumno->givePermissionTo('timestamp.show');
        $alumno->givePermissionTo('timestamp.update');
        $alumno->givePermissionTo('timestamp.create');

        $aspirante = Role::where('name', 'Aspirante a ingreso')->first();
        $aspirante->givePermissionTo('timestamp.show');
        $aspirante->givePermissionTo('timestamp.update');
        $aspirante->givePermissionTo('timestamp.create');
    }
}
