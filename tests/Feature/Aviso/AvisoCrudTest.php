<?php

namespace Tests\Feature\Aviso;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\Aviso;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AvisoCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $aviso;

    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * BLOQUE : TEST PARA CRUD CON USUARIO CON TODOS LOS PERMISOS SOBRE EL MODELO
     * NOTA : con assertJsonStructure pueden traer mas datos pero no menos
     * si se necesita ver los errores sin el handler agregar:
     *  $this->withoutExceptionHandling();
     * en la primera linea de tu función
     */

    public function test_aviso_crud_create()
    {
      $response = $this->post('/api/v1/avisos',[
        'nombre' => 'Aviso',
        'descripcion' => 'Descripción',
        'usuario_id' => 1,
        'dirigido_a' => 'Docente',
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'model_type',
          'model_id',
        ]
      ]);

    }

    public function test_aviso_crud_list(){
      $response = $this->get('/api/v1/avisos');
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'model_type',
          'model_id',
          ]
        ]
      ]);
    }

    public function test_aviso_crud_get(){
      $response = $this->get('/api/v1/avisos/'.$this->aviso->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'model_type',
          'model_id',
        ]
      ]);
    }

    public function test_aviso_crud_update(){
      $response = $this->post('/api/v1/avisos/'.$this->aviso->id,[
        'nombre' => 'update',
        'descripcion' => 'Descripción',
        'usuario_id' => 1,
        'dirigido_a' => 'Docente',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'model_type',
          'model_id',
        ]
      ]);

      $avisoUpdate = Aviso::find($this->aviso->id);
      $this->assertEquals($avisoUpdate->nombre,'update');
    }

    public function test_aviso_crud_delete(){
      $response = $this->delete('/api/v1/avisos/'.$this->aviso->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'model_type',
          'model_id',
        ]
      ]);

      $avisoDelete = Aviso::find($this->aviso->id);
      $this->assertEquals($avisoDelete,NULL);
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

      $this->aviso = Aviso::create([
        'nombre' => 'Aviso',
        'descripcion' => 'Descripción',
        'usuario_id' => 1,
        'model_type' => 'Todos',
      ]);
    }
}