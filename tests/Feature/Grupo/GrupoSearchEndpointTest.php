<?php

namespace Tests\Feature\Grupo;

use Tests\TestCase;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\AlumnoGrupo;
use App\Models\Especialidad;
use App\Models\AspiranteStatus;
use App\Models\DatosAcademicos;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrupoSearchEndpointTest extends TestCase
{
    use DatabaseTransactions;

    public function test_grupo_endpoint_search()
    {
      $user = User::factory()
      ->has(
        DatosAcademicos::factory()
        ->for(AspiranteStatus::find(1),'Status')
        ->state(['generacion' => Date('Y') ])
      )
      ->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();
      $grupo = Grupo::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for($grupo)
      ->create();

      AlumnoGrupo::factory()
      ->for($epg)
      ->for($user)
      ->create();

      $response = $this->get(
        '/api/v1/grupos:search?grupoId=' . $grupo->id .
        '&&especialidadId=' . $especialidad->id .
        '&&periodoId=' . $periodo->id .
        '&&generacion' . Date('Y')
      );

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

}