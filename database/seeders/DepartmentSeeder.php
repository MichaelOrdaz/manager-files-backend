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
            'name' => 'Backend'
        ]);

        Department::create([
            'name' => 'Frontend'
        ]);

        Department::create([
            'name' => 'QA'
        ]);

        Department::create([
            'name' => 'Diseño UI/UX'
        ]);
    }
}
