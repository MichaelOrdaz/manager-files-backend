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
namespace Tests\Feature\Tema;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;

use App\Models\Tarea;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\TareaTema;
use App\Models\Componente;
use App\Models\ExamenTema;
use App\Models\ExamenTipo;
use App\Models\Especialidad;
use App\Models\MaterialTipo;
use App\Models\TareaEnviada;
use App\Models\MaterialDidactico;
use App\Models\EspecialidadPeriodo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemaCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected $tema, $especialidad, $materia, $unidad, $url;

    public function test_tema_crud_create()
    {
      $response = $this->post($this->url,[
        'nombre' => 'Test de creación tema',
        'descripcion' => 'Tema prueba para desarrolladores',
        'materia_id' => $this->materia->id,
        'unidad_id' => $this->unidad->id,
      ]);
      $response->assertJsonFragment(['nombre'=>'Test de creación tema']);
      $response->assertJsonStructure([
        'data' =>[
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
          'unidad_id',
          'unidad',
        ]
      ]);
    }

    public function test_tema_crud_list()
    {
      $response = $this->get($this->url);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre'=>$this->tema->nombre]);
      $response->assertJsonStructure([
        'data' =>[
          [
            'id',
            'nombre',
            'descripcion',
            'materia_id',
            'materia',
            'unidad_id',
            'unidad',
          ]
        ]
      ]);
    }

    public function test_tema_crud_get()
    {
      $response = $this->get($this->url . $this->tema->id);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre'=>$this->tema->nombre]);
      $response->assertJsonStructure([
        'data' =>[
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
          'unidad_id',
          'unidad',
        ]
      ]);
    }

    public function test_tema_crud_update()
    {
      $this->handleValidationExceptions();
      $response = $this->put($this->url . $this->tema->id ,[
        'nombre' => 'Test de update tema',
        'descripcion' => 'Tema prueba para desarrolladores',
        'materia_id' => $this->materia->id,
        'unidad_id' => $this->unidad->id,
      ]);
      $response->assertJsonStructure([
        'data' =>[
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
          'unidad_id',
          'unidad',
        ]
      ]);

      $temaUpdate = Tema::find($this->tema->id);
      $this->assertEquals($temaUpdate->nombre,'Test de update tema');
    }

    public function test_tema_crud_delete()
    {
      $materialDidactico = $this->tema->materialDidactico[0];
      $tutoria = $this->tema->tutorias[0];
      $tareasTema = $this->tema->tarea_tema;
      $examenTema = $this->tema->ExamenTema->first();

      $response = $this->delete($this->url . $this->tema->id);
      $response->assertStatus(200);
      $response->assertJsonFragment(['nombre'=>$this->tema->nombre]);
      $response->assertJsonStructure([
        'data' =>[
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
          'unidad_id',
          'unidad',
        ]
      ]);
      $this->assertSoftDeleted($this->tema);
      $this->assertSoftDeleted($materialDidactico);
      $this->assertSoftDeleted($tutoria);
      $this->assertSoftDeleted($examenTema);
      $tareasTema->each(function($tarea){$this->assertSoftDeleted($tarea);});
    }

    public function setUp():void{
      parent::setUp();

      $usuario = User::factory()->create();
      $usuario->assignRole('Admin');
      $this->actingAs($usuario, 'api');

      $this->especialidad = Especialidad::factory()->create();
      $periodo = Periodo::factory()->create();
      $componente = Componente::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($periodo)
        ->for($this->especialidad)
        ->create();

      $this->materia = Materia::factory()
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->for($componente)
        ->has( Examen::factory()->forUser()->for(ExamenTipo::factory())->count(3) , 'examenes' )
        ->has(Unidad::factory(), 'unidades')
        ->create();

      $this->unidad = $this->materia->unidades->first();

      $this->tema = Tema::factory()
        ->for($this->unidad)
        ->create();

      TareaTema::factory()
        ->for(
          Tarea::factory()->state([
            'creador_id' => User::factory()->create()->id,
            'unidad_id' => $this->materia->unidades->first()->id,
            'materia_id' => $this->materia->id,
          ])
        )
        ->for($this->tema)
        ->create();

      ExamenTema::factory()
        ->for($this->materia->examenes[0])
        ->for($this->tema)
        ->create();

      $this->materialDidactico = MaterialDidactico::factory()
        ->for($this->tema)
        ->forCreador()
        ->for( MaterialTipo::factory() , 'MaterialesTipo' )
        ->create();

        Tutoria::factory()->forUser()
        ->for($this->materialDidactico, 'Material')
        ->for($this->materia)
        ->for($this->tema, 'tema')
        ->for(Grupo::factory())->create();

      $this->url = "api/v1/especialidades/{$this->especialidad->id}/materias/{$this->materia->id}/unidades/{$this->unidad->id}/temas/";
    }
}
