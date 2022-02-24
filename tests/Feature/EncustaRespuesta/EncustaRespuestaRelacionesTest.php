<?php

namespace Tests\Feature\EncustaRespuesta;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Model;
use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use App\Models\EncustaRespuesta;
use App\Models\PreguntaTipo;
use App\Models\User;

class EncustaRespuestaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $encustarespuesta;

    public function test_encustarespuesta_has_belongs()
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

      $this->assertInstanceOf(User::class, $respuesta->User);
      $this->assertInstanceOf(EncuestaPregunta::class, $respuesta->EncuestaPregunta);
      $this->assertInstanceOf(Encuesta::class, $respuesta->Encuesta);
    }
}