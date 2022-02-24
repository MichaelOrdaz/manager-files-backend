<?php
/**
 * @author Enrique Sevilla <sevilla@puller.mx>
 * @version  1.0
 * Test para endpoint aspirantes, prueba el registro de usuarios
 * aspirantes creando sus datos académicos
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

namespace Tests\Feature\Alumnos;

use Tests\TestCase;

use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\TareaTema;
use App\Models\Componente;
use App\Models\ExamenTema;
use App\Models\ExamenTipo;
use App\Models\TareaGrupo;
use App\Models\AlumnoGrupo;
use App\Models\ExamenGrupo;
use App\Models\Especialidad;
use App\Models\TareaEnviada;
use Illuminate\Http\UploadedFile;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlumnosSpecialEndpointsTest extends TestCase
{
  use DatabaseTransactions;

  protected $usuario,$tarea,$tareaEnviada,$materia,$tema,$examen;

  public function test_endpoint_alumno_tareas_list()
  {
    $response = $this->get(
      'api/v1/alumnos/'.$this->usuario->id.'/tareas:search?' .
      'materiaId='. $this->materia->id .
      '&temaId=' . $this->tema->id
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        [
          'id',
          'titulo',
          'descripcion',
          'fecha_limite',
          'archivo_url',
          'materia_id',
          'materia',
          'temas',
          ]
        ],
        'success'
      ]);
    $response->assertJsonFragment(['materia_id' => $this->materia->id]);
    $response->assertJsonFragment(['id' => $this->tema->id]);

    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas:search?materiaId=0');
    $response->assertExactJson([
      'data' => [],
      'message' => 'Tareas se recuperaron correctamente.',
      'success' => true,
    ]);

    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas:search?temaId=0');
    $response->assertExactJson([
      'data' => [],
      'message' => 'Tareas se recuperaron correctamente.',
      'success' => true,
    ]);
  }

  public function test_endpoint_alumno_tareas_enviadas_list()
  {
    $response = $this->get(
      'api/v1/alumnos/'. $this->usuario->id .
      '/tareas/*/tareas-enviadas:search?' .
      'materiaId=' . $this->materia->id .
      '&temaId=' . $this->tema->id
    );
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        [
          'id',
          'titulo',
          'descripcion',
          'fecha_limite',
          'archivo_url',
          'status_tarea_enviada',
          'materia_id',
          'materia',
          'temas',
        ]
      ],
      'success'
    ]);
    $response->assertJsonFragment(['materia_id' => $this->materia->id]);
    $response->assertJsonFragment(['id' => $this->tema->id]);
    $response->assertJsonFragment(['status_tarea_enviada' => '5.00']);

    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas/*/tareas-enviadas:search?materiaId=0');
    $response->assertExactJson(['data' => [],'message' => 'Tareas se recuperaron correctamente.','success' => true]);

    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas/*/tareas-enviadas:search?temaId=0');
    $response->assertExactJson(['data' => [],'message' => 'Tareas se recuperaron correctamente.','success' => true]);

    TareaEnviada::find($this->tareaEnviada->id)->update(['calificacion'=> null ]);
    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas/*/tareas-enviadas:search');
    $response->assertJsonFragment(['status_tarea_enviada' => 'Enviada']);

    TareaEnviada::find($this->tareaEnviada->id)->delete();
    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas/*/tareas-enviadas:search');
    $response->assertJsonFragment(['status_tarea_enviada' => 'No enviada']);
  }

  public function test_endpoint_alumno_tareas_get()
  {
    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/tareas/'.$this->tarea->id);
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'fecha_limite',
        'titulo',
        'descripcion',
        'creador_id',
        'creador',
        'usuario_id',
        'usuario',
        'unidad_id',
        'unidad',
        'archivo_url',
        'temas',
        'materia_id',
        'materia',
        'tarea_enviada',
      ],
      'success'
    ]);
  }

  public function test_endpoint_alumno_tareas_enviadas_create()
  {
    $this->withoutExceptionHandling();
    $file = UploadedFile::fake()->image('avatar.jpg');
    $response = $this->post(
      'api/v1/alumnos/'. $this->usuario->id .
      '/tareas/' . $this->tarea->id .
      '/tareas-enviadas',
      [
        'nota' => 'nota tarea enviada',
        'usuario_id' => $this->usuario->id,
        'materia_id' => $this->materia->id,
        'observaciones' => 'observacion tarea enviada',
        'archivo' => $file,
      ]
    );
    $response->assertStatus(201);
    $response->assertJsonFragment(['nota' => 'nota tarea enviada']);
    $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario_id',
          'usuario',
          'usuario_id',
          'tarea_id',
          'tarea',
          'tema',
          'materia_id',
          'materia',
          'nota',
          'observaciones',
          'archivo_url',
          'calificacion',
        ]
      ]);
  }

  public function test_endpoint_alumno_examenes_list()
  {
    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/examenes:search');
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'puntaje_minimo',
          'aleatorio',
          'lecciones_referencia',
          'fecha_aplicacion',
          'examenes_calificaciones',
        ]
      ],
      'success'
    ]);
  }

  public function test_endpoint_alumno_examenes_get()
  {
    $response = $this->get('api/v1/alumnos/'.$this->usuario->id.'/examenes/'.$this->examen->id.':search');
    $response->assertStatus(200);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'nombre',
        'descripcion',
        'usuario_id',
        'usuario',
        'materia_id',
        'materia',
        'tipo_id',
        'tipo',
        'duracion_minutos',
        'puntaje_minimo',
        'aleatorio',
        'lecciones_referencia',
        'fecha_aplicacion',
        'examenes_calificaciones',
        'temas',
      ],
      'success'
    ]);
  }

  // Es la primer función a ejecutar
  public function setUp():void
  {
    parent::setUp();
    //Creamos usuario que se usara para las peticiones
    $this->usuario = User::factory()->create();
    $this->usuario->assignRole('Alumno');
    //uso ese usuario como el usuario autenticado en cada petición
    $this->actingAs($this->usuario, 'api');

    $periodo = Periodo::factory()->create();
    $especialidad = Especialidad::factory()->create();
    $grupo = Grupo::factory()->create();
    $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

    $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadperiodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

    $alumnoGrupo = AlumnoGrupo::factory()
      ->for($this->usuario)
      ->for($especialidadPeriodoGrupo)
      ->create();

    $this->materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadperiodo, 'especialidad_periodo')
      ->create();

    $this->tarea =  Tarea::factory()
      ->for(User::factory(),'creador')
      ->for(Unidad::factory()->for($this->materia))
      ->for($this->materia)
      ->create();

    $this->tareaEnviada = TareaEnviada::factory()
      ->for($this->usuario)
      ->for($this->materia)
      ->for($this->tarea)
      ->create(['calificacion' => 5]);

    $tareaGrupo = TareaGrupo::factory()
      ->for($especialidadPeriodoGrupo)
      ->for(
        $this->tarea
      )
      ->create(['fecha_limite' => date('Y-m-d H:i:s', strtotime(Date('Y-m-d'). ' + 2 days'))]);

    $this->tema = Tema::factory()
      ->for($this->materia->unidades->first())
      ->create();

    TareaTema::factory()
    ->for($this->tarea)
    ->for($this->tema)
    ->create();

    $this->examen = Examen::factory()
    ->for(User::factory())
    ->for($this->materia)
    ->has(
      ExamenTema::factory()->for( Tema::factory()->for(Unidad::factory()->for($this->materia)) )
    )
    ->for(ExamenTipo::factory())
    ->create();

    ExamenGrupo::factory()
    ->for($especialidadPeriodoGrupo)
    ->for($this->examen)
    ->create(['fecha_limite' => date('Y-m-d H:i:s', strtotime(Date('Y-m-d'). ' + 2 days'))]);
  }
}