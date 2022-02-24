<?php

namespace Tests\Feature\Tarea;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TareaBindGrupoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tarea_endpoint_grupo_bind()
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

      EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for($grupo)
      ->create();

      $materia = Materia::factory()
        ->for(Componente::factory())
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->create();

      $tarea = Tarea::factory()
        ->for(
          Unidad::factory()->for($materia)
        )
        ->for($materia)
        ->forCreador()
        ->create();


      $response = $this->post(
        "api/v1/tareas/". $tarea->id ."/grupos/". $grupo->id .":bind",
      [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
        'fecha_limite' => Date('Y-m-d H:i:s'),
      ]);
      $response->assertStatus(201);
      $response->assertJsonFragment(['titulo' => $tarea->titulo]);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'titulo',
          'descripcion',
          'grupos',
          'creador_id',
          'creador',
          'usuario_id',
          'usuario',
          'unidad_id',
          'unidad',
          'materia_id',
          'materia',
          'archivo_url',
        ]
      ]);

    }



}