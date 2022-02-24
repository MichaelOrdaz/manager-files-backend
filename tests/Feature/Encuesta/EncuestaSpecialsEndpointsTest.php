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

class EncuestaSpecialsEndpointsTest extends TestCase
{
    use DatabaseTransactions;

    protected $encuesta;

  public function test_encuesta_endpoint_copy()
  {
    $response = $this->get('api/v1/encuestas/'.$this->encuesta->id.':copy');
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'nombre',
        'descripcion',
        'usuario_id',
        'usuario',
        'objetivo',
        'model_type',
        'model_id',
        'lanzada',
      ]
    ]);
    $data = json_decode($response->getContent())->data;

    $this->assertEquals(
      $this->encuesta->encuestaPreguntas->count(),
      Encuesta::find($data->id)->encuestaPreguntas->count()
    );
  }

  public function test_encuesta_endpoint_resultados()
  {
    $response = $this->get('api/v1/encuestas/'.$this->encuesta->id.'/resultados');
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'nombre',
        'creador_id',
        'creador',
        'usuarios_respondieron_encuesta',
      ]
    ]);
  }

  public function test_encuesta_endpoint_exportar_resultados()
  {
    $response = $this->get('api/v1/encuestas/'.$this->encuesta->id.'/exportar');
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'url',
      ]
    ]);
  }

  /**
   * Primer función a ejecutar del test,
   */
  public function setUp():void
  {
    parent::setUp();
    //Creamos usuario que se usara para las peticiones
    $usuario = User::factory()->create();
    $usuario->assignRole('Admin');
    //uso ese usuario como el usuario autenticado en cada petición
    $this->actingAs($usuario, 'api');

    $this->encuesta = Encuesta::factory()
    ->for($usuario)
    ->create();

    EncuestaPregunta::factory()
    ->for($this->encuesta, 'Encuesta')
    ->for($usuario, 'User')
    ->for(PreguntaTipo::factory(), 'PreguntasTipo')
    ->count(3)
    ->create();
  }

}