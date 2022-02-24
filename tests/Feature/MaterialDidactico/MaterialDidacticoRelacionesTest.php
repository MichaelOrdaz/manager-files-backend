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
namespace Tests\Feature\MaterialDidactico;

use Tests\TestCase;

use App\Models\Tema;
use App\Models\User;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\MaterialTipo;
use App\Models\MaterialDidactico;

use App\Models\EspecialidadPeriodo;
use App\Models\AlumnoMaterialDidactico;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterialDidacticoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $materialDidactico;

    public function test_material_didactico_belongs_to_tema()
    {
      $this->assertInstanceOf(
        Tema::class,
        $this->materialDidactico->Tema,
      );
    }

    public function test_material_didactico_belongs_to_material_tipo()
    {
      $this->assertInstanceOf(
        MaterialTipo::class,
        $this->materialDidactico->MaterialesTipo,
      );
    }

    public function test_material_didactico_belongs_to_usuario()
    {
      $this->assertInstanceOf(
        User::class,
        $this->materialDidactico->creador,
      );
      $this->assertInstanceOf(
        User::class,
        $this->materialDidactico->usuario,
      );
    }

    public function test_material_didactico_has_many_alumnoMaterialDidactico()
    {
      $this->assertInstanceOf(AlumnoMaterialDidactico::class,$this->materialDidactico->AlumnoMaterialDidactico->first());
    }

    public function setUp():void{
      parent::setUp();

      $especialidad = Especialidad::factory()->create();
      $periodo = Periodo::factory()->create();
      $componente = Componente::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($periodo)
        ->for($especialidad)
        ->create();

      $materia = Materia::factory()
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->for($componente)
        ->has(Unidad::factory(), 'unidades')
        ->create();

      $unidad = $materia->unidades->first();
      $tema = Tema::factory()->for($unidad)->create();


      $this->materialDidactico = MaterialDidactico::factory()
        ->for($tema)
        ->forCreador()
        ->for(User::factory(),'Usuario')
        ->for( MaterialTipo::factory() , 'MaterialesTipo' )
        ->create(['nombre' => 'puller materia didáctico test']);

      AlumnoMaterialDidactico::factory()
        ->for(User::factory())
        ->for($this->materialDidactico)
        ->create();
    }
}