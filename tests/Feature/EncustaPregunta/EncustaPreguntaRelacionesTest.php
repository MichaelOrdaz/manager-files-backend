<?php

namespace Tests\Feature\EncustaPregunta;

use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\EncustaPregunta;
use App\Models\Model;
use App\Models\PreguntaTipo;
use App\Models\User;

class EncustaPreguntaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $encustapregunta;

    public function test_encustapregunta_has_one_model()
    {
      $user = User::factory()->create();

      $encuesta = Encuesta::factory()
      ->for($user)
      ->create();

      $tipo = PreguntaTipo::factory()->create();

      $pregunta = EncuestaPregunta::factory()
      ->for($encuesta, 'Encuesta')
      ->for($user, 'User')
      ->for($tipo, 'PreguntasTipo')
      ->create();

      $respuesta = EncuestaRespuesta::factory()
      ->for($user)
      ->for($encuesta)
      ->for($pregunta)
      ->create();

      $this->assertInstanceOf(User::class, $pregunta->User);
      $this->assertInstanceOf(Encuesta::class, $pregunta->Encuesta);
      $this->assertInstanceOf(PreguntaTipo::class, $pregunta->PreguntasTipo);
      $this->assertContainsOnlyInstancesOf(EncuestaRespuesta::class, $pregunta->encuestasRespuestas);

    }
}