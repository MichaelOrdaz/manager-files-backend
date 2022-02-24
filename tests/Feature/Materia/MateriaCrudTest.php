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
namespace Tests\Feature\Materia;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\ExamenTipo;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\TareaEnviada;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MateriaCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $materia, $url, $especialidad, $componente, $periodo;

    public function test_materia_crud_create()
    {
      $response = $this->post($this->url,[
        'nombre' => $this->materia->nombre,
        'descripcion' => 'Descripción Materia test',
        'periodo_id' => $this->periodo->id,
        'componente_id' => $this->componente->id,
      ]);
      $response->assertJsonFragment(['nombre' => $this->materia->nombre]);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'periodo_id',
          'periodo',
          'componente_id',
          'componente',
          'especialidad_id',
          'especialidad',
          'imagen_url',
          'requisitos',
          'ultima_actualizacion',
          'grupos',
          'docentes',
          'unidades',
        ]
      ]);
    }

    public function test_materia_crud_list()
    {
      $response = $this->get($this->url);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre' => $this->materia->nombre]);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
            'descripcion',
            'periodo_id',
            'periodo',
            'componente_id',
            'componente',
            'especialidad_id',
            'especialidad',
            'imagen_url',
            'requisitos',
            'ultima_actualizacion',
            'grupos',
            'docentes',
            'unidades',
          ]
        ]
      ]);
    }

    public function test_materia_crud_get()
    {
      $response = $this->get($this->url . $this->materia->id);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre' => $this->materia->nombre]);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'periodo_id',
          'periodo',
          'componente_id',
          'componente',
          'especialidad_id',
          'especialidad',
          'imagen_url',
          'requisitos',
          'ultima_actualizacion',
          'grupos',
          'docentes',
          'unidades',
        ]
      ]);
    }

    public function test_materia_crud_update()
    {
      $response = $this->post($this->url . $this->materia->id,[
        'nombre' => 'puller Materia test update',
        'descripcion' => 'Descripción Materia test',
        'periodo_id' => $this->periodo->id,
        'componente_id' => $this->componente->id,
      ]);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre' => 'puller Materia test update']);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'periodo_id',
          'periodo',
          'componente_id',
          'componente',
          'especialidad_id',
          'especialidad',
          'imagen_url',
          'requisitos',
          'ultima_actualizacion',
          'grupos',
          'docentes',
          'unidades',
        ]
      ]);
    }

    public function test_materia_crud_delete()
    {
      $unidades = $this->materia->unidades;
      $conferencias = $this->materia->conferencias;
      $tareas = $this->materia->tareas;
      $tareasEnviadas = $this->materia->tareasEnviadas;
      $examenes = $this->materia->examenes;

      $response = $this->delete($this->url . $this->materia->id);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'periodo_id',
          'periodo',
          'componente_id',
          'componente',
          'especialidad_id',
          'especialidad',
          'imagen_url',
          'requisitos',
          'ultima_actualizacion',
          'grupos',
          'docentes',
          'unidades',
        ]
      ]);
      $response->assertJsonFragment(['nombre' => $this->materia->nombre]);
      $this->assertSoftDeleted($this->materia);
      $examenes->each(function($examen){ $this->assertSoftDeleted($examen); });
      $conferencias->each(function($conferencia){ $this->assertSoftDeleted($conferencia); });
      $tareas->each(function($tarea){ $this->assertSoftDeleted($tarea); });
      $tareasEnviadas->each(function($tarea){ $this->assertSoftDeleted($tarea); });
      $unidades->each(function($unidad){ $this->assertSoftDeleted($unidad); });
    }

    public function setUp():void{
      parent::setUp();

      $usuario = User::factory()->create();
      $usuario->assignRole('Admin');
      $this->actingAs($usuario, 'api');

      $this->especialidad = Especialidad::factory()->create();
      $this->periodo = Periodo::factory()->create();
      $this->componente = Componente::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($this->periodo)
        ->for($this->especialidad)
        ->create();

      $grupo = Grupo::Factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();
      $this->materia = Materia::factory()
        ->for( $especialidadPeriodo , 'especialidad_periodo' )
        ->for( $this->componente )
        ->has( Unidad::factory()->count(1) , 'unidades' )
        ->has( Examen::factory()->forUser()->for(ExamenTipo::factory())->count(1) , 'examenes' )
        ->create();
        $unidad = Unidad::factory()->for($this->materia)->create();
        $tema = Tema::factory()->for($unidad)->create();

        Conferencia::factory()
          ->for($this->materia)
          ->for($unidad)
          ->for($tema)
          ->for($epg)
          ->count(5)
          ->create();

      TareaEnviada::factory()
        ->count(10)
        ->forUser()
        ->for($this->materia)
        ->forTarea([
          'creador_id' => $usuario->id,
          'unidad_id' => $this->materia->unidades->first()->id,
          'materia_id' => $this->materia->id,
        ])->create();

      $this->url = '/api/v1/especialidades/'. $this->especialidad->id .'/materias/';

    }
}
