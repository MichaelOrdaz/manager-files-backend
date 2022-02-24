<?php

namespace Tests\Feature\ConfiguracionAdmision;

use App\Models\Componente;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ConfiguracionAdmision;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenCalificacionStatus;
use App\Models\ExamenTipo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\User;

class ConfiguracionAdmisionCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_configuracionadmision_crud_create()
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

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $response = $this->post("api/v1/configuracion-admision", [
        'activo' => '1',
        'fecha_inicio' => '2021-08-08',
        'fecha_fin' => '2021-08-09',
        'instrucciones' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit.',
        'examen_psicometrico_id' => $examen[0]->id,
        'examen_psicologico_id' => $examen[1]->id,
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
          'data' => [
            "id",
            "activo",
            "fecha_inicio",
            "fecha_fin",
            "instrucciones",
            "convocatoria_url",
            "examen_psicometrico_id",
            "examen_psicometrico",
            "examen_psicologico_id",
            "examen_psicologico",
          ]
      ]);
    }

    public function test_configuracionadmision_crud_list()
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

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $config = ConfiguracionAdmision::factory()
      ->for($examen[0], 'ExamenPsicologico')
      ->for($examen[1], 'ExamenPsicometrico')
      ->create();

      $response = $this->get("api/v1/configuracion-admision");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
            "id",
            "activo",
            "fecha_inicio",
            "fecha_fin",
            "instrucciones",
            "convocatoria_url",
            "examen_psicometrico_id",
            "examen_psicometrico",
            "examen_psicologico_id",
            "examen_psicologico",
        ]]
      ]);
    }

    public function test_configuracionadmision_crud_get()
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

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $config = ConfiguracionAdmision::factory()
      ->for($examen[0], 'ExamenPsicologico')
      ->for($examen[1], 'ExamenPsicometrico')
      ->create();

      $response = $this->get("api/v1/configuracion-admision/{$config->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
            "id",
            "activo",
            "fecha_inicio",
            "fecha_fin",
            "instrucciones",
            "convocatoria_url",
            "examen_psicometrico_id",
            "examen_psicometrico",
            "examen_psicologico_id",
            "examen_psicologico",
        ]
      ]);
    }

    public function test_configuracionadmision_crud_update()
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

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $config = ConfiguracionAdmision::factory()
      ->for($examen[0], 'ExamenPsicologico')
      ->for($examen[1], 'ExamenPsicometrico')
      ->create();

      $response = $this->post("api/v1/configuracion-admision/{$config->id}",[
        'activo' => '1',
        'fecha_inicio' => '2021-08-08',
        'fecha_fin' => '2021-08-09',
        'instrucciones' => 'Lorem ipsum, dolor sit amet consectetur adipisicing elit.',
        'examen_psicometrico_id' => $examen[0]->id,
        'examen_psicologico_id' => $examen[1]->id,
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
            "id",
            "activo",
            "fecha_inicio",
            "fecha_fin",
            "instrucciones",
            "convocatoria_url",
            "examen_psicometrico_id",
            "examen_psicometrico",
            "examen_psicologico_id",
            "examen_psicologico",
        ]
      ]);

      $config->refresh();
      $this->assertEquals($config->fecha_inicio, "2021-08-08");
    }

    public function test_configuracionadmision_crud_delete()
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

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $config = ConfiguracionAdmision::factory()
      ->for($examen[0], 'ExamenPsicologico')
      ->for($examen[1], 'ExamenPsicometrico')
      ->create();

      $response = $this->delete("api/v1/configuracion-admision/{$config->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
            "id",
            "activo",
            "fecha_inicio",
            "fecha_fin",
            "instrucciones",
            "convocatoria_url",
            "examen_psicometrico_id",
            "examen_psicometrico",
            "examen_psicologico_id",
            "examen_psicologico",
        ]
      ]);
      $this->assertSoftDeleted($config);
    }

}