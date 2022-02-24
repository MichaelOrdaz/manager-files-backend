<?php

namespace Tests\Feature\DatosAcademicos;

use App\Models\AspiranteStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\DatosAcademicos;
use App\Models\User;

class DatosAcademicosCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_datosacademicos_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $status = AspiranteStatus::factory()->create();

      $response = $this->post("api/v1/usuarios/{$user->id}/datos-academicos",[
        'matricula' => 'lorem',
        'generacion' => '2021',
        'status_id' => $status->id,
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          "usuario_id",
          "usuario",
          "matricula",
          "generacion",
          "baja_id",
          "baja",
          "fecha_baja",
          "status_id",
          "status",
        ]
      ]);
    }

    public function test_datosacademicos_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $status = AspiranteStatus::factory()->create();

      $datos = DatosAcademicos::factory()
      ->for($user)
      ->for($status, 'Status')
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/datos-academicos");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          "usuario_id",
          "usuario",
          "matricula",
          "generacion",
          "baja_id",
          "baja",
          "fecha_baja",
          "status_id",
          "status",
        ]]
      ]);
    }

    public function test_datosacademicos_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $status = AspiranteStatus::factory()->create();

      $datos = DatosAcademicos::factory()
      ->for($user)
      ->for($status, 'Status')
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/datos-academicos/{$datos->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "usuario_id",
          "usuario",
          "matricula",
          "generacion",
          "baja_id",
          "baja",
          "fecha_baja",
          "status_id",
          "status",
        ]
      ]);
    }

    public function test_datosacademicos_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $status = AspiranteStatus::factory()->create();

      $datos = DatosAcademicos::factory()
      ->for($user)
      ->for($status, 'Status')
      ->create();

      $response = $this->put("api/v1/usuarios/{$user->id}/datos-academicos/{$datos->id}",[
        'matricula' => 'lorem ipsum',
        'generacion' => '2022',
        'status_id' => $status->id,
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "usuario_id",
          "usuario",
          "matricula",
          "generacion",
          "baja_id",
          "baja",
          "fecha_baja",
          "status_id",
          "status",
        ]
      ]);

      $datos->refresh();
      $this->assertEquals($datos->generacion, '2022');
      $this->assertEquals($datos->matricula, 'lorem ipsum');
    }

}