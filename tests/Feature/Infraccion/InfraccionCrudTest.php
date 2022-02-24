<?php

namespace Tests\Feature\Infraccion;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\Infraccion;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class InfraccionCrudTest extends TestCase
{
  use DatabaseTransactions;
  protected $infraccion,$acusado;

  /**
   * @author Enrique Sevilla <sevilla@puller.mx>
   * @version  1.0
   * BLOQUE : TEST PARA CRUD CON USUARIO CON TODOS LOS PERMISOS SOBRE EL MODELO
   * NOTA : con assertJsonStructure pueden traer mas datos pero no menos
   * si se necesita ver los errores sin el handler agregar:
   *  $this->withoutExceptionHandling();
   * en la primera linea de tu función
   */

  public function test_infraccion_crud_create()
  {
    $files[] = UploadedFile::fake()->image('avatar1.jpg');
    $files[] = UploadedFile::fake()->image('avatar2.jpg');

    $response = $this->post('/api/v1/infracciones',[
      'acusado_id' => $this->acusado->id,
      'motivo' => 'motivo',
      'lugar' => 'Lugar',
      'fecha' => '2021-11-11',
      'archivo' => $files
    ]);

    $response->assertStatus(201)
    ->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'usuario',
        'acusado_id',
        'motivo',
        'lugar',
        'fecha',
        'archivo_url',
      ]
    ]);

  }

  public function test_infraccion_crud_create_sin_imagenes_array_vacio()
  {
    $response = $this->post('/api/v1/infracciones',[
      'acusado_id' => $this->acusado->id,
      'motivo' => 'motivo',
      'lugar' => 'Lugar',
      'fecha' => '2021-11-11',
      'archivo' => []
    ]);

    $response->assertStatus(201)
    ->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'usuario',
        'acusado_id',
        'motivo',
        'lugar',
        'fecha',
        'archivo_url',
      ]
    ]);

  }

  public function test_infraccion_crud_create_sin_imagenes_sin_parametro()
  {
    $response = $this->post('/api/v1/infracciones',[
      'acusado_id' => $this->acusado->id,
      'motivo' => 'motivo',
      'lugar' => 'Lugar',
      'fecha' => '2021-11-11',
    ]);

    $response->assertStatus(201)
    ->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'usuario',
        'acusado_id',
        'motivo',
        'lugar',
        'fecha',
        'archivo_url',
      ]
    ]);

  }

  public function test_infraccion_crud_list(){
    $response = $this->get('/api/v1/infracciones');
    $response->assertStatus(200)
    ->assertJsonStructure([
      'data' => [
        [
          'id',
          'usuario_id',
          'usuario',
          'acusado_id',
          'motivo',
          'lugar',
          'fecha',
          'archivo_url',
        ]
      ]
    ]);
  }

  public function test_infraccion_crud_get(){
    $response = $this->get('/api/v1/infracciones/'.$this->infraccion->id);
    $response->assertStatus(200)
    ->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'usuario',
        'acusado_id',
        'motivo',
        'lugar',
        'fecha',
        'archivo_url',
      ]
    ]);
  }

  public function test_infraccion_crud_update(){
    $response = $this->post('/api/v1/infracciones/'.$this->infraccion->id,[
      'acusado_id' => $this->acusado->id,
      'motivo' => 'update',
      'lugar' => 'Lugar',
      'fecha' => '2021-11-11',
    ]);

    $response->assertStatus(200)
    ->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'usuario',
        'acusado_id',
        'motivo',
        'lugar',
        'fecha',
        'archivo_url',
      ]
    ]);

      $infraccionUpdate = Infraccion::find($this->infraccion->id);
      $this->assertEquals($infraccionUpdate->motivo,'update');
    }

  public function test_infraccion_crud_delete(){
    $response = $this->delete('/api/v1/infracciones/'.$this->infraccion->id);
    $response->assertStatus(200)
    ->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'usuario',
        'acusado_id',
        'motivo',
        'lugar',
        'fecha',
        'archivo_url',
      ]
    ]);

    $infraccionDelete = Infraccion::find($this->infraccion->id);
    $this->assertEquals($infraccionDelete,NULL);
  }

  /**
   * @author Enrique Sevilla <sevilla@puller.mx>
   * @version  1.0
   * Primer función a ejecutar del test,
   * Dentro de esta función iniciamos una transacción y
   * creamos todos las entidades que interactúan con nuestro modelo
   */
  public function setUp():void
  {
    parent::setUp();
    //Creamos usuario que se usara para las peticiones
    $usuario = User::factory()->create();
    $usuario->assignRole('Admin');
    //uso ese usuario como el usuario autenticado en cada petición
    $this->actingAs($usuario, 'api');
    $this->acusado = User::factory()->create();

    $this->infraccion = Infraccion::factory()
    ->for($usuario)
    ->for($this->acusado,'acusado')
    ->create();
  }

}