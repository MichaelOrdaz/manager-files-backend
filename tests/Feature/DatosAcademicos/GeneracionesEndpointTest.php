<?php

namespace Tests\Feature\DatosAcademicos;

use App\Models\AspiranteStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\DatosAcademicos;
use App\Models\User;

class GeneracionesEndpointTest extends TestCase
{
    use DatabaseTransactions;

    public function test_datos_academicos_endpoint_generaciones()
    {
      $user = User::factory()->create();
      $user->assignRole('Departamento de docentes');
      $this->actingAs($user, 'api');

      $response = $this->get('api/v1/generaciones');

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'generaciones',
        ]
      ]);
    }
}