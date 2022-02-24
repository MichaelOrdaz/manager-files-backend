<?php

namespace Tests\Feature\DatosFamiliares;

use App\Models\AspiranteStatus;
use App\Models\DatosFamiliares;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\DatosGenerales;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class DatosFamiliaresCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_datosfamiliares_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $response = $this->post("api/v1/usuarios/{$user->id}/datos-familiares",[
        'nombre' => 'pedrito',
        'apellido_paterno' => 'perez',
        'apellido_materno' => 'gonzales',
        'parentesco' => 'papa',
        'vive_con_tutorado' => '1',
        'ocupacion' => 'obrero',
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          "id",
          "usuario_id",
          "usuario",
          "nombre",
          "apellido_paterno",
          "apellido_materno",
          "parentesco",
          "vive_con_tutorado",
          "ocupacion",
        ]
      ]);
    }

    public function test_datosfamiliares_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create();

      $datos = DatosFamiliares::factory()
      ->for($user, 'usuario')
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/datos-familiares/{$datos->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "id",
          "usuario_id",
          "usuario",
          "nombre",
          "apellido_paterno",
          "apellido_materno",
          "parentesco",
          "vive_con_tutorado",
          "ocupacion",
        ]
      ]);
    }

    public function test_datosfamiliares_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create();

      $datos = DatosFamiliares::factory()
      ->for($user, 'usuario')
      ->create();

      $response = $this->put("api/v1/usuarios/{$user->id}/datos-familiares/{$datos->id}",[
        'nombre' => 'pedro',
        'apellido_paterno' => 'sanchez',
        'apellido_materno' => 'gonzales',
        'parentesco' => 'papa',
        'vive_con_tutorado' => '1',
        'ocupacion' => 'obrero',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "id",
          "usuario_id",
          "usuario",
          "nombre",
          "apellido_paterno",
          "apellido_materno",
          "parentesco",
          "vive_con_tutorado",
          "ocupacion",
        ]
      ]);

      $datos->refresh();
      $this->assertEquals($datos->nombre, 'pedro');
      $this->assertEquals($datos->apellido_paterno, 'sanchez');
    }

}