<?php

namespace Tests\Feature;

use App\Models\Componente;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckPermissionComponenteTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_permisos_roles()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $can = $user->can('baja_tipo.show');
        $this->assertTrue($user->can('componente.show'));
        $this->assertTrue($user->can('componente.update'));
        $this->assertTrue($user->can('componente.create'));
        $this->assertTrue($user->can('componente.delete'));

        $user = User::factory()->create();
        $user->assignRole('Departamento de docentes');
        $this->assertTrue($user->can('componente.show'));
        $this->assertTrue($user->can('componente.update'));
        $this->assertTrue($user->can('componente.create'));
        $this->assertTrue($user->can('componente.delete'));

        $user = User::factory()->create();
        $user->assignRole('Control Escolar');
        $this->assertTrue($user->can('componente.show'));
        $this->assertTrue($user->can('componente.update'));
        $this->assertTrue($user->can('componente.create'));

        $user = User::factory()->create();
        $user->assignRole('Docente');
        $can = $user->can('baja_tipo.show');
        $this->assertTrue($user->can('componente.show'));

        $user = User::factory()->create();
        $user->assignRole('Prefecto');
        $can = $user->can('baja_tipo.show');
        $this->assertTrue($user->can('componente.show'));

        $user = User::factory()->create();
        $user->assignRole('Padre de familia');
        $can = $user->can('baja_tipo.show');
        $this->assertTrue($user->can('componente.show'));

        $user = User::factory()->create();
        $user->assignRole('Alumno');
        $this->assertFalse($user->can('baja_tipo.show'));
    }

    public function test_componente_departamento_docentes_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Departamento de docentes');
      $this->actingAs($user, 'api');

      $response = $this->post("api/v1/componentes",[
        'nombre' => 'componenete test',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'activo' => '1',
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'activo',
        ]
      ]);
    }

    public function test_componente_departamento_docentes_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Departamento de docentes');
      $this->actingAs($user, 'api');

      Componente::factory()->count(2)->create();

      $response = $this->get("api/v1/componentes");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'activo',
        ]]
      ]);
    }

    public function test_componente_departamento_docentes_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Departamento de docentes');
      $this->actingAs($user, 'api');

      $componente = Componente::factory()->create();

      $response = $this->get("api/v1/componentes/{$componente->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'activo',
        ]
      ]);
    }

    public function test_componente_departamento_docentes_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Departamento de docentes');
      $this->actingAs($user, 'api');

      $componente = Componente::factory()->create();

      $response = $this->put("api/v1/componentes/{$componente->id}",[
        'nombre' => 'componenete test update',
        'descripcion' => 'Lorem ipsum dolor sit lorem.',
        'activo' => '1',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'activo',
        ]
      ]);
      $componente->refresh();
      $this->assertEquals($componente->nombre, "componenete test update");
    }

    public function test_componente_departamento_docentes_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Departamento de docentes');
      $this->actingAs($user, 'api');

      $componente = Componente::factory()->create();

      $response = $this->delete("api/v1/componentes/{$componente->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'activo',
        ]
      ]);
      $this->assertSoftDeleted($componente);
    }
}
