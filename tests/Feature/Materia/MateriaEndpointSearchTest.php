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
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Especialidad;

use App\Models\DocenteMateria;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MateriaEndpointSearchTest extends TestCase
{
  use DatabaseTransactions;

  public function test_materia_endpoint_search()
  {
    $usuario = User::factory()->create();
    $usuario->assignRole('Admin');
    $this->actingAs($usuario, 'api');

    $periodo = Periodo::factory()->create();

    $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for(Especialidad::factory())
      ->create();

    $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for(Grupo::factory())
      ->create();

    $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create([ 'nombre' => 'Prueba materia' ]);

    DocenteMateria::factory()
      ->for($usuario)
      ->for($materia)
      ->create([
        'especialidad_periodo_grupo_id' => $epg->id
      ]);

    $response = $this->get(
      'api/v1/materias:search?page=1&periodoId='. $periodo->id .
      '&nombre='. $materia->nombre .
      '&docenteId='.$usuario->id
    );

    $response->assertStatus(200);
    $response->assertJsonFragment(['nombre' => $materia->nombre]);
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
          'grupos',
          'docentes',
          'unidades',
        ]
      ]
    ]);

  }
}