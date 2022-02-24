<?php

namespace Tests\Feature\Periodo;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use App\Models\Grupo;
use App\Models\Materia;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Periodo;
use App\Models\User;

class PeriodoCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_periodo_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      Periodo::factory()->count(3)->create();

      $response = $this->get("api/v1/periodos");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
          ]
        ],
        "success",
      ]);
    }

    public function test_periodo_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();

      $response = $this->get("api/v1/periodos/{$periodo->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
        ],
        "success",
      ]);
    }

    public function test_periodos_search_empty()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()
      ->count(10)
      ->create();

      $response = $this->get("api/v1/periodos:search");

      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'especialidades',
          'materias',
        ]],
        "success",
        "message",
      ]);

    }

    public function test_periodos_bind()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $response = $this->post("api/v1/periodos:bind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
      ]);

      $response->assertStatus(201);
    }

    public function test_periodos_unbind_no_exist()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $response = $this->post("api/v1/periodos:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
      ]);

      $response->assertStatus(404);
    }

    public function test_periodos_unbind_ok()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $response = $this->post("api/v1/periodos:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
      ]);

      $response->assertStatus(200);

      $response->assertJsonFragment([
        'message' => 'Se ha desvinculado la relación eliminando sus relaciones.'
      ]);
    }

    public function test_periodos_unbind_con_materias()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadperiodo, 'especialidad_periodo')
      ->create();

      $response = $this->post("api/v1/periodos:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
      ]);

      $response->assertStatus(422);
      $response->assertJsonFragment([
        'errors' => [
          'No se puede eliminar, tiene materias y/o grupos asociados'
          ]
      ]);
    }

    public function test_periodos_unbind_con_grupo()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      EspecialidadPeriodoGrupo::factory()
      ->for($especialidadperiodo, 'EspecialidadPeriodo')
      ->for(Grupo::factory())
      ->create();

      $response = $this->post("api/v1/periodos:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
      ]);

      $response->assertStatus(422);
      $response->assertJsonFragment([
        'errors' => [
          'No se puede eliminar, tiene materias y/o grupos asociados'
          ]
      ]);
    }

    public function test_periodos_unbind_con_materias_grupo()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      EspecialidadPeriodoGrupo::factory()
      ->for($especialidadperiodo, 'EspecialidadPeriodo')
      ->for(Grupo::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadperiodo, 'especialidad_periodo')
      ->create();

      $response = $this->post("api/v1/periodos:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
      ]);

      $response->assertStatus(422);
      $response->assertJsonFragment([
        'errors' => [
          'No se puede eliminar, tiene materias y/o grupos asociados'
          ]
      ]);
    }



}