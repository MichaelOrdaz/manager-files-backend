<?php

namespace Tests\Feature\EncustaPregunta;

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
use Illuminate\Http\UploadedFile;

class EncustaPreguntaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_encuestapregunta_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $encuesta = Encuesta::factory()
      ->for($user)
      ->create();

      $tipo = PreguntaTipo::factory()->create();

      $file = UploadedFile::fake()->image('avatar.jpg');

      $response = $this->post("api/v1/encuestas/{$encuesta->id}/encuestas-preguntas", [
        'usuario_id' => $user->id,
        'tipo_id' => $tipo->id,
        'pregunta' => 'una pregunta de encuesta',
        'respuestas' => json_encode(['hola' => 'mundo']),
        'archivo' => $file,
        'activo' => 1,
      ]);

      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'pregunta',
          'encuesta',
          'tipo',
          'respuestas',
          'imagen_url',
        ]
      ]);
    }

    public function test_encuestapregunta_crud_list()
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

      $response = $this->get("api/v1/encuestas/{$encuesta->id}/encuestas-preguntas");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'usuario',
            'pregunta',
            'encuesta',
            'tipo',
            'respuestas',
            'imagen_url',
          ]
        ]
      ]);
    }

    public function test_encuestapregunta_crud_get()
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

      $response = $this->get("api/v1/encuestas/{$encuesta->id}/encuestas-preguntas/{$pregunta->id}");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'pregunta',
          'encuesta',
          'tipo',
          'respuestas',
          'imagen_url',
        ]
      ]);
    }

    public function test_encuestapregunta_crud_update()
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

      $file = UploadedFile::fake()->image('avatar.jpg');

      $response = $this->post("api/v1/encuestas/{$encuesta->id}/encuestas-preguntas/{$pregunta->id}",[
        'usuario_id' => $user->id,
        'tipo_id' => $tipo->id,
        'pregunta' => 'una pregunta de encuesta update',
        'respuestas' => json_encode(['hola' => 'mundote']),
        'archivo' => $file,
        'activo' => 1,
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
            'id',
          'usuario',
          'pregunta',
          'encuesta',
          'tipo',
          'respuestas',
          'imagen_url',
        ]
      ]);

      $pregunta->refresh();
      $this->assertEquals($pregunta->pregunta, "una pregunta de encuesta update");
    }

    public function test_encuestapregunta_crud_delete()
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

      $response = $this->delete("api/v1/encuestas/{$encuesta->id}/encuestas-preguntas/{$pregunta->id}");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'pregunta',
          'encuesta',
          'tipo',
          'respuestas',
          'imagen_url',
        ]
      ]);
      $this->assertSoftDeleted($pregunta);
    }

}