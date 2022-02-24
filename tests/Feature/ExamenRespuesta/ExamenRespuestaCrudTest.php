<?php

namespace Tests\Feature\ExamenRespuesta;

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

class ExamenRespuestaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_examenrespuesta_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}/examenes-respuestas", [
        'respuesta' => 'respuestas de le pregunta',
        'observaciones' => 'obvervaciones',
        'activo' => '1',
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['respuesta' => 'respuestas de le pregunta'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'pregunta_id',
          'pregunta',
          'examen_id',
          'examen',
          'respuesta',
          'calificacion',
          'observaciones',
          'activo',
        ],
        'message',
        'success'
      ]);
    }

    public function test_examenrespuesta_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      ExamenRespuesta::factory()
      ->for($user)
      ->for($examenPregunta)
      ->for($examen)
      ->count(3)
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-respuestas");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'usuario_id',
          'usuario',
          'pregunta_id',
          'pregunta',
          'examen_id',
          'examen',
          'respuesta',
          'calificacion',
          'observaciones',
          'activo',
        ]],
        'message',
        'success'
      ]);
    }

    public function test_examenrespuesta_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}/examenes-respuestas/{$examenRespuesta->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'pregunta_id',
          'pregunta',
          'examen_id',
          'examen',
          'respuesta',
          'calificacion',
          'observaciones',
          'activo',
        ],
        'message',
        'success'
      ]);
    }

    public function test_examenrespuesta_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->put("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}/examenes-respuestas/{$examenRespuesta->id}", [
        'respuesta' => 'respuestas de le pregunta update',
        'observaciones' => 'obvervaciones',
        'activo' => '1',
        'calificacion' => 10,
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'pregunta_id',
          'pregunta',
          'examen_id',
          'examen',
          'respuesta',
          'calificacion',
          'observaciones',
          'activo',
        ],
        'message',
        'success'
      ]);

      $examenRespuesta->refresh();
      $this->assertEquals($examenRespuesta->respuesta, 'respuestas de le pregunta update');
      $this->assertEquals($examenRespuesta->calificacion, 10);
    }

    public function test_examenrespuesta_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

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

      $response = $this->delete("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}/examenes-respuestas/{$examenRespuesta->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'pregunta_id',
          'pregunta',
          'examen_id',
          'examen',
          'respuesta',
          'calificacion',
          'observaciones',
          'activo',
        ],
        'message',
        'success'
      ]);
      $this->assertSoftDeleted($examenRespuesta);
    }
}