<?php

namespace Tests\Feature\Examen;


use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\ExamenTipo;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use Illuminate\Support\Facades\Hash;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @author Enrique Sevilla <sevilla@puller.mx>
 * @version  1.0
 * BLOQUE : TEST PARA CRUD CON USUARIO CON TODOS LOS PERMISOS SOBRE EL MODELO
 * NOTA : con assertJsonStructure pueden traer mas datos pero no menos
 * si se necesita ver los errores sin el handler agregar:
 *  $this->withoutExceptionHandling();
 * en la primera linea de tu función
 */

class ExamenEspecialEndpointsTest extends TestCase
{
    use DatabaseTransactions;
    protected $examen,$tema,$epg;

    public function test_endpoint_search()
    {
      $response = $this->get('api/v1/examenes:search?' .
        'nombre=' . $this->examen->nombre .
        '&tipoId=' . $this->examen->tipo_id .
        '&materiaId=' . $this->examen->materia_id
      );

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
            'aleatorio',
            'lecciones_referencia',
            'activo',
          ]
        ],
        'message',
        'success'
      ]);
    }

    public function test_endpoint_bind_tema()
    {
      $response = $this->post(
        'api/v1/examenes/'.$this->examen->id.'/temas/'.$this->tema->id.':bind',
        []
      );
      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'examen_id',
          'examen',
          'tema_id',
          'tema',
        ],
        'message',
        'success'
      ]);
      $response->assertJsonFragment(['examen_id' => $this->examen->id]);
      $response->assertJsonFragment(['tema_id' => $this->tema->id]);
    }

    public function test_endpoint_unbind_tema()
    {
      $this->post('api/v1/examenes/'.$this->examen->id.'/temas/'.$this->tema->id.':bind',[]);
      $response = $this->post(
        'api/v1/examenes/'.$this->examen->id.'/temas/'.$this->tema->id.':unbind',
        []
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'examen_id',
          'examen',
          'tema_id',
          'tema',
        ],
        'message',
        'success'
      ]);
      $response->assertJsonFragment(['examen_id' => $this->examen->id]);
      $response->assertJsonFragment(['tema_id' => $this->tema->id]);
    }

    public function test_endpoint_bind_especialidad_periodo_grupo()
    {
      $response = $this->post(
        'api/v1/examenes/'.$this->examen->id.
        '/especialidad-periodo-grupo/'.$this->epg->id.':bind',
        ['fecha_limite' => Date('Y-m-d H:i:s') ]
      );
      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'examen_id',
          'examen',
          'fecha_limite',
          'especialidad_periodo_grupo_id',
          'especialidad_periodo_grupo',
        ],
        'message',
        'success'
      ]);

    }

    public function test_endpoint_unbind_especialidad_periodo_grupo()
    {
      $this->post(
        'api/v1/examenes/'.$this->examen->id.
        '/especialidad-periodo-grupo/'.$this->epg->id.':bind',
        ['fecha_limite' => Date('Y-m-d H:i:s') ]
      );
      $response = $this->post(
        'api/v1/examenes/'.$this->examen->id.
        '/especialidad-periodo-grupo/'.$this->epg->id.':unbind',
        []
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'examen_id',
          'examen',
          'fecha_limite',
          'especialidad_periodo_grupo_id',
          'especialidad_periodo_grupo',
        ],
        'message',
        'success'
      ]);

    }

  public function test_endpoint_examen_rate()
  {
    $this->withoutExceptionHandling();
    $usuario = User::factory()->create();
    $response = $this->post(
      'api/v1/usuarios/'.$usuario->id.'/examenes/'.$this->examen->id.':rate',
      []
    );
    $response->assertStatus(201);
    $response->assertJsonStructure([
      'data' => [
        'id',
        'usuario_id',
        'examen_id',
        'status_id',
        'calificacion_maxima',
        'calificacion_obtenida',
        'examen',
        'status',
      ],
      'message',
      'success'
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
    //Creamos usuario que se usara para las peticiones
    $usuario = User::factory()->create();
    $usuario->assignRole('Admin');
    //uso ese usuario como el usuario autenticado en cada petición
    $this->actingAs($usuario, 'api');

    $especialidadPeriodo = EspecialidadPeriodo::factory()
    ->for(Periodo::factory())
    ->for(Especialidad::factory())
    ->create();

    $this->epg = EspecialidadPeriodoGrupo::factory()
    ->for($especialidadPeriodo, 'EspecialidadPeriodo')
    ->for(Grupo::factory())
    ->create();

    $materia = Materia::factory()
    ->for(Componente::factory())
    ->for(
      $especialidadPeriodo,
      'especialidad_periodo'
    )
    ->create();

    $unidad = Unidad::factory()->for($materia)->create();
    $this->tema = Tema::factory()->for($unidad)->create();

    $this->examen = Examen::factory()
    ->for(User::factory())
    ->for(ExamenTipo::factory())
    ->for($materia)
    ->create();
  }
}