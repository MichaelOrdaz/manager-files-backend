<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::create([
            'name' => 'Ordenes de cobro'
        ]);

        Department::create([
            'name' => 'Facturas'
        ]);

        Department::create([
            'name' => 'Notas de credito'
        ]);
    }
}
