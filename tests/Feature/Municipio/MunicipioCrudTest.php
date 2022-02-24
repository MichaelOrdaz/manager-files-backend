<?php

namespace Tests\Feature\Municipio;

use App\Models\Estado;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Municipio;
use App\Models\User;

class MunicipioCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_municipio_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $response = $this->post("api/v1/estados/{$estado->id}/municipios", [
        'nombre' => 'puebla',
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['nombre' => 'puebla'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'estado_id',
          'estado',
        ]
      ]);
    }

    public function test_municipio_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      Municipio::factory()
        ->for($estado)
      ->count(5)
      ->create();

      $response = $this->get("api/v1/estados/{$estado->id}/municipios");
      $response->assertJsonFragment(['estado_id' => $estado->id]);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
            'estado_id',
            'estado',
          ]
        ],
        "success",
      ]);
    }

    public function test_municipio_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()
        ->for($estado)
      ->create();

      $response = $this->get("api/v1/estados/{$estado->id}/municipios/{$municipio->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'estado_id',
          'estado',
        ],
        "success",
      ]);
    }

    public function test_municipio_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()
        ->for($estado)
      ->create();

      $response = $this->put("api/v1/estados/{$estado->id}/municipios/{$municipio->id}",[
        'nombre' => 'Mexico'
      ]);

      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'estado_id',
          'estado',
        ],
        "success",
      ]);

      $municipio->refresh();
      $this->assertEquals($municipio->nombre, 'Mexico');
    }

    public function test_municipio_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()
        ->for($estado)
      ->create();

      $response = $this->delete("api/v1/estados/{$estado->id}/municipios/{$municipio->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'estado_id',
          'estado',
        ],
        "success",
      ]);

      $this->assertSoftDeleted($municipio);
    }

}