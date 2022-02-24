<?php

namespace Tests\Feature\MaterialTipo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\MaterialTipo;
use App\Models\User;

class MaterialTipoCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_materialtipo_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $response = $this->post("api/v1/tipos-material", [
        'nombre' => 'prueba',
        'icono' => 'link_outline',
        'activo' => 1,
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['nombre' => 'prueba'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'icono',
        ]
      ]);
    }

    public function test_materialtipo_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      MaterialTipo::factory()->count(1)->create();

      $response = $this->get("api/v1/tipos-material");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
            'icono',
          ]
        ],
        "success",
      ]);
    }

    public function test_materialtipo_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $material = MaterialTipo::factory()->create();

      $response = $this->get("api/v1/tipos-material/{$material->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'icono',
        ],
        "success",
      ]);
    }

    public function test_materialtipo_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $material = MaterialTipo::factory()->create();

      $response = $this->put("api/v1/tipos-material/{$material->id}", [
        'nombre' => 'prueba update',
        'icono' => 'link_outline',
        'activo' => 0,
      ]);

      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'icono',
        ],
        "success",
      ]);

      $material->refresh();
      $this->assertEquals($material->nombre, 'prueba update');
    }

    public function test_materialtipo_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $material = MaterialTipo::factory()->create();

      $response = $this->delete("api/v1/tipos-material/{$material->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'icono',
        ],
        "success",
      ]);

      $this->assertSoftDeleted($material);
    }
}
