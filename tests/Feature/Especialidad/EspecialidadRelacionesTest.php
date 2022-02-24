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
namespace Tests\Feature\Especialidad;

use App\Models\Componente;
use App\Models\ContenidosExtra;
use Tests\TestCase;

use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Especialidad;

use App\Models\EspecialidadPeriodo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\EspecialidadPeriodoGrupo;
use App\Models\Grupo;
use App\Models\MaterialTipo;
use App\Models\User;

class EspecialidadRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $especialidad;

    public function test_especialidad_has_many_periodos()
    {
      $this->assertInstanceOf(\App\Models\Periodo::class,$this->especialidad->periodos[0]);
    }

    public function test_especialidad_has_many_materias()
    {
      $this->assertInstanceOf(\App\Models\Materia::class,$this->especialidad->materias[0]);
    }

    public function test_especialidad_tiene_relaciones()
    {
      $user = User::factory()->create();

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->has(
        ContenidosExtra::factory()
        ->for(MaterialTipo::factory(), 'MaterialesTipo'),
        'contenidoExtra'
      )
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadperiodo, 'especialidad_periodo')
      ->create();

      EspecialidadPeriodoGrupo::factory()
      ->for($especialidadperiodo, 'EspecialidadPeriodo')
      ->for(Grupo::factory())
      ->create();

      $this->assertInstanceOf(Periodo::class, $especialidad->periodos->first());
      $this->assertInstanceOf(Materia::class, $especialidad->materias->first());
      $this->assertInstanceOf(EspecialidadPeriodo::class, $especialidad->especialidad_periodo->first());
      $this->assertInstanceOf(ContenidosExtra::class, $especialidad->contenidosExtra->first());
    }

    public function setUp():void{
      parent::setUp();
      $this->especialidad = Especialidad::factory()->create();

      Materia::factory()
        ->for(Componente::factory())
        ->for(
          EspecialidadPeriodo::factory()
            ->for(Periodo::factory())
            ->for($this->especialidad),
          'especialidad_periodo'
        )->create();
    }
}