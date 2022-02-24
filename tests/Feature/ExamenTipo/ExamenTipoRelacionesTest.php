<?php

namespace Tests\Feature\ExamenTipo;

use App\Models\Examen;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenTipo;
use App\Models\Model;
use App\Models\User;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use App\Models\Periodo;

class ExamenTipoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examentipo;

    public function test_examentipo_has_examen()
    {
      $examenTipo = ExamenTipo::factory()->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $this->assertInstanceOf(Examen::class, $examenTipo->examen);
    }
}