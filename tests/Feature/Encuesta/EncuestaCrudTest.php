<?php

namespace Tests\Feature\Encuesta;

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

class EncuestaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_encuestapregunta_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $response = $this->post("api/v1/encuestas", [
        'nombre' => 'desempeño docente',
        'descripcion' => 'Lorem ipsum dolor sit amet consectetur.',
        'usuario_id' => $user->id,
        'objetivo' => 'Lorem ipsum dolor sit amet, conse',
        'dirigido_a' => 'Todos',
      ]);

      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' => [
          "id",
          "nombre",
          "descripcion",
          "usuario_id",
          "usuario",
          "objetivo",
          "model_type",
          "model_id",
          "lanzada",
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

      $response = $this->get("api/v1/encuestas");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          [
            "id",
            "nombre",
            "descripcion",
            "usuario_id",
            "usuario",
            "objetivo",
            "model_type",
            "model_id",
            "lanzada",
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

      $response = $this->get("api/v1/encuestas/{$encuesta->id}");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          "id",
          "nombre",
          "descripcion",
          "usuario_id",
          "usuario",
          "objetivo",
          "model_type",
          "model_id",
          "lanzada",
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

      $response = $this->put("api/v1/encuestas/{$encuesta->id}",[
        'nombre' => 'desempeño docente',
        'descripcion' => 'Lorem ipsum dolor sit amet consectetur.',
        'usuario_id' => $user->id,
        'objetivo' => 'Lorem ipsum dolor sit amet, conse',
        'dirigido_a' => 'Todos',
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          "id",
          "nombre",
          "descripcion",
          "usuario_id",
          "usuario",
          "objetivo",
          "model_type",
          "model_id",
          "lanzada",
        ]
      ]);

      $encuesta->refresh();
      $this->assertEquals($encuesta->nombre, "desempeño docente");
    }

    public function test_encuestapregunta_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $encuesta = Encuesta::factory()
      ->for($user)
      ->create();

      $response = $this->delete("api/v1/encuestas/{$encuesta->id}");
      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          "id",
          "nombre",
          "descripcion",
          "usuario_id",
          "usuario",
          "objetivo",
          "model_type",
          "model_id",
          "lanzada",
        ]
      ]);
      $this->assertSoftDeleted($encuesta);
    }

}