<?php

namespace Tests\Feature\Eventos;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\Evento;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EventoCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $evento;

    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * BLOQUE : TEST PARA CRUD CON USUARIO CON TODOS LOS PERMISOS SOBRE EL MODELO
     * NOTA : con assertJsonStructure pueden traer mas datos pero no menos
     * si se necesita ver los errores sin el handler agregar:
     *  $this->withoutExceptionHandling();
     * en la primera linea de tu función
     */

    public function test_evento_crud_create()
    {
      $response = $this->post('/api/v1/eventos',[
        'nombre' => 'Evento',
        'descripcion' => 'Descripción',
        'usuario_id' => 1,
        'dirigido_a' => 'Docente',
        'fecha_inicio' => '2021-12-11 12:00:00',
        'fecha_fin' => '2021-12-15 12:00:00',
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'fecha_inicio',
          'fecha_fin',
          'model_type',
          'model_id',
        ]
      ]);

    }

    public function test_evento_crud_list(){
      $response = $this->get('/api/v1/eventos');
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'fecha_inicio',
          'fecha_fin',
          'model_type',
          'model_id',
          ]
        ]
      ]);
    }

    public function test_evento_crud_get(){
      $response = $this->get('/api/v1/eventos/'.$this->evento->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'fecha_inicio',
          'fecha_fin',
          'model_type',
          'model_id',
        ]
      ]);
    }

    public function test_evento_crud_update(){
      $response = $this->post('/api/v1/eventos/'.$this->evento->id,[
        'nombre' => 'update',
        'descripcion' => 'Descripción',
        'usuario_id' => 1,
        'dirigido_a' => 'Docente',
        'fecha_inicio' => '2021-12-11 11:00:00',
        'fecha_fin' => '2022-02-11 11:00:00',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'fecha_inicio',
          'fecha_fin',
          'model_type',
          'model_id',
        ]
      ]);

      $eventoUpdate = Evento::find($this->evento->id);
      $this->assertEquals($eventoUpdate->nombre,'update');
    }

    public function test_evento_crud_delete(){
      $response = $this->delete('/api/v1/eventos/'.$this->evento->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'fecha_inicio',
          'fecha_fin',
          'model_type',
          'model_id',
        ]
      ]);

      $eventoDelete = Evento::find($this->evento->id);
      $this->assertEquals($eventoDelete,NULL);
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
      $usuario = User::create([
        'email' => 'adminTest@puller.mx',
        'password' => Hash::make('12345'),
        'activo' =>   true,
        'firebase_uid' => 'kJK1CRjkci9mCY7CuN',
        'email_verified_at' => Carbon::now(),
      ]);
      $usuario->assignRole('Admin');
      //uso ese usuario como el usuario autenticado en cada petición
      $this->actingAs($usuario, 'api');

      $this->evento = Evento::factory()
      ->for($usuario)
      ->create();

    }
}