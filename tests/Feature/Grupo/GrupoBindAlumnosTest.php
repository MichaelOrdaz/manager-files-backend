<?php

namespace Tests\Feature\Grupo;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrupoBindAlumnosTest extends TestCase
{
  use DatabaseTransactions;

  public function test_endpoint_grupos_bind_alumnos()
  {
    $user = User::factory()->create();
    $user->assignRole('Departamento de docentes');
    $this->actingAs($user, 'api');

    $periodo = Periodo::factory()->create();
    $especialidad = Especialidad::factory()->create();

    $especialidadPeriodo = EspecialidadPeriodo::factory()
    ->for($periodo)
    ->for($especialidad)
    ->create();

    $grupo = Grupo::factory()->create();

    $epg = EspecialidadPeriodoGrupo::factory()
    ->for($especialidadPeriodo)
    ->for($grupo)
    ->create();

    $alumnos = User::factory()->count(1)->create();
    $response = $this->post('api/v1/grupos/'. $grupo->id .'/alumnos/*:bind', [
      'periodo_id' => $periodo->id,
      'especialidad_id'=> $especialidad->id,
      'alumnos' => $alumnos->pluck('id'),
    ]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' => [
        'grupo_id',
        'periodo_id',
        'especialidad_id',
        'alumnos',
      ]
    ]);

  }

  public function test_endpoint_grupo_bind_alumno()
  {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $user->assignRole('Departamento de docentes');
    $this->actingAs($user, 'api');

    $periodo = Periodo::factory()->create();
    $especialidad = Especialidad::factory()->create();

    $especialidadPeriodo = EspecialidadPeriodo::factory()
    ->for($periodo)
    ->for($especialidad)
    ->create();

    $grupo = Grupo::factory()->create();

    EspecialidadPeriodoGrupo::factory()
    ->for($especialidadPeriodo)
    ->for($grupo)
    ->create();

    $alumno = User::factory()->create();
    $response = $this->post(
      'api/v1/grupos/'. $grupo->id .
      '/alumnos/'.$alumno->id.':bind',
    [
      'periodo_id' => $periodo->id,
      'especialidad_id'=> $especialidad->id,
      'alumnos' => $alumno->id,
    ]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' => [
        'grupo_id',
        'periodo_id',
        'especialidad_id',
        'alumno_id',
      ]
    ]);

  }


}