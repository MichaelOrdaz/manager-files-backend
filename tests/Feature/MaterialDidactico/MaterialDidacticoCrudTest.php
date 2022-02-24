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
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class MaterialDidacticoCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected $materialDidactico,$url;

    public function test_material_didactico_crud_create()
    {
      $response = $this->post($this->url,[
        'nombre' => 'Nuevo registro material didactico',
        'descripcion' => 'Nuevo registro material didactico descripción',
        'tipo_material_id' => $this->materialDidactico->tipo_material_id,
      ]);
      $response->assertStatus(201);
      $response->assertJsonFragment(['nombre'=>'Nuevo registro material didactico']);
      $response->assertJsonStructure([
        'data' =>[
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
      ]);

    }

    public function test_material_didactico_crud_list()
    {
      $response = $this->get($this->url);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre'=>$this->materialDidactico->nombre]);
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

    public function test_material_didactico_crud_get()
    {
      $response = $this->get($this->url . $this->materialDidactico->id);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre'=>$this->materialDidactico->nombre]);
      $response->assertJsonStructure([
        'data' =>[
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
      ]);
    }

    public function test_material_didactico_crud_update()
    {

        $file = UploadedFile::fake()->image('avatar.jpg');

      $response = $this->post($this->url . $this->materialDidactico->id,[
        'nombre' => 'puller materia didáctico test update',
        'descripcion' => 'puller descripcion materia didáctico test',
        'tipo_material_id' => $this->materialDidactico->tipo_material_id,
        'archivo' => $file,
      ]);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' =>[
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
      ]);

      $materialUpdate = MaterialDidactico::find($this->materialDidactico->id);
      $this->assertEquals($materialUpdate->nombre,'puller materia didáctico test update');
    }

    public function test_material_didactico_crud_copy()
    {
      $urlCopy = $this->url . $this->materialDidactico->id . ':copy';
      $response = $this->post($urlCopy);
      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' =>[
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
      ]);
      $response->assertJsonFragment(['nombre' => $this->materialDidactico->nombre]);
      $response->assertJsonFragment(['creador_id' => $this->materialDidactico->creador_id]);
    }

    public function test_material_didactico_crud_delete()
    {
      $response = $this->delete($this->url . $this->materialDidactico->id);
      $response->assertJsonFragment(['nombre'=>'puller materia didáctico test']);
      $response->assertJsonStructure([
        'data' =>[
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
      ]);
    }

    public function test_material_didactico_crud_check()
    {
      $this->withoutExceptionHandling();
      $response = $this->put($this->url . $this->materialDidactico->id . ':check', []);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' =>[
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
            'alumno_material_didactico',
            'activo',
        ]
      ]);
      $response = $this->put($this->url . $this->materialDidactico->id . ':check', []);
      $response->assertJsonFragment(['alumno_material_didactico' => null ]);
    }


    public function setUp():void{
      parent::setUp();

      $usuario = User::factory()->create();
      $usuario->assignRole('Admin');
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


      $this->materialDidactico = MaterialDidactico::factory()
        ->for($tema)
        ->forCreador()
        ->for( MaterialTipo::factory() , 'MaterialesTipo' )
        ->create(['nombre' => 'puller materia didáctico test']);

      $this->url = '/api/v1/especialidades/'. $especialidad->id .'/materias/'.
        $materia->id .'/unidades/' . $unidad->id.'/temas/' . $tema->id . '/materiales-didacticos/';
    }
}
