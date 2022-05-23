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
        $departments = [
            'Órdenes de Pago',
            'Caja',
            'Flujo de Efectivo',
            'Conciliaciones Bancarias',
            'Registro y Seguimiento de Ingresos y Egresos',
            'Deuda Pública'
        ];
        foreach ($departments as $department) {
            Department::create([
                'name' => $department
            ]);
        }
    }
}
