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

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Unidad;

use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Especialidad;
use App\Models\contenidosExtra;
use App\Models\EspecialidadPeriodo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EspecialidadCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected $especialidad;
    protected $url = '/api/v1/especialidades/';

    public function test_especialidad_crud_create()
    {
      $response = $this->post($this->url,[
        'nombre' => 'nombre',
        'objetivos' => 'objetivos',
        'planes_de_estudios' => 'planes_de_estudios',
        'perfil_egresado' => 'perfil_egresado',
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'objetivos',
          'planes_de_estudios',
          'perfil_egresado',
          'imagen_url',
          'activo',
        ]
      ]);

    }

    public function test_especialidad_crud_list()
    {
      $response = $this->get($this->url);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
            'objetivos',
            'planes_de_estudios',
            'perfil_egresado',
            'imagen_url',
            'activo',
          ]
        ]
      ]);

    }

    public function test_especialidad_crud_get()
    {
      $response = $this->get($this->url . $this->especialidad->id );
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'objetivos',
          'planes_de_estudios',
          'perfil_egresado',
          'imagen_url',
          'activo',
        ]
      ]);
    }

    public function test_especialidad_crud_update()
    {
      $response = $this->post($this->url . $this->especialidad->id, [
        'nombre' => 'nombre update',
        'objetivos' => 'objetivos update',
        'planes_de_estudios' => 'planes_de_estudios update',
        'perfil_egresado' => 'perfil_egresado update',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'objetivos',
          'planes_de_estudios',
          'perfil_egresado',
          'imagen_url',
          'activo',
        ]
      ]);

      $especialidadUpdate = Especialidad::find($this->especialidad->id);
      $this->assertEquals($especialidadUpdate->nombre,'nombre update');
      $this->assertEquals($especialidadUpdate->objetivos,'objetivos update');
      $this->assertEquals($especialidadUpdate->planes_de_estudios,'planes_de_estudios update');
      $this->assertEquals($especialidadUpdate->perfil_egresado,'perfil_egresado update');
    }

    public function test_especialidad_crud_delete()
    {
      $this->handleValidationExceptions();

      $tablaIntermediaIds = $this->especialidad->especialidad_periodo->pluck('id');
      $periodosIds = $this->especialidad->periodos->pluck('id');
      $contenidosExtraIds = $this->especialidad->contenidosExtra->pluck('id');

      $this->assertFalse(empty($tablaIntermediaIds));
      $this->assertFalse(empty($periodosIds));
      $this->assertFalse(empty($contenidosExtraIds));

      $response = $this->delete($this->url . $this->especialidad->id );

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'objetivos',
          'planes_de_estudios',
          'perfil_egresado',
          'imagen_url',
          'activo',
        ]
      ]);

      $especialidadDelete = Especialidad::find($this->especialidad->id);
      $this->assertEquals($especialidadDelete,NULL);
      $this->assertEquals(0,EspecialidadPeriodo::whereIn('id',$tablaIntermediaIds)->count());
      $this->assertEquals(0,Unidad::where('nombre','puller Unidad test')->count());
      $this->assertEquals(0,Tema::where('nombre','puller Tema prueba')->count());
    }

    public function setUp():void{
      parent::setUp();
      $usuario = User::factory()->create();
      $usuario->assignRole('Admin');
      $this->actingAs($usuario, 'api');
      $this->especialidad = Especialidad::factory()->create();
    }
}