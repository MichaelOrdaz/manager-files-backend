<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Estado;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Estado::create(['id'=>1, 'nombre' => 'AGUASCALIENTES']);
      Estado::create(['id'=>2, 'nombre' => 'BAJA CALIFORNIA']);
      Estado::create(['id'=>3, 'nombre' => 'BAJA CALIFORNIA SUR']);
      Estado::create(['id'=>4, 'nombre' => 'CAMPECHE']);
      Estado::create(['id'=>5, 'nombre' => 'COAHUILA']);
      Estado::create(['id'=>6, 'nombre' => 'COLIMA']);
      Estado::create(['id'=>7, 'nombre' => 'CHIAPAS']);
      Estado::create(['id'=>8, 'nombre' => 'CHIHUAHUA']);
      Estado::create(['id'=>9, 'nombre' => 'CIUDAD DE MÉXICO']);
      Estado::create(['id'=>10,'nombre' =>  'DURANGO']);
      Estado::create(['id'=>11,'nombre' =>  'GUANAJUATO']);
      Estado::create(['id'=>12,'nombre' =>  'GUERRERO']);
      Estado::create(['id'=>13,'nombre' =>  'HIDALGO']);
      Estado::create(['id'=>14,'nombre' =>  'JALISCO']);
      Estado::create(['id'=>15,'nombre' =>  'MÉXICO']);
      Estado::create(['id'=>16,'nombre' =>  'MICHOACAN']);
      Estado::create(['id'=>17,'nombre' =>  'MORELOS']);
      Estado::create(['id'=>18,'nombre' =>  'NAYARIT']);
      Estado::create(['id'=>19,'nombre' =>  'NUEVO LEON']);
      Estado::create(['id'=>20,'nombre' =>  'OAXACA']);
      Estado::create(['id'=>21,'nombre' =>  'PUEBLA']);
      Estado::create(['id'=>22,'nombre' =>  'QUERETARO']);
      Estado::create(['id'=>23,'nombre' =>  'QUINTANA ROO']);
      Estado::create(['id'=>24,'nombre' =>  'SAN LUIS POTOSI']);
      Estado::create(['id'=>25,'nombre' =>  'SINALOA']);
      Estado::create(['id'=>26,'nombre' =>  'SONORA']);
      Estado::create(['id'=>27,'nombre' =>  'TABASCO']);
      Estado::create(['id'=>28,'nombre' =>  'TAMAULIPAS']);
      Estado::create(['id'=>29,'nombre' =>  'TLAXCALA']);
      Estado::create(['id'=>30,'nombre' =>  'VERACRUZ']);
      Estado::create(['id'=>31,'nombre' =>  'YUCATAN']);
      Estado::create(['id'=>32,'nombre' =>  'ZACATECAS']);
    }
}
