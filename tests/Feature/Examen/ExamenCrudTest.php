<?php

namespace Tests\Feature\Examen;

use Tests\TestCase;
use App\Models\Tema;

use App\Models\User;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\Componente;
use App\Models\ExamenTema;

use App\Models\ExamenTipo;
use App\Models\Especialidad;
use App\Models\ExamenCalificacion;
use App\Models\EspecialidadPeriodo;
use App\Models\ExamenCalificacionStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExamenCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_examencalificacion_crud_create()
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

      $response = $this->post("api/v1/usuarios/{$user->id}/examenes", [
        'nombre' => 'examen de matematicas',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'tipo_id' => $examenTipo->id,
        'materia_id' => $materia->id,
        'duracion_minutos' => '60',
        'aleatorio' => '1',
        'puntaje_minimo' => '100',
        'activo' => '1',
      ]);

      $response->assertStatus(201);
      $response->assertJsonFragment(['duracion_minutos' => '60']);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'aleatorio',
          'lecciones_referencia',
          'puntaje_minimo',
        ],
      ]);
    }

    public function test_examencalificacion_crud_create_nullables_fields()
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

      $response = $this->post("api/v1/usuarios/{$user->id}/examenes", [
        'nombre' => 'examen de matematicas',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'tipo_id' => $examenTipo->id,
        'materia_id' => $materia->id,
        'aleatorio' => '1',
        'activo' => '1',
      ]);

      $response->assertStatus(201);
      $response->assertJsonFragment(['duracion_minutos' => null]);
      $response->assertJsonFragment(['puntaje_minimo' => null]);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'aleatorio',
          'lecciones_referencia',
          'puntaje_minimo',
        ],
      ]);
    }

    public function test_examencalificacion_crud_list()
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

      $status = ExamenCalificacionStatus::factory()->create();

      $examenTipo = ExamenTipo::factory()->create();
      Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(3)
      ->create();


      $response = $this->get("api/v1/usuarios/{$user->id}/examenes");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'aleatorio',
          'lecciones_referencia',
          'puntaje_minimo',
        ]],
        'message',
        'success'
      ]);
    }

    public function test_examencalificacion_crud_get()
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
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'aleatorio',
          'lecciones_referencia',
          'puntaje_minimo',
        ],
        'message',
        'success'
      ]);
    }

    public function test_examencalificacion_crud_update()
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
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $response = $this->put("api/v1/usuarios/{$user->id}/examenes/{$examen->id}", [
        'nombre' => 'examen de matematicas',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'tipo_id' => $examenTipo->id,
        'materia_id' => $materia->id,
        'duracion_minutos' => '60',
        'aleatorio' => '1',
        'activo' => '1',
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'aleatorio',
          'lecciones_referencia',
          'puntaje_minimo',
        ],
        'message',
        'success'
      ]);

      $examen->refresh();
      $this->assertEquals($examen->nombre, 'examen de matematicas');
    }

    public function test_examencalificacion_crud_delete()
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
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $tema = Tema::factory()
      ->for(Unidad::factory()->for($materia))
      ->create();

      $examenTema = ExamenTema::factory()
      ->for($examen)
      ->for($tema)
      ->create();

      $response = $this->delete("api/v1/usuarios/{$user->id}/examenes/{$examen->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'usuario_id',
          'usuario',
          'materia_id',
          'materia',
          'tipo_id',
          'tipo',
          'duracion_minutos',
          'aleatorio',
          'lecciones_referencia',
          'puntaje_minimo',
        ],
        'message',
        'success'
      ]);
      $this->assertSoftDeleted($examen);
      $this->assertSoftDeleted($examenTema);
    }
}