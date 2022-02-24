<?php

namespace Tests\Feature\Unidad;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use App\Models\Periodo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Unidad;
use App\Models\User;

class UnidadCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unidad_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $response = $this->post("api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades",
      [
        'nombre' => 'nombre prueba',
        'descripcion' => 'Lorem ipsum dolor sit',
        'materia_id' => $materia->id,
        'activo' => 1,
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['nombre' => 'nombre prueba'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
        ]
      ]);
    }

    public function test_unidad_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $especialidad = Especialidad::factory()->create();
      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for($especialidad)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadPeriodo, 'especialidad_periodo')
      ->create();

      Unidad::factory()
      ->for($materia)
      ->count(10)
      ->create();

      $response = $this->getJson("api/v1/especialidades/{$especialidad->id}/materias/{$materia->id}/unidades");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
            'descripcion',
            'materia_id',
            'materia',
          ]
        ],
        "success",
      ]);
    }

    public function test_unidad_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $response = $this->get(
        "api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades/".$unidad->id
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
        ],
        "success",
      ]);
    }

    public function test_unidad_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $response = $this->put("api/v1/especialidades/".
      $materia->especialidad_periodo->id .
      "/materias/".$materia->id.
      "/unidades/".$unidad->id,
      [
        'nombre' => 'nombre de prueba actualizado',
        'descripcion' => 'descripcion update',
        'materia_id' => $materia->id,
        'activo' => 1,
      ]);

      $response->assertOk()
      ->assertJsonFragment(['nombre' => 'nombre de prueba actualizado'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
        ]
      ]);

      $unidad->refresh();
      $this->assertEquals($unidad->descripcion, 'descripcion update');
    }

    public function test_unidad_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $response = $this->delete(
        "api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades/".$unidad->id
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'materia_id',
          'materia',
        ],
        "success",
      ]);

      $this->assertSoftDeleted($unidad);
    }
}