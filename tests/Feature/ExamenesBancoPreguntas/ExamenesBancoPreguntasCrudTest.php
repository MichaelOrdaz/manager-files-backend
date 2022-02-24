<?php

namespace Tests\Feature\ExamenesBancoPreguntas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenPregunta;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenesBancoPreguntas;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\ExamenRespuesta;
use App\Models\ExamenTipo;
use App\Models\PreguntaTipo;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ExamenesBancoPreguntasCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_examen_banco_preguntas_crud_create()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $file = UploadedFile::fake()->image('avatar.jpg');

      $respuesta = json_encode(['hola' => 'mundo']);
      $response = $this->post("api/v1/examenes-banco-preguntas", [
        'materia_id' => $materia->id,
        'usuario_id' => $user->id,
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
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'usuario_id',
          'usuario',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
      ]);

    }

    public function test_examen_banco_preguntas_crud_list()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $banco = ExamenesBancoPreguntas::factory()
      ->for($materia)
      ->for($user)
      ->for($preguntaTipo)
      ->count(3)
      ->create();

      $response = $this->get("api/v1/examenes-banco-preguntas");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'usuario_id',
          'usuario',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ]],
        'message',
        'success'
      ]);
    }

    public function test_examen_banco_preguntas_crud_get()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $banco = ExamenesBancoPreguntas::factory()
      ->for($materia)
      ->for($user)
      ->for($preguntaTipo)
      ->create();

      $response = $this->get("api/v1/examenes-banco-preguntas/{$banco->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'usuario_id',
          'usuario',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
        'message',
        'success'
      ]);
    }

    public function test_examen_banco_preguntas_crud_update()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $banco = ExamenesBancoPreguntas::factory()
      ->for($materia)
      ->for($user)
      ->for($preguntaTipo)
      ->create();

      $file = UploadedFile::fake()->image('avatar2.jpg');

      $respuesta = json_encode(['mounstruo' => 'comegalletas']);
      $response = $this->post("api/v1/examenes-banco-preguntas/{$banco->id}", [
        'materia_id' => $materia->id,
        'usuario_id' => $user->id,
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
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'usuario_id',
          'usuario',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
        'message',
        'success'
      ]);

      $banco->refresh();
      $this->assertEquals($banco->pregunta, 'una pregunta de prueba');
      $this->assertEquals($banco->valor, '3');
    }

    public function test_examen_banco_preguntas_crud_delete()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $banco = ExamenesBancoPreguntas::factory()
      ->for($materia)
      ->for($user)
      ->for($preguntaTipo)
      ->create();

      $response = $this->delete("api/v1/examenes-banco-preguntas/{$banco->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'usuario_id',
          'usuario',
          'pregunta',
          'valor',
          'respuestas',
          'imagen_url',
        ],
        'message',
        'success'
      ]);
      $this->assertSoftDeleted($banco);
    }
}