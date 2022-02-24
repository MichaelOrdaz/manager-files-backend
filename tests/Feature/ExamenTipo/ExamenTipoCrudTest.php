<?php

namespace Tests\Feature\ExamenTipo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenTipo;
use App\Models\User;

class ExamenTipoCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_examentipo_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      ExamenTipo::factory()->count(1)->create();

      $response = $this->get("api/v1/examenes-tipo");

      $response->assertOk()
      ->assertJsonStructure([
        'data' => [
          [
            'id',
            'nombre',
            'descripcion',
            'activo',
          ]
        ]
      ]);
    }

    public function test_examentipo_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $examenTipo = ExamenTipo::factory()->create();

      $response = $this->get("api/v1/examenes-tipo/{$examenTipo->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'activo',
        ],
        "success",
      ]);
    }
}