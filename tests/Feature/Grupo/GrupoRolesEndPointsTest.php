<?php

namespace Tests\Feature\Grupo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrupoRolesEndPointsTest extends TestCase
{
    use DatabaseTransactions;

    protected $grupo;
    protected $usuario;
    protected $periodo;

    public function test_grupo_endpoints_para_alumno()
    {
      //Create
      $this->usuario->syncRoles(['Alumno']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // list
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // get
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // update
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(403);
    }

    public function test_grupo_endpoints_para_admin()
    {
      // create
      $this->usuario->syncRoles(['Admin']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(201);

      // list
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // get
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // update
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(200);

      // delete
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);
    }


    public function test_grupo_endpoints_para_control_escolar()
    {
      // Create
      $this->usuario->syncRoles(['Control Escolar']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(201);

      // List
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // Get
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // Update
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(200);

      // Delete
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);
    }

    public function test_grupo_endpoints_para_departamente_docentes()
    {
      // Create
      $this->usuario->syncRoles(['Departamento de docentes']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(201);

      // List
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // GET
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // UPDATE
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(200);

      // DELETE
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);
    }

    public function test_grupo_endpoints_para_docente()
    {
      // CREATE
      $this->usuario->syncRoles(['Docente']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // LIST
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // GET
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // UPDATE
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // DELETE
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(403);
    }

    public function test_grupo_endpoints_para_prefecto()
    {
      // CREATE
      $this->usuario->syncRoles(['Prefecto']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // LIST
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // GET
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // UPDATE
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // DELETE
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(403);
    }

    public function test_grupo_endpoints_para_padre_familia()
    {
      // Create
      $this->usuario->syncRoles(['Padre de familia']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // List
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(200);

      // Get
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(200);

      // Update
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // Delete
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(403);
    }

    public function test_grupo_endpoints_para_aspirante()
    {
      // Create
      $this->usuario->syncRoles(['Aspirante a ingreso']);
      $response = $this->post("/api/v1/grupos",[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // List
      $response = $this->get("/api/v1/grupos");
      $response->assertStatus(403);

      // Get
      $response = $this->get("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(403);

      // Update
      $response = $this->put("/api/v1/grupos/".$this->grupo->id,[
        "nombre" => "Grupo A",
        "periodo_id" => $this->periodo->id,
        "activo" => 1
      ]);
      $response->assertStatus(403);

      // Delete
      $response = $this->delete("/api/v1/grupos/".$this->grupo->id);
      $response->assertStatus(403);
    }

    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * Primer función a ejecutar del test,
     * creamos nuestras entidades que vamos a usar para este test
     */
    public function setUp():void
    {
      parent::setUp();

      $this->usuario = User::create([
        'email' => 'admin_test@puller.mx',
        'password' => Hash::make('12345'),
        'activo' =>   true,
        'firebase_uid' => 'kJK1CRjkci9mCY7CuN',
        'email_verified_at' => Carbon::now(),
      ]);
      //uso ese usuario como el usuario autenticado en cada petición
      $this->actingAs($this->usuario, 'api');

      $this->periodo = Periodo::create([
        'nombre' => 'periodo_test',
      ]);

      $this->grupo = Grupo::create([
        "nombre" => "grupo_test",
        "periodo_id" => $this->periodo->id,
        "activo" => true,
      ]);

    }


}