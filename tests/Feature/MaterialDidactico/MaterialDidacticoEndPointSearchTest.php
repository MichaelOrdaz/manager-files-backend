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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterialDidacticoEndPointSearchTest extends TestCase
{
  use DatabaseTransactions;

  public function test_material_didactico_endpoint_search()
  {
    $usuario = User::factory()->create();
    $usuario->assignRole('Docente');
    $this->actingAs($usuario, 'api');

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

    $materialDidactico = MaterialDidactico::factory()
      ->for($tema)
      ->forCreador()
      ->for( MaterialTipo::factory() , 'MaterialesTipo' )
      ->create(['nombre' => 'puller materia didáctico test']);

    $response = $this->get('/api/v1/materiales-didacticos:search?temaId=' . $tema->id);
    $response->assertStatus(200);
    $response->assertJsonFragment(['nombre'=>$materialDidactico->nombre]);
    $response->assertJsonStructure([
      'data' =>[
        [
          'id',
          'nombre',
          'descripcion',
          'tema_id',
          'tema',
          'tipo_material_id',
          'material',
          'archivo_url',
          'creador_id',
          'creador',
          'usuario_id',
          'usuario',
          'activo',
        ]
      ]
    ]);
  }
}
