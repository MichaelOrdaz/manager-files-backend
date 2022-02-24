<?php

namespace Tests\Feature\Conferencia;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\Componente;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use App\Models\Unidad;
use App\Models\Tema;
use Faker\Factory;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenciaCrudTest extends TestCase
{
    use DatabaseTransactions;
    protected $faker;

    public function test_conferencia_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      $grupo = Grupo::factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $conferencia = Conferencia::factory()
      ->for($user)
      ->for($materia)
      ->for($epg)
      ->for($unidad)
      ->for($tema)
      ->create();

      $response = $this->get("api/v1/conferencias");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [[
            "id",
            "nombre",
            "usuario_id",
            "usuario",
            "status",
            "meeting_id",
            "materia_id",
            "materia",
            "unidad_id",
            "unidad",
            "tema_id",
            "tema",
            "grupo",
            "meeting_password",
            "usuario",
            "zoom_start_time"
        ]]
      ]);
    }

    public function test_conferencia_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      $grupo = Grupo::factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $conferencia = Conferencia::factory()
      ->for($user)
      ->for($materia)
      ->for($epg)
      ->for($unidad)
      ->for($tema)
      ->create();

      $response = $this->get("api/v1/conferencias/{$conferencia->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
            "id",
            "nombre",
            "usuario_id",
            "usuario",
            "status",
            "meeting_id",
            "materia_id",
            "materia",
            "unidad_id",
            "unidad",
            "tema_id",
            "tema",
            "grupo",
            "meeting_password",
            "usuario",
            "zoom_start_time"
        ]
      ]);
    }

    public function test_conferencia_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      $grupo = Grupo::factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $conferencia = Conferencia::factory()
      ->for($user)
      ->for($materia)
      ->for($epg)
      ->for($unidad)
      ->for($tema)
      ->create();

      $response = $this->put("api/v1/conferencias/{$conferencia->id}",[
        'nombre' => 'lorem update',
        'usuario_id' => $user->id,
        'status' => 'En vivo',
        'meeting_uid' => 'lorem',
        'meeting_password' => 'lorem',
        'materia_id' => $materia->id,
        'unidad_id' => $unidad->id,
        'tema_id' => $tema->id,
        'zoom_start_time' => $this->faker->dateTimeThisMonth()->format('Y-m-d h:i'),
        'especialidad_periodo_grupo_id' => $epg->id,
        'activo' => 1,
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
            "id",
            "nombre",
            "usuario_id",
            "usuario",
            "status",
            "meeting_id",
            "materia_id",
            "materia",
            "unidad_id",
            "unidad",
            "tema_id",
            "tema",
            "grupo",
            "meeting_password",
            "usuario",
            "zoom_start_time"
        ]
      ]);

      $conferencia->refresh();
      $this->assertEquals($conferencia->nombre, "lorem update");
    }

    public function conferencia_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      $grupo = Grupo::factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $conferencia = Conferencia::factory()
      ->for($user)
      ->for($materia)
      ->for($epg)
      ->for($unidad)
      ->for($tema)
      ->create();

      $response = $this->delete("api/v1/conferencias/{$conferencia->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
            "id",
            "nombre",
            "usuario_id",
            "usuario",
            "status",
            "meeting_id",
            "materia_id",
            "materia",
            "unidad_id",
            "unidad",
            "tema_id",
            "tema",
            "grupo",
            "meeting_password",
            "usuario",
            "zoom_start_time"
        ]
      ]);

      $this->assertSoftDeleted($conferencia);
    }

    public function conferencia_endpoint_search()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      $grupo = Grupo::factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $conferencia = Conferencia::factory()
      ->for($user)
      ->for($materia)
      ->for($unidad)
      ->for($tema)
      ->for($epg)
      ->create(['status' => 'Grabada']);

      $response = $this->get(
        "api/v1/conferencias:search?" .
        "materiaId=" . $materia->id .
        "&grupoId=" . $epg->id .
        "&docenteId=" . $user->id .
        "&conferenciaStatus=Grabada"
      );
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
            "id",
            "nombre",
            "usuario_id",
            "usuario",
            "status",
            "meeting_id",
            "materia_id",
            "materia",
            "unidad_id",
            "unidad",
            "tema_id",
            "tema",
            "grupo",
            "meeting_password",
            "usuario",
            "zoom_start_time"
        ]]
      ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }
}
