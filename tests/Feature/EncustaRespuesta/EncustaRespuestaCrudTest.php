<?php

namespace Tests\Feature\EncustaRespuesta;

use App\Models\Encuesta;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\EncustaRespuesta;
use App\Models\PreguntaTipo;
use App\Models\User;

class EncustaRespuestaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_encustarespuesta_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $encuesta = Encuesta::factory()
      ->for($user)
      ->create();

      $tipo = PreguntaTipo::factory()->create();

      $pregunta = EncuestaPregunta::factory()
      ->for($encuesta, 'Encuesta')
      ->for($user, 'User')
      ->for($tipo, 'PreguntasTipo')
      ->create();

      $response = $this->post("api/v1/encuestas/{$encuesta->id}/encuestas-respuestas", [
        'usuario_id' => $user->id,
        'pregunta_id' => $pregunta->id,
        'respuesta' => 'lorem',
      ]);

      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'pregunta',
          'encuesta',
          'respuesta',
        ]
      ]);
    }

    public function test_encuestarespuesta_crud_list()
    {
      $this->handleValidationExceptions();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $encuesta = Encuesta::factory()
      ->for($user)
      ->create();

      $tipo = PreguntaTipo::factory()->create();

      $pregunta = EncuestaPregunta::factory()
      ->for($encuesta, 'Encuesta')
      ->for($user, 'User')
      ->for($tipo, 'PreguntasTipo')
      ->create();

      EncuestaRespuesta::factory()
      ->for($user)
      ->for($encuesta)
      ->for($pregunta)
      ->create();

      $response = $this->get("api/v1/encuestas/{$encuesta->id}/encuestas-respuestas");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'usuario',
            'pregunta',
            'encuesta',
            'respuesta',
          ]
        ]
      ]);
    }

    public function test_encustarespuesta_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->get("api/v1/encuestas/{$encuesta->id}/encuestas-respuestas/{$respuesta->id}");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
            'id',
            'usuario',
            'pregunta',
            'encuesta',
            'respuesta',
        ]
      ]);
    }

    public function test_encustarespuesta_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->put("api/v1/encuestas/{$encuesta->id}/encuestas-respuestas/{$respuesta->id}",[
        'usuario_id' => $user->id,
        'pregunta_id' => $pregunta->id,
        'respuesta' => 'lorem ipsum',
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
            'id',
            'usuario',
            'pregunta',
            'encuesta',
            'respuesta',
        ]
      ]);

      $respuesta->refresh();
      $this->assertEquals($respuesta->respuesta, "lorem ipsum");
    }

    public function test_encustarespuesta_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->delete("api/v1/encuestas/{$encuesta->id}/encuestas-respuestas/{$respuesta->id}");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
            'id',
            'usuario',
            'pregunta',
            'encuesta',
            'respuesta',
        ]
      ]);
      $this->assertSoftDeleted($respuesta);
    }

}