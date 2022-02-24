<?php

namespace Tests\Feature\ExamenPregunta;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenPregunta;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\ExamenRespuesta;
use App\Models\ExamenTipo;
use App\Models\PreguntaTipo;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ExamenPreguntaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_examenpregunta_crud_create()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $file = UploadedFile::fake()->image('avatar.jpg');

      $respuesta = json_encode(['hola' => 'mundo']);
      $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas", [
        'tipo_id' => $preguntaTipo->id,
        'pregunta' => 'una pregunta de prueba',
        'valor' => '3',
        'respuestas' => $respuesta,
        'activo' => '1',
        'imagen' => $file,
      ]);

      $response->assertStatus(201);
      $response->assertJsonFragment(['pregunta' => 'una pregunta de prueba']);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'examen_id',
          'examen',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
      ]);

    }

    public function test_examenpregunta_crud_list()
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
      ->count(3)
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'usuario_id',
          'usuario',
          'examen_id',
          'examen',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ]],
        'message',
        'success'
      ]);
    }

    public function test_examenpregunta_crud_get()
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

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'examen_id',
          'examen',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
        'message',
        'success'
      ]);
    }

    public function test_examenpregunta_crud_update()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $file = UploadedFile::fake()->image('avatar2.jpg');

      $respuesta = json_encode(['mounstruo' => 'comegalletas']);
      $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}", [
        'tipo_id' => $preguntaTipo->id,
        'pregunta' => 'una pregunta de prueba',
        'valor' => '3',
        'respuestas' => $respuesta,
        'activo' => '1',
        'imagen' => $file,
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'examen_id',
          'examen',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
        'message',
        'success'
      ]);

      $examenPregunta->refresh();
      $this->assertEquals($examenPregunta->pregunta, 'una pregunta de prueba');
      $this->assertEquals($examenPregunta->valor, '3');
    }

    public function test_examenpregunta_crud_delete()
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

      $response = $this->delete("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-preguntas/{$examenPregunta->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'examen_id',
          'examen',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
        'message',
        'success'
      ]);
      $this->assertSoftDeleted($examenPregunta);
    }
}