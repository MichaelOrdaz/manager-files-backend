<?php

namespace Tests\Feature\CalificacionParcial;

use Tests\TestCase;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\AlumnoGrupo;
use App\Models\Especialidad;
use Illuminate\Http\UploadedFile;
use App\Models\CalificacionParcial;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalifiacionParcialEndpoinsTest extends TestCase
{
    use DatabaseTransactions;

    protected $calificacionParcial,$materia,$epg,$alumno;

    public function test_califacion_parcial_list_alumnos()
    {
      $response = $this->get(
        'api/v1/califacion-parcial/especialidad-periodo-grupo/' .
        $this->epg->id.'/materias/' .
        $this->materia->id.'/alumnos'
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'nombre_completo',
          'matricula',
          'calificacion_parcial',
        ]]
      ]);
    }

    public function test_califacion_parcial_create_or_update()
    {
      $response = $this->post(
        'api/v1/califacion-parcial/especialidad-periodo-grupo/' .
        $this->epg->id.'/materias/' .
        $this->materia->id .
        '/alumnos/' . $this->alumno->id,
      [
        'usuario_id' => $this->alumno->id,
        'materia_id' => $this->materia->id,
        'parcial_uno' => 10,
        'calificacion_semestral' => 3,
        'calificacion_letra' => 'Tres',
        'especialidad_periodo_grupo_id' => $this->epg->id,
        'asistencia_total' => 19,
        'porcentaje' => 100,
        'ciclo_escolar' => '2021-2022',
      ]);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'materia_id',
          'materia_nombre',
          'asistencia_total',
          'porcentaje',
          'parcial_uno',
          'parcial_dos',
          'parcial_tres',
          'suma',
          'calificacion_semestral',
          'calificacion_letra',
          'observaciones',
          'especialidad_periodo_grupo_id',
          'especialidad_nombre',
          'periodo_nombre',
          'grupo_nombre',
          'ciclo_escolar',
        ]
      ]);
    }

    public function setUp():void
    {
      parent::setUp();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $grupo = Grupo::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $this->epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $this->materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadPeriodo,'especialidad_periodo')
      ->create();

      $this->alumno = User::factory()->create();

      AlumnoGrupo::factory()
      ->for($this->alumno)
      ->for($this->epg)
      ->create();

      $this->calificacionParcial = CalificacionParcial::factory()
      ->ponerMateriaNombre($this->materia->nombre)
      ->ponerEspecialidadPeriodoGrupo($this->epg)
      ->for($this->epg)
      ->for($this->materia)
      ->for($this->alumno)
      ->create();

    }
}
