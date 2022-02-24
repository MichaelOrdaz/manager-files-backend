<?php
/**
 * @author Enrique Sevilla <sevilla@puller.mx>
 * @version  1.0
 *
 * NOTAS :
 * con assertJsonStructure pueden traer mas datos pero no menos
 *
 * si se necesita ver los errores sin el handler agregar:
 *  $this->withoutExceptionHandling();
 * en la primera linea de tu función
 *
 * Sirve para hacer debug cuando se hace una petición al api
 * $response->dump();
 */
namespace Tests\Feature\Docente;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Estado;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\Municipio;
use App\Models\Componente;
use App\Models\ExamenTema;
use App\Models\ExamenTipo;
use App\Models\TareaGrupo;
use App\Models\AlumnoGrupo;
use App\Models\Conferencia;
use App\Models\ExamenGrupo;
use App\Models\Especialidad;
use App\Models\MaterialTipo;
use App\Models\TareaEnviada;
use App\Models\DatosGenerales;
use App\Models\DocenteMateria;
use App\Models\AsistenciaAlumno;
use App\Models\MaterialDidactico;
use App\Models\ExamenCalificacion;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use App\Models\ExamenCalificacionStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocenteEnpointsTest extends TestCase
{
  use DatabaseTransactions;

  protected $epg,$docente,$alumnos,$conferencia,$materia,$tarea,$examen,$tutoria,$asistencia;

  public function test_docente_endpoint_post_bind_materia()
  {
    $response = $this->post(
      'api/v1/docentes/'.$this->docente->id.
      '/materias/'.$this->materia->id.':bind',
      [
        'especialidad_id' => $this->epg->EspecialidadPeriodo->especialidad_id,
        'periodo_id' => $this->epg->EspecialidadPeriodo->periodo_id,
        'grupo_id' =>  $this->epg->grupo_id,
      ]
    );
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' =>[
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'materia',
        'periodo',
        'grupo',
        'especialidad',
      ]
    ]);

    $response = $this->post(
      'api/v1/docentes/'.$this->docente->id.
      '/materias/'.$this->materia->id.':bind',
      [
        'especialidad_id' => $this->epg->EspecialidadPeriodo->especialidad_id,
        'periodo_id' => $this->epg->EspecialidadPeriodo->periodo_id,
        'grupo_id' =>  $this->epg->grupo_id,
      ]
    );
    $response->assertStatus(422);

  }

  public function test_docente_endpoint_post_unbind_materia()
  {
    $this->post(
      'api/v1/docentes/'.$this->docente->id.
      '/materias/'.$this->materia->id.':bind',
      [
        'especialidad_id' => $this->epg->EspecialidadPeriodo->especialidad_id,
        'periodo_id' => $this->epg->EspecialidadPeriodo->periodo_id,
        'grupo_id' =>  $this->epg->grupo_id,
      ]
    );
    $response = $this->post(
      'api/v1/docentes/'.$this->docente->id.
      '/materias/'.$this->materia->id.':unbind',
      [
        'especialidad_id' => $this->epg->EspecialidadPeriodo->especialidad_id,
        'periodo_id' => $this->epg->EspecialidadPeriodo->periodo_id,
        'grupo_id' =>  $this->epg->grupo_id,
      ]
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'materia',
        'periodo',
        'grupo',
        'especialidad',
      ]
    ]);
  }

  public function test_docente_endpoint_get_listGrupos()
  {
    DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();

    $response = $this->get(
      'api/v1/docentes/'. $this->docente->id .
      '/materias/'. $this->materia->id .'/grupos'
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'especialidad_periodo_grupo_id',
          'especialidad_periodo_id',
          'especialidad',
          'periodo',
          'grupo',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_get_listCalificarTareaAlumnos()
  {
    $response = $this->get('api/v1/docentes/*/calificar-tarea/'.$this->tarea->id);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'email',
          'nombre_completo',
          'especialidad_id',
          'periodo_id',
          'grupo_id',
          'tarea_id',
          'tarea',
          'tarea_enviada_id',
          'tarea_enviada',
          'materia_id',
          'materia',
          'unidades',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_post_createUpdateCalificarTareaAlumnos()
  {
    $response = $this->post(
      'api/v1/docentes/*/calificar-tarea/'.$this->tarea->id.'/alumnos/'.$this->alumnos->first()->id,
      [ 'materia_id' => $this->materia->id ]
    );
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' =>[
        'id',
        'usuario_id',
        'usuario',
        'tarea_id',
        'temas',
        'materia_id',
        'materia',
        'nota',
        'calificacion',
        'observaciones',
        'archivo_url',
      ]
    ]);
  }

  public function test_docente_endpoint_getDocenteMateria()
  {
    DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();
    $response = $this->get('api/v1/docentes/'.$this->docente->id.'/materias/'.$this->materia->id);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        'id',
        'usuario_id',
        'usuario',
        'especialidad_periodo_grupo_id',
        'grupo_id',
        'grupo',
        'materia_id',
        'materia',
      ]
    ]);
  }

  public function test_docente_endpoint_get_listCalificarExamenAlumnos()
  {
    $response = $this->get('api/v1/docentes/*/calificar-examenes/'.$this->examen->id);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'email',
          'nombre_completo',
          'especialidad_id',
          'periodo_id',
          'grupo_id',
          'grupo',
          'examen_id',
          'examen',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_getTareasExamenesCalendario()
  {
    DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();
    $response = $this->get(
      'api/v1/docentes/'.$this->docente->id.
      '/calendario?materiaId='.$this->materia->id
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'tipo',
          'title',
          'color',
          'start',
          'end',
          'icon',
          'grupo',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_listAlumnosGrupo()
  {
    $response = $this->get('api/v1/docentes/*/alumnos-grupo/' . $this->epg->id);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'email',
          'firebase_uid',
          'nombre_completo',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_post_createUpdateAsistenciasGrupo()
  {
    $alumnos = $this->alumnos->toArray();

    $response = $this->post('api/v1/docentes/'. $this->docente->id .'/pase-lista',[
      'paseLista' => [
        [
          "alumno_id" => $alumnos[0]['id'],
          "conferencia_id" => $this->conferencia->id,
          "asistencia" => "true"
        ],
        [
          "alumno_id" => $alumnos[1]['id'],
          "conferencia_id" => $this->conferencia->id,
          "asistencia" => "true"
        ],
        [
          "alumno_id" => $alumnos[2]['id'],
          "conferencia_id" => $this->conferencia->id,
          "asistencia" => "false"
        ],
      ]
    ]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'alumno_id',
          'alumno',
          'docente_id',
          'docente',
          'conferencia_id',
          'conferencia',
          'asistencia',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_put_createUpdateAsistenciaAlumno()
  {
    $alumno = $this->alumnos->first();
    $response = $this->put(
      'api/v1/docentes/'. $this->docente->id .'/pase-lista/*/alumnos/' . $alumno->id,
      [
        'alumno_id' => $alumno->id ,
        'conferencia_id' => $this->conferencia->id,
        'asistencia' => 1,
      ]);
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' =>[
        'id',
        'alumno_id',
        'alumno',
        'docente_id',
        'docente',
        'conferencia_id',
        'conferencia',
        'asistencia',
      ]
    ]);
  }

  public function test_docente_endpoint_get_searchAsistenciaAlumno()
  {
    $alumno = $this->alumnos->first();
    $response = $this->get(
      'api/v1/docentes/'. $this->docente->id .'/pase-lista:search?' .
      'docenteId=' . $this->docente->id .
      '&alumnoId=' . $alumno->id .
      '&conferenciaId=' . $this->conferencia->id .
      '&especialidadPeriodoGrupoId=' . $this->epg->id
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'alumno_id',
          'alumno',
          'docente_id',
          'docente',
          'conferencia_id',
          'conferencia',
          'asistencia',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_getMateriasGrupo()
  {
    DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();
    $response = $this->get('api/v1/docentes/'.$this->docente->id.'/materias');
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'nombre',
          'materia_id',
          'grupo_id',
          'especialidad_periodo_grupo_id',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_getTablaCalificacion()
  {
    $docenteMateria = DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();
    $response = $this->get('api/v1/docentes/*/tabla-calificacion/'.$docenteMateria->id);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        'columnas',
        'filas',
      ]
    ]);
  }

  public function test_docente_endpoint_getTablaCalificacionDetalleAlumno()
  {
    $docenteMateria = DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();

    $response = $this->get(
      'api/v1/docentes/*/tabla-calificacion/' . $docenteMateria->id .
      '/alumno/' . $this->alumnos->first()->id
    );

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        'alumno',
        'actividades',
      ]
    ]);
  }

  public function test_docente_endpoint_getTablaRubrica()
  {
    $docenteMateria = DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();
    $response = $this->get(
      'api/v1/docentes/*/tabla-rubrica/' . $docenteMateria->id
    );

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'nombre_completo',
          'tareas',
          'examenes',
          'asistencia',
          'examen_extraordinario',
          'titulo_suficiencia',
          'total',
        ]
      ]
    ]);
  }

  public function test_docente_endpoint_post_updateCalificacionMisActividades()
  {
    // Testeando Tarea
    $response = $this->put('api/v1/docentes/*/alumnos/*/mis-actividades/' .
      $this->tarea->tareasEnviada->first()->id,[
        'calificacion' => 10,
        'asignado' => 'Tarea',
    ]);
    $tareaEnviada = TareaEnviada::find($this->tarea->tareasEnviada->first()->id);
    $this->assertEquals($tareaEnviada->calificacion,10);

    // Testeando Examen
    $response = $this->put('api/v1/docentes/*/alumnos/*/mis-actividades/' .
      $this->examen->examenCalificacion->first()->id,[
        'calificacion' => $this->examen->puntaje_minimo,
        'asignado' => 'Examen',
    ]);
    $examenCalificacion = ExamenCalificacion::find($this->examen->examenCalificacion->first()->id);
    $this->assertEquals($examenCalificacion->calificacion_obtenida,$this->examen->puntaje_minimo);

    // Testeando Tutoria
    $response = $this->put('api/v1/docentes/*/alumnos/*/mis-actividades/' .
      $this->tutoria->id,[
        'calificacion' => 'Algo que decir',
        'asignado' => 'Tutoria',
    ]);
    $tutoria = Tutoria::where([
      'respuesta_tutoria_id' => $this->tutoria->id,
      'descripcion' => 'Algo que decir',
    ])->first();
    $this->assertTrue($tutoria != null);

    // Asistencia
    $response = $this->put('api/v1/docentes/*/alumnos/*/mis-actividades/' .
      $this->asistencia->id,[
        'calificacion' => true,
        'asignado' => 'Asistencia',
    ]);
    $asistencia = AsistenciaAlumno::find($this->asistencia->id);
    $this->assertEquals($asistencia->asistencia,true);

  }

  public function test_docentes_especialidades_periodos_grupos()
  {  
    DocenteMateria::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg, 'especialidad_periodo_grupo')
    ->create();

    $response = $this->get("api/v1/docentes/{$this->docente->id}/especialidades-periodos-grupos");
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'grupo_id',
          'grupo',
          'especialidad_id',
          'especialidad',
          'periodo_id',
          'periodo',
          'generacion',
          'numero_alumnos',
          'numero_materias',
          'nombre',
        ]
      ]
    ]);
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
    $this->docente = User::factory()->create();
    $this->docente->assignRole('Docente');
    $this->actingAs($this->docente, 'api');

    $especialidadPeriodo = EspecialidadPeriodo::factory()
    ->for(Periodo::factory())
    ->for(Especialidad::factory())
    ->create();

    $this->materia = Materia::factory()
    ->for(Componente::factory())
    ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
    )
    ->create();

    $grupo = Grupo::factory()->create();

    $this->epg = EspecialidadPeriodoGrupo::factory()
    ->for($especialidadPeriodo, 'EspecialidadPeriodo')
    ->for($grupo)
    ->create();

    $unidad = Unidad::factory()->for($this->materia)->create();
    $tema = Tema::factory()->for($unidad)->create();
    $this->conferencia = Conferencia::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->for($this->epg)
    ->for($unidad)
    ->for($tema)
    ->create();

    $this->alumnos = User::factory()->count(3)->create();
    foreach($this->alumnos as $alumno)
    {

      DatosGenerales::factory()
      ->for(Municipio::factory()->for(Estado::factory()))
      ->for(Estado::factory())
      ->for($alumno,'Usuario')
      ->create();

      AlumnoGrupo::factory()
      ->for($alumno)
      ->for($this->epg)
      ->create();

      $this->asistencia = AsistenciaAlumno::factory()
      ->for($alumno, 'Alumno')
      ->for($this->docente, 'Docente')
      ->for($this->conferencia)
      ->create();
    }

    $this->tarea = Tarea::factory()
    ->for($this->docente,'creador')
    ->for($unidad)
    ->for($this->materia)
    ->has(
      TareaEnviada::factory()
      ->for($this->alumnos->first())
      ->for($this->materia)
      ,'tareasEnviada'
    )
    ->create();

    TareaGrupo::factory()
    ->for($this->epg)
    ->for($this->tarea)
    ->create();

    $tema = Tema::factory()->for(Unidad::factory()->for($this->materia))->create();
    $this->examen = Examen::factory()
    ->for($this->docente)
    ->for($this->materia)
    ->has(
      ExamenTema::factory()
      ->for($tema)
    )
    ->for(ExamenTipo::factory())
    ->create();

    ExamenGrupo::factory()
    ->for($this->epg)
    ->for($this->examen)
    ->create();

    ExamenCalificacion::factory()
    ->for($this->alumnos->first(), 'usuario')
    ->for(ExamenCalificacionStatus::factory(), 'status')
    ->for($this->examen)
    ->create();

    $material = MaterialDidactico::factory()
    ->for($this->docente, 'creador')
    ->for(MaterialTipo::factory(),'MaterialesTipo')
    ->for($tema, 'tema')
    ->create();

    $this->tutoria = Tutoria::factory()
    ->for($this->alumnos->first())
    ->for($material, 'material')
    ->for($tema, 'tema')
    ->for($this->materia)
    ->for($grupo)
    ->create();

  }


}