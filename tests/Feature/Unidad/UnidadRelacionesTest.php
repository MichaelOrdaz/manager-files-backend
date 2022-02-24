<?php

namespace Tests\Feature\Unidad;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Unidad;
use App\Models\Model;
use App\Models\Periodo;
use App\Models\Tema;

class UnidadRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $unidad;

    public function test_unidad_belong_materia()
    {
      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $this->assertInstanceOf(Materia::class, $unidad->materia);
    }

    public function test_unidad_has_temas()
    {
      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      Tema::factory()
      ->for($unidad)
      ->count(5)
      ->create();

      foreach ($unidad->temas as $tema) {
        $this->assertInstanceOf(Tema::class, $tema);
      }
    }
}