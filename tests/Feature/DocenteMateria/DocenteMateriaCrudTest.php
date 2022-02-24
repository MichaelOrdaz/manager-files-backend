<?php

namespace Tests\Feature\DocenteMateria;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\{
  User,
  Periodo,
  EspecialidadPeriodo,
  Especialidad,
  EspecialidadPeriodoGrupo,
  Materia,
  Componente,
  DocenteMateria,
  Grupo
};

class DocenteMateriaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_docentemateria_bind_fail_validate()
    {
      $admin = User::factory()->create();
      $admin->assignRole('Admin');
      $this->actingAs($admin, 'api');

      $response = $this->post("api/v1/docentes/100/materias/100:bind", [
        'especialidad_id' => 100,
        'periodo_id' => 100,
      ]);

      $response->assertStatus(422);
      $response->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_docentemateria_unbind_fail_validate()
    {
      $admin = User::factory()->create();
      $admin->assignRole('Admin');
      $this->actingAs($admin, 'api');

      $response = $this->post("api/v1/docentes/100/materias/100:unbind", [
        'especialidad_id' => 100,
      ]);

      $response->assertStatus(422);
      $response->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_docentemateria_bind_ok()
    {
      $admin = User::factory()->create();
      $admin->assignRole('Admin');
      $this->actingAs($admin, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $grupo = Grupo::factory()->create();

      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for($grupo)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $docente = User::factory()->create();
      $docente->assignRole('Docente');

      $response = $this->post("api/v1/docentes/{$docente->id}/materias/{$materia->id}:bind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
        'grupo_id' => $grupo->id,
      ]);

      $response->assertStatus(201);
      $response->assertJsonStructure([
        'message',
        'success',
        'data' => [
          'nombre',
          'apellido_paterno',
          'apellido_materno',
          'email',
          'materia',
          'periodo',
          'grupo',
          'especialidad',
        ],
      ]);
    }

    public function test_docentemateria_bind_fail_role()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $grupo = Grupo::factory()->create();

      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for($grupo)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $docente = User::factory()->create();
      $docente->assignRole('Docente');

      $response = $this->post("api/v1/docentes/{$docente->id}/materias/{$materia->id}:bind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
        'grupo_id' => $grupo->id,
      ]);

      $response->assertStatus(403);
      $response->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_docentemateria_bind_fail_exist()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $grupo = Grupo::factory()->create();

      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo)
      ->for($grupo)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $docente = User::factory()->create();
      $docente->assignRole('Docente');

      DocenteMateria::factory()
      ->for($docente)
      ->for($materia)
      ->for($especialidadPeriodoGrupo, 'especialidad_periodo_grupo')
      ->create();

      $response = $this->post("api/v1/docentes/{$docente->id}/materias/{$materia->id}:bind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
        'grupo_id' => $grupo->id,
      ]);

      $response->assertStatus(422);
      $response->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_docentemateria_unbind_ok()
    {
      $admin = User::factory()->create();
      $admin->assignRole('Admin');
      $this->actingAs($admin, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $grupo = Grupo::factory()->create();

      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
        ->for($especialidadPeriodo)
        ->for($grupo)
        ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $docente = User::factory()->create();
      $docente->assignRole('Docente');

      DocenteMateria::factory()
      ->for($docente)
      ->for($materia)
      ->for($especialidadPeriodoGrupo, 'especialidad_periodo_grupo')
      ->create();

      $response = $this->post("api/v1/docentes/{$docente->id}/materias/{$materia->id}:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
        'grupo_id' => $grupo->id,
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'message',
        'success',
        'data' => [
          'nombre',
          'apellido_paterno',
          'apellido_materno',
          'email',
          'materia',
          'periodo',
          'grupo',
          'especialidad',
        ],
      ]);
    }

    public function test_docentemateria_unbind_fail_no_existe()
    {
      $admin = User::factory()->create();
      $admin->assignRole('Admin');
      $this->actingAs($admin, 'api');

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $grupo = Grupo::factory()->create();

      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
        ->for($especialidadPeriodo)
        ->for($grupo)
        ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $docente = User::factory()->create();
      $docente->assignRole('Docente');

      $response = $this->post("api/v1/docentes/{$docente->id}/materias/{$materia->id}:unbind", [
        'especialidad_id' => $especialidad->id,
        'periodo_id' => $periodo->id,
        'grupo_id' => $grupo->id,
      ]);

      $response->assertStatus(404);
      $response->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

}