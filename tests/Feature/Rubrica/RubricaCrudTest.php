<?php

namespace Tests\Feature\Rubrica;

use Tests\TestCase;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Rubrica;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\DocenteMateria;
use Spatie\Permission\Models\Role;
use App\Models\EspecialidadPeriodo;
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
class RubricaCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $rubrica, $docenteMateria;

    public function test_rubrica_crud_create()
    {
      $response = $this->post('/api/v1/rubrica',[
        'docente_materia_id' => $this->docenteMateria->id,
        'tareas' => 10,
        'examenes' => 80,
        'asistencias' => 10,
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'docente_materia_id',
          'tareas',
          'examenes',
          'asistencias',
        ]
      ]);

    }

    public function test_rubrica_crud_get(){
      $response = $this->get('/api/v1/rubrica/'.$this->rubrica->id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'docente_materia_id',
          'tareas',
          'examenes',
          'asistencias',
        ]
      ]);
    }

    public function test_rubrica_crud_update(){
      $rubricasTotales = Rubrica::count();

      $response = $this->post('/api/v1/rubrica' ,[
        'docente_materia_id' => $this->rubrica->docente_materia_id,
        'tareas' => 33,
        'examenes' => 33,
        'asistencias' => 34,
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'docente_materia_id',
          'tareas',
          'examenes',
          'asistencias',
        ]
      ]);

      $this->assertCount($rubricasTotales,Rubrica::all()->toArray());
      $rubrica = Rubrica::findOrFail($this->rubrica->id);
      $this->assertSame($rubrica->docente_materia_id,$this->docenteMateria->id);
      $this->assertSame($rubrica->tareas,33);
      $this->assertSame($rubrica->examenes,33);
      $this->assertSame($rubrica->asistencias,34);
    }

    public function test_rubrica_get_by_docente_materia_id()
    {
      $response = $this->get('/api/v1/rubrica-docente-materia/'.$this->rubrica->docente_materia_id);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'docente_materia_id',
          'tareas',
          'examenes',
          'asistencias',
        ]
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
      $usuario->assignRole('Docente');
      //uso ese usuario como el usuario autenticado en cada petición
      $this->actingAs($usuario, 'api');

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for(Grupo::factory())
      ->create();

      $this->docenteMateria = DocenteMateria::factory()
      ->for(User::factory())
      ->for($materia)
      ->create([
        'especialidad_periodo_grupo_id' => $epg->id
      ]);

      $this->rubrica = Rubrica::factory()
      ->for($this->docenteMateria)
      ->create();

    }
}