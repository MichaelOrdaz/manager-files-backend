<?php

namespace Tests\Feature\ExamenPregunta;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenPregunta;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\ExamenRespuesta;
use App\Models\ExamenTipo;
use App\Models\PreguntaTipo;
use App\Models\User;

class ExamenPreguntaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examenpregunta;

    public function test_examenpregunta_has_one_model()
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
      ->count(3)
      ->create();

      $this->assertInstanceOf(Examen::class, $examenPregunta->examen);
      $this->assertInstanceOf(User::class, $examenPregunta->user);
      
      foreach ($examenPregunta->examenesRespuestas as $test) {
        $this->assertInstanceOf(ExamenRespuesta::class, $test);
      }
    }
}