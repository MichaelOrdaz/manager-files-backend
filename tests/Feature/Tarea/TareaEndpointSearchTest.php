<?php

namespace Tests\Feature\Tarea;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\TareaTema;
use App\Models\Componente;
use App\Models\TareaGrupo;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TareaEndpointSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tarea_endpoint_search()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $grupo = Grupo::factory()->create();
      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();
      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($periodo)
        ->for($especialidad)
        ->create();

      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for($grupo)
      ->create();

      $materia = Materia::factory()
        ->for(Componente::factory())
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->create();

      $unidad = Unidad::factory()->for($materia)->create();

      $tarea = Tarea::factory()
        ->for($unidad)
        ->for($materia)
        ->forCreador()
        ->create();

      TareaGrupo::factory()
        ->for($especialidadPeriodoGrupo)
        ->for($tarea)
        ->create();

      $tema = Tema::factory()->for($unidad)->create();
      TareaTema::factory()
      ->for($tarea)
      ->for($tema)
      ->create();

      $response = $this->get(
        'api/v1/tareas:search?page=1&&nombre=' . $tarea->titulo .
        '&&unidadId=' . $unidad->id .
        '&&materiaId=' . $materia->id .
        '&&periodoIdByGrupo=' . $periodo->id .
        '&&periodoIdByMateria=' . $periodo->id .
        '&&temaId=' . $tema->id
      );
      $response->assertStatus(200);
      $response->assertJsonFragment(['titulo' => $tarea->titulo]);
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'titulo',
          'descripcion',
          'creador_id',
          'usuario',
          'unidad_id',
          'unidad',
          'materia_id',
          'materia',
          'archivo_url',
        ]]
      ]);

    }



}
