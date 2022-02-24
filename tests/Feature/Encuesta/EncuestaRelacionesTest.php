<?php

namespace Tests\Feature\Encuesta;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use App\Models\Model;
use App\Models\PreguntaTipo;
use App\Models\User;

class EncuestaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $encuesta;

    public function test_encuesta_has_one_model()
    {
      $this->withoutExceptionHandling();
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

      $this->assertInstanceOf(User::class, $encuesta->User);
      $this->assertContainsOnlyInstancesOf(EncuestaPregunta::class, $encuesta->EncuestaPreguntas);
      $this->assertContainsOnlyInstancesOf(EncuestaRespuesta::class, $encuesta->encuestaRespuestas);
    }
}