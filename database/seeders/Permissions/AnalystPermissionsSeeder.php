<?php

namespace Database\Seeders\Permissions;

use App\Helpers\Dixa;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AnalystPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run ()
    {
      Permission::create(['name' => Dixa::ANALYST_READ_PERMISSION]);
      Permission::create(['name' => Dixa::ANALYST_WRITE_PERMISSION]);
    }
}
