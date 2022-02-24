<?php

namespace Tests\Feature\Estado;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Estado;
use App\Models\User;

class EstadoCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_estado_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $response = $this->post("api/v1/estados", [
        'nombre' => 'puebla',
        'activo' => 1,
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['nombre' => 'puebla'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
        ]
      ]);
    }

    public function test_estado_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->count(3)->create();

      $response = $this->get("api/v1/estados");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
          ]
        ],
        "success",
      ]);
    }

    public function test_estado_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();

      $response = $this->get("api/v1/estados/{$estado->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
        ],
        "success",
      ]);
    }

    public function test_estado_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();

      $response = $this->put("api/v1/estados/{$estado->id}",[
        'nombre' => 'Mexico',
        'activo' => 1,
      ]);

      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
        ],
        "success",
      ]);

      $estado->refresh();
      $this->assertEquals($estado->nombre, 'Mexico');
    }

    public function test_estado_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();

      $response = $this->delete("api/v1/estados/{$estado->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
        ],
        "success",
      ]);

      $this->assertSoftDeleted($estado);
    }
}