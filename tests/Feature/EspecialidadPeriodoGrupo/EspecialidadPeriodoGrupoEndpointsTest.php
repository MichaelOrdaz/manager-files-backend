<?php

namespace Tests\Feature\EspecialidadPeriodoGrupo;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\ExamenTipo;
use App\Models\TareaGrupo;
use App\Models\AlumnoGrupo;
use App\Models\ExamenGrupo;
use App\Models\Especialidad;
use App\Models\PreguntaTipo;
use App\Models\TareaEnviada;
use App\Models\DocenteMateria;
use App\Models\ExamenPregunta;
use App\Models\DatosAcademicos;
use App\Models\ExamenRespuesta;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EspecialidadPeriodoGrupoEndpointsTest extends TestCase
{
  use DatabaseTransactions;

  protected $epg,$especialidad,$periodo,$grupo, $materias, $alumnosGrupo;

  public function test_especialidad_periodo_grupo_get()
  {
    $response = $this->get('api/v1/especialidad-periodo-grupo/' . $this->epg->id );
    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'grupo_id',
          'grupo',
          'especialidad_id',
          'especialidad',
          'periodo_id',
          'periodo',
          'generacion',
        ]
      ]);
  }

  public function test_especialidad_periodo_grupo_search()
  {
    $response = $this->get(
      'api/v1/especialidad-periodo-grupo:search?especialidadId=' . $this->especialidad->id .
      '&&periodoId='. $this->periodo->id .
      '&&grupoId='. $this->grupo->id .
      '&&generacion=4'
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        [
          'id',
          'grupo_id',
          'grupo',
          'especialidad_id',
          'especialidad',
          'periodo_id',
          'periodo',
          'generacion',
        ]
      ]
    ]);
    $response->assertJsonFragment(['generacion' => 4]);
    $data = json_decode($response->getContent())->data[0];
    $this->assertEquals($data->especialidad->id,$this->especialidad->id);
    $this->assertEquals($data->periodo->id,$this->periodo->id);
    $this->assertEquals($data->grupo->id,$this->grupo->id);
  }

  public function test_especialidad_periodo_grupo_delete()
  {
    // Tareas grupo
    $tareaGrupo = TareaGrupo::factory()
    ->for($this->epg)
    ->for(
      Tarea::factory()
      ->for(User::factory(),'creador')
      ->for(Unidad::factory()->for($this->materias[0]))
      ->has(
        TareaEnviada::factory()
        ->forUser()
        ->for($this->materias[0])
        ,'tareasEnviada'
      )
    )
    ->create();
    $this->assertInstanceOf(TareaEnviada::class, $tareaGrupo->tareasEnvidadas->first());

    // Materias grupo
    foreach($this->materias as $materia){
      DocenteMateria::factory()
      ->forUser()
      ->for($materia)
      ->for($this->epg, 'especialidad_periodo_grupo')
      ->create();
    }

    $docentesMaterias = DocenteMateria::
      where(['especialidad_periodo_grupo_id' => $this->epg->id])
      ->get();

    // Examen Grupo
    $examen = Examen::factory()
    ->for(User::factory())
    ->for(ExamenTipo::factory())
    ->create();

    $examenGrupo = ExamenGrupo::factory()
    ->for($this->epg)
    ->for($examen)
    ->create();

    $examenPregunta = ExamenPregunta::factory()
    ->for(User::factory())
    ->for($examen)
    ->for(PreguntaTipo::factory())
    ->create();

    $examenRespuesta = ExamenRespuesta::factory()
    ->for(User::factory())
    ->for($examenPregunta)
    ->for($examen)
    ->create();

    $this->assertInstanceOf(ExamenGrupo::class, $this->epg->ExamenesGrupo->first() );
    $this->assertEquals($examenGrupo->id,$this->epg->ExamenesGrupo->first()->id);

    $response = $this->delete('api/v1/especialidad-periodo-grupo/' . $this->epg->id );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'grupo_id',
        'grupo',
        'especialidad_id',
        'especialidad',
        'periodo_id',
        'periodo',
        'generacion',
      ]
    ]);

    // Eliminado de tareas enviadas
    $this->assertSoftDeleted($tareaGrupo);
    $this->assertDatabaseHas('tareas', ['titulo' => $tareaGrupo->tarea->titulo]);
    foreach($tareaGrupo->tareasEnvidadas as $tareaEnviada){
      $this->assertSoftDeleted($tareaEnviada);
    }

    // Eliminado de relación alumno grupo sin eliminar alumnos
    $this->assertDatabaseHas('users', ['email' => $this->alumnosGrupo->User->email]);
    $this->assertSoftDeleted($this->alumnosGrupo);

    // Eliminando la relación docente materia, sin eliminar docente ni materia
    foreach($docentesMaterias as $docenteMateria){
      $this->assertSoftDeleted($docenteMateria);
      $this->assertDatabaseHas('materias', ['nombre' => $docenteMateria->materia->nombre ]);
      $this->assertDatabaseHas('users', ['email' => $docenteMateria->User->email ]);
    }

    // Eliminado de examenes respuestas, sin eliminar al examen
    $this->assertDatabaseHas('examenes', ['nombre' => $examen->nombre]);
    $this->assertSoftDeleted($this->epg->ExamenesGrupo->first());
    $this->assertSoftDeleted($examenRespuesta);
  }

  public function test_especialidad_periodo_grupo_get_alumnos()
  {
    $response = $this->get(
      'api/v1/especialidad-periodo-grupo/' . $this->epg->id .
      '/alumnos/*'
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        [
          'id',
          'alumno_email',
          'datos_generales',
          'grupo_id',
          'especialidad_id',
          'periodo_id',
        ]
      ]
    ]);

  }

  public function test_especialidad_periodo_grupo_materias()
  {
    $user = User::factory()->create();
    $user->assignRole('Control escolar');
    $this->actingAs($user, 'api');

    $response = $this->get(
      'api/v1/especialidad-periodo-grupo/' . $this->epg->id .
      '/materias'
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        [
          'id',
          'nombre',
          'descripcion',
          'requisitos',
          'especialidad_periodo_id',
          'componente_id',
          'imagen_url',
          'activo',
          'grupo_id',
          'grupo',
          'especialidad_periodo_grupo_id'
        ]
      ]
    ]);

  }

  public function setUp(): void
  {
    parent::setUp();

    $usuario = User::factory()->create();
    $usuario->assignRole('Departamento de docentes');
    $this->actingAs($usuario, 'api');

    $this->periodo = Periodo::factory()->create();
    $this->especialidad = Especialidad::factory()->create();

    $especialidadPeriodo = EspecialidadPeriodo::factory()
    ->for($this->periodo)
    ->for($this->especialidad)
    ->create();

    $this->grupo = Grupo::factory()->create();

    $this->epg = EspecialidadPeriodoGrupo::factory()
    ->for($especialidadPeriodo)
    ->for($this->grupo)
    ->create();

    $this->alumnosGrupo = AlumnoGrupo::factory()
    ->for(
      User::factory()->has(DatosAcademicos::factory()->state(['generacion' => 4]))
    )
    ->for($this->epg)
    ->create();

    $this->materias = Materia::factory()
    ->for(Componente::factory())
    ->for($especialidadPeriodo,'especialidad_periodo')
    ->count(5)
    ->create();
  }
}

