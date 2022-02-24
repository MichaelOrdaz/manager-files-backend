<?php

namespace Tests\Feature\Examen;

use Tests\TestCase;

use App\Models\User;
use App\Models\Examen;
use App\Models\ExamenTipo;
use Illuminate\Support\Str;
use App\Models\PreguntaTipo;
use App\Models\ExamenPregunta;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExamenEndpointCopyTest extends TestCase
{
  use DatabaseTransactions;

  public function test_examen_endpoint_copy()
  {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $user->assignRole('Docente');
    $this->actingAs($user, 'api');

    $examenTipo = ExamenTipo::factory()->create();
    $examen = Examen::factory()
    ->for($user)
    ->for($examenTipo)
    ->create();

    $tiposDePregunta = PreguntaTipo::all();
    foreach ($tiposDePregunta as $tipo) {
      $preguntaTipoNombre = Str::slug($tipo->nombre, '_');
      ExamenPregunta::factory()
        ->{$preguntaTipoNombre}()
        ->for($examen)
        ->for($user)
        ->for($tipo)
        ->create(['valor'=>50]);
    }

    $response = $this->post('api/v1/examenes/examen/'.$examen->id.':copy',[]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' => [
        "id",
        "nombre",
        "descripcion",
        "usuario_id",
        "materia_id",
        "tipo_id",
        "duracion_minutos",
        "aleatorio",
        "lecciones_referencia",
        "puntaje_minimo",
        "activo",
        "materia",
        "user",
        "examen_tipo",
        'examen_preguntas',
      ]
    ]);

  }

}