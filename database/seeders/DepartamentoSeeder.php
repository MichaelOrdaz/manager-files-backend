<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Departamento::create([
            'nombre' => 'Backend'
        ]);

        Departamento::create([
            'nombre' => 'Frontend'
        ]);

        Departamento::create([
            'nombre' => 'QA'
        ]);

        Departamento::create([
            'nombre' => 'Diseño UI/UX'
        ]);
    }
}
