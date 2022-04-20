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
            'nombre' => 'Backend'
        ]);

        Department::create([
            'nombre' => 'Frontend'
        ]);

        Department::create([
            'nombre' => 'QA'
        ]);

        Department::create([
            'nombre' => 'Diseño UI/UX'
        ]);
    }
}
