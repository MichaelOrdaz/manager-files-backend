<?php

namespace Tests\Feature\Componente;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use App\Models\Model;
use App\Models\Periodo;

class ComponenteRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $componente;

    public function test_componente_has_one_model()
    {
      $componente = Componente::factory()
      ->has(Materia::factory()
        ->for(
          EspecialidadPeriodo::factory()
          ->for(Periodo::factory())
          ->for(Especialidad::factory()),
          'especialidad_periodo'
        )
        ->count(2)
      )
      ->create();

      $this->assertInstanceOf(Materia::class, $componente->materias[0]);
    }
}