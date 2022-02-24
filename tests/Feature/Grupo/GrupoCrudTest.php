<?php

namespace Tests\Feature\Grupo;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;

use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Especialidad;
use Spatie\Permission\Models\Role;
use App\Models\EspecialidadPeriodo;
use Illuminate\Support\Facades\Hash;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrupoCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $grupo;

    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * BLOQUE : TEST PARA CRUD CON USUARIO CON TODOS LOS PERMISOS SOBRE EL MODELO
     * NOTA : con assertJsonStructure pueden traer mas datos pero no menos
     * si se necesita ver los errores sin el handler agregar:
     *  $this->withoutExceptionHandling();
     * en la primera linea de tu función
     */

    public function test_grupo_crud_create()
    {
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "activo" => 1
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        "data" => [
          "id",
          "nombre",
          "activo",
        ]
      ]);

    }

    public function test_grupo_crud_list(){
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          [
            "id",
            "nombre",
            "activo",
          ]
        ]
      ]);
    }

    public function test_grupo_crud_get(){
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "nombre",
          "activo",
        ]
      ]);
    }

    public function test_grupo_crud_update(){
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo update",
        "activo" => 1
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "nombre",
          "activo",
        ]
      ]);

      $grupoUpdate = Grupo::find($this->grupo->id);
      $this->assertEquals($grupoUpdate->nombre,"Grupo update");
    }

    public function test_grupo_crud_delete(){
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "nombre",
          "activo",
        ]
      ]);

      // Nos aseguramos que el dato borrado haya borrado en cascada
      // ya que si se hace un list en los otros modelos ya no tienen grupo con el que estar relacionados
      $grupoDelete = Grupo::find($this->grupo->id);
      $this->assertEquals($grupoDelete,NULL);

      $EPGDelete = EspecialidadPeriodoGrupo::where("grupo_id",$this->grupo->id)->get();
      $this->assertEmpty($EPGDelete);
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

      $this->grupo = Grupo::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory())
        ->create();

      $materia = Materia::factory()
        ->for(Componente::factory())
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->create();
    }
}