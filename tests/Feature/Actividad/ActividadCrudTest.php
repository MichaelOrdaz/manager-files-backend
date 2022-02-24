<?php

namespace Tests\Feature\Actividad;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\Actividad;
use Illuminate\Support\Facades\Hash;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActividadCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $actividad;

    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * BLOQUE : TEST PARA CRUD CON USUARIO CON TODOS LOS PERMISOS SOBRE EL MODELO
     * NOTA : con assertJsonStructure pueden traer mas datos pero no menos
     * si se necesita ver los errores sin el handler agregar:
     *  $this->withoutExceptionHandling();
     * en la primera linea de tu función
     */

    public function test_actividad_crud_create()
    {
      $response = $this->post("/api/v1/usuarios/1/actividades",[
        "titulo" => "Titulo",
        "contenido" => "Contenido",
        "fecha_inicio" => "2020-10-12 05:05:01",
        "fecha_fin" => "2020-10-12 05:05:01"
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        "data" => [
          "id",
          "titulo",
          "contenido",
          "fecha_inicio",
          "fecha_fin",
        ]
      ]);

    }

    public function test_actividad_crud_list(){
      $response = $this->get("/api/v1/usuarios/1/actividades");
      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          [
            "id",
            "titulo",
            "contenido",
            "fecha_inicio",
            "fecha_fin",
            "usuario_id",
            "usuario",
          ]
        ]
      ]);
    }

    public function test_actividad_crud_get(){
      $response = $this->get("/api/v1/usuarios/1/actividades/".$this->actividad->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "titulo",
          "contenido",
          "fecha_inicio",
          "fecha_fin",
          "usuario_id",
          "usuario",
        ]
      ]);
    }

    public function test_actividad_crud_update(){
      $response = $this->put("/api/v1/usuarios/1/actividades/".$this->actividad->id,[
        "titulo" => "update",
        "contenido" => "Contenido",
        "fecha_inicio" => "2020-10-12 05:05:01",
        "fecha_fin" => "2020-10-12 05:05:01"
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "titulo",
          "contenido",
          "fecha_inicio",
          "fecha_fin",
        ]
      ]);

      $actividadUpdate = Actividad::find($this->actividad->id);
      $this->assertEquals($actividadUpdate->titulo,"update");
    }

    public function test_actividad_crud_delete(){
      $response = $this->delete("/api/v1/usuarios/1/actividades/".$this->actividad->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "titulo",
          "contenido",
          "fecha_inicio",
          "fecha_fin",
        ]
      ]);

      $actividadDelete = Actividad::find($this->actividad->id);
      $this->assertEquals($actividadDelete,NULL);
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

      $this->actividad = Actividad::create([
          "usuario_id" => 1,
          "titulo" => "algo",
          "contenido" => "contenidop",
          "fecha_inicio" => "2020-10-12 05:05:01",
          "fecha_fin" => "2020-10-12 05:05:01"
      ]);
    }
}