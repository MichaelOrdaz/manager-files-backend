<?php

namespace Tests\Feature\Componente;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Componente;
use App\Models\User;

class ComponenteCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_componente_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
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

    public function test_componente_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
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

    public function test_componente_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
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

    public function test_componente_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
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

    public function test_componente_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
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