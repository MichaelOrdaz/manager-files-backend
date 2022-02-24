<?php

namespace Tests\Feature\Periodo;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Periodo;
use App\Models\Model;

class PeriodoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $periodo;

    public function test_periodo_has_one_model()
    {
      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadperiodo, 'especialidad_periodo')
      ->create();

      $this->assertInstanceOf(Especialidad::class, $periodo->especialidades->first());
      $this->assertInstanceOf(EspecialidadPeriodo::class, $periodo->especialidad_periodo->first());
      $this->assertInstanceOf(Materia::class, $periodo->custom_materias($periodo->id, $especialidad->id)->first());
      $this->assertInstanceOf(Materia::class, $periodo->materias->first());
    }

}