<?php

namespace Tests\Feature\ExamenRespuesta;

use App\Models\Examen;
use App\Models\ExamenPregunta;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenRespuesta;
use App\Models\Model;
use App\Models\User;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\ExamenTipo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\PreguntaTipo;

class ExamenRespuestaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examenrespuesta;

    public function test_examenrespuesta_belong_relatioship()
    {
      $user = User::factory()->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $examenPregunta = ExamenPregunta::factory()
      ->for($user)
      ->for($examen)
      ->for(PreguntaTipo::factory())
      ->create();

      $examenRespuesta = ExamenRespuesta::factory()
      ->for($user)
      ->for($examenPregunta)
      ->for($examen)
      ->create();

      $this->assertInstanceOf(User::class, $examenRespuesta->user);
      $this->assertInstanceOf(ExamenPregunta::class, $examenRespuesta->examenPregunta);
      $this->assertInstanceOf(Examen::class, $examenRespuesta->examen);
    }
}