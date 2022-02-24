<?php

namespace Tests\Feature\Examen;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\ExamenTema;
use App\Models\ExamenTipo;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExamenEndpointBindTemaTest extends TestCase
{
  use DatabaseTransactions;

  protected $examen, $tema;

  public function test_examen_endpoint_bind_tema()
  {
    $response = $this->post('api/v1/examenes/'. $this->examen->id .'/temas/'. $this->tema->id .':bind',[]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'examen_id',
        'examen',
        'tema_id',
        'tema',
      ]
    ]);
    $response->assertJsonFragment(['examen_id' => $this->examen->id]);
    $response->assertJsonFragment(['tema_id' => $this->tema->id]);
  }

  public function test_examen_endpoint_unbind_tema()
  {
    $response = $this->post('api/v1/examenes/'. $this->examen->id .'/temas/'. $this->tema->id .':unbind',[]);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'examen_id',
        'examen',
        'tema_id',
        'tema',
      ]
    ]);
    $response->assertJsonFragment(['examen_id' => $this->examen->id]);
    $response->assertJsonFragment(['tema_id' => $this->tema->id]);
  }

  public function setUp():void
  {
    parent::setUp();

    $user = User::factory()->create();
    $user->assignRole('Docente');
    $this->actingAs($user, 'api');

    $materia = Materia::factory()
    ->for(Componente::factory())
    ->for(
      EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory()),
      'especialidad_periodo'
    )
    ->has(Unidad::factory(), 'unidades')
    ->create();

    $examenTipo = ExamenTipo::factory()->create();
    $this->examen = Examen::factory()
    ->for($user)
    ->for($materia)
    ->for($examenTipo)
    ->create();

    $unidad = $materia->unidades->first();
    $this->tema = Tema::factory()->for($unidad)->create();

    ExamenTema::factory()
    ->for($this->examen)
    ->for($this->tema)
    ->create();
  }

}