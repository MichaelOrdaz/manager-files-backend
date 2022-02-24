<?php

namespace Tests\Feature\ContenidosExtra;

use App\Models\Componente;
use App\Models\ContenidosExtra;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use App\Models\MaterialTipo;
use App\Models\Periodo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Unidad;
use App\Models\User;

class ContenidosExtraCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_contenidoextra_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $tipo = MaterialTipo::factory()->create();

      $response = $this->post("api/v1/periodos/{$periodo->id}/contenidos-extra", [
        'nombre' => 'material para el grupo b',
        'descripcion' => 'Lorem ipsum dolor sit amet consectetur.',
        'especialidad_id' => $especialidad->id,
        'activo' => '1',
        'tipo_contenido_id' => $tipo->id,
      ]);

      $response->assertOk()
      ->assertJsonFragment(['nombre' => 'material para el grupo b'])
      ->assertJsonStructure([
        'data' => [
            "id",
            "nombre",
            "descripcion",
            "archivo_url",
            "periodo_id",
            "periodo",
            "especialidad_id",
            "especialidad",
            "tipo_contenido_id",
            "tipo_contenido",
        ]
      ]);
    }

    public function test_contenidoextra_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $EspecialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $tipo = MaterialTipo::factory()->create();

      $contenido = ContenidosExtra::factory()
      ->for($EspecialidadPeriodo, 'especialidad_periodo')
      ->for($tipo, 'MaterialesTipo')
      ->create();

      $response = $this->get("api/v1/periodos/{$periodo->id}/contenidos-extra");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          [
            "id",
            "nombre",
            "descripcion",
            "archivo_url",
            "periodo_id",
            "periodo",
            "especialidad_id",
            "especialidad",
            "tipo_contenido_id",
            "tipo_contenido",
          ]
        ],
        "success",
      ]);
    }

    public function test_contenidoextra_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $EspecialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $tipo = MaterialTipo::factory()->create();

      $contenido = ContenidosExtra::factory()
      ->for($EspecialidadPeriodo, 'especialidad_periodo')
      ->for($tipo, 'MaterialesTipo')
      ->create();

      $response = $this->get("api/v1/periodos/{$periodo->id}/contenidos-extra/{$contenido->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          "id",
            "nombre",
            "descripcion",
            "archivo_url",
            "periodo_id",
            "periodo",
            "especialidad_id",
            "especialidad",
            "tipo_contenido_id",
            "tipo_contenido",
        ],
        "success",
      ]);
    }

    public function test_contenidoextra_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $EspecialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $tipo = MaterialTipo::factory()->create();

      $contenido = ContenidosExtra::factory()
      ->for($EspecialidadPeriodo, 'especialidad_periodo')
      ->for($tipo, 'MaterialesTipo')
      ->create();

      $response = $this->post("api/v1/periodos/{$periodo->id}/contenidos-extra/{$contenido->id}", [
        'nombre' => 'material para el grupo b',
        'descripcion' => 'Lorem ipsum dolor sit amet consectetur.',
        'especialidad_id' => $especialidad->id,
        'activo' => '1',
        'tipo_contenido_id' => $tipo->id,
      ]);

      $response->assertOk()
      ->assertJsonFragment(['nombre' => 'material para el grupo b'])
      ->assertJsonStructure([
        'data' => [
          "id",
            "nombre",
            "descripcion",
            "archivo_url",
            "periodo_id",
            "periodo",
            "especialidad_id",
            "especialidad",
            "tipo_contenido_id",
            "tipo_contenido",
        ]
      ]);

      $contenido->refresh();
      $this->assertEquals($contenido->descripcion, 'Lorem ipsum dolor sit amet consectetur.');
    }

    public function test_contenidoextra_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $EspecialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $tipo = MaterialTipo::factory()->create();

      $contenido = ContenidosExtra::factory()
      ->for($EspecialidadPeriodo, 'especialidad_periodo')
      ->for($tipo, 'MaterialesTipo')
      ->create();

      $response = $this->delete("api/v1/periodos/{$periodo->id}/contenidos-extra/{$contenido->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          "id",
            "nombre",
            "descripcion",
            "archivo_url",
            "periodo_id",
            "periodo",
            "especialidad_id",
            "especialidad",
            "tipo_contenido_id",
            "tipo_contenido",
        ],
        "success",
      ]);

      $this->assertSoftDeleted($contenido);
    }

}
