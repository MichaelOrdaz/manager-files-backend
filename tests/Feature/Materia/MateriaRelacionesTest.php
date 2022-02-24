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

use App\Models\User;
use App\Models\Grupo;
use App\Models\Tarea;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Tema;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\ExamenTipo;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\TareaEnviada;
use App\Models\DocenteMateria;

use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MateriaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $materia;

    public function test_materia_has_belongs_many_especialidades()
    {
      $this->assertInstanceOf(
        Especialidad::class,
        $this->materia->especialidad_periodo->especialidad
      );
    }

    public function test_materia_has_one_componente()
    {
      $this->assertInstanceOf(
        \App\Models\Componente::class,
        $this->materia->componente
      );
    }

    public function test_materia_has_one_especialidadPeriodo()
    {
      $this->assertInstanceOf(
        \App\Models\EspecialidadPeriodo::class,
        $this->materia->especialidad_periodo
      );
    }

    public function test_materia_has_many_unidades()
    {
      $this->assertInstanceOf(
        \App\Models\Unidad::class,
        $this->materia->unidades[0]
      );
    }

    public function test_materia_has_many_conferencia()
    {
      $this->assertContainsOnlyInstancesOf(
        \App\Models\Conferencia::class,
        $this->materia->conferencias
      );
    }

    public function test_materia_has_many_tareas()
    {
      $this->assertInstanceOf(
        \App\Models\Tarea::class,
        $this->materia->Tareas[0]
      );
    }

    public function test_materia_has_many_tareas_enviadas()
    {
      $this->assertInstanceOf(
        \App\Models\TareaEnviada::class,
        $this->materia->tareasEnviadas[0]
      );
    }

    public function test_materia_has_many_examenes()
    {
      $this->assertInstanceOf(
        \App\Models\Examen::class,
        $this->materia->examenes[0]
      );
    }

    public function test_materia_has_many_docente_materia()
    {
      $this->assertInstanceOf(
        \App\Models\DocenteMateria::class,
        $this->materia->docentesMaterias[0]
      );
    }

    public function setUp():void{
      parent::setUp();

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
        ->has( Examen::factory()->forUser()->for(ExamenTipo::factory())->count(3) , 'examenes' )
        ->create();

        $unidad = Unidad::factory()->for($this->materia)->create();
        $tema = Tema::factory()->for($unidad)->create();

        Conferencia::factory()
          ->for($this->materia)
          ->for($unidad)
          ->for($tema)
          ->for($epg)
          ->count(3)
          ->create();

      TareaEnviada::factory()
        ->count(10)
        ->forUser()
        ->for($this->materia)
        ->forTarea([
          'creador_id' => User::factory()->create()->id,
          'unidad_id' => $this->materia->unidades->first()->id,
          'materia_id' => $this->materia->id,
        ])->create();

        $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
          ->for($especialidadPeriodo)
          ->for(Grupo::factory())
          ->create();

        DocenteMateria::factory()
          ->for(User::factory())
          ->for($this->materia)
          ->for($especialidadPeriodoGrupo, 'especialidad_periodo_grupo')
          ->create();

    }
}
