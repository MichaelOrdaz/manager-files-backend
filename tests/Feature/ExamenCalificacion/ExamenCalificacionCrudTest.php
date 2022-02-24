<?php

namespace Tests\Feature\ExamenCalificacion;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenPregunta;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenCalificacion;
use App\Models\ExamenCalificacionStatus;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\ExamenTipo;
use App\Models\PreguntaTipo;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ExamenCalificacionCrudTest extends TestCase
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
      $examen = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $status = ExamenCalificacionStatus::factory()->create();

      $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-calificaciones", [
        'usuario_id' => $user->id,
        'examen_id' => $examen->id,
        'status_id' => $status->id,
        'calificacion_maxima' => 100,
        'calificacion_obtenida' => 78,
        'activo' => 1,
      ]);

      $response->assertStatus(201);
      $response->assertJsonFragment(['calificacion_maxima' => 100]);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'examen',
          'status',
          'calificacion_maxima',
          'calificacion_obtenida',
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
      $examen = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->has(ExamenCalificacion::factory()->for($user, 'usuario')->for($status, 'status')->count(2))
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-calificaciones");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [[
          'id',
          'usuario',
          'examen',
          'status',
          'calificacion_maxima',
          'calificacion_obtenida',
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

      $calificacion = ExamenCalificacion::factory()
      ->for($user, 'usuario')
      ->for(ExamenCalificacionStatus::factory(), 'status')
      ->for($examen)
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-calificaciones/{$calificacion->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'examen',
          'status',
          'calificacion_maxima',
          'calificacion_obtenida',
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

      $status = ExamenCalificacionStatus::factory()->create();
      $calificacion = ExamenCalificacion::factory()
      ->for($user, 'usuario')
      ->for($status, 'status')
      ->for($examen)
      ->create();

      $response = $this->put("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-calificaciones/{$calificacion->id}", [
        'usuario_id' => $user->id,
        'examen_id' => $examen->id,
        'status_id' => $status->id,
        'calificacion_maxima' => 100,
        'calificacion_obtenida' => 81,
        'activo' => 1,
      ]);

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'examen',
          'status',
          'calificacion_maxima',
          'calificacion_obtenida',
        ],
        'message',
        'success'
      ]);

      $calificacion->refresh();
      $this->assertEquals($calificacion->calificacion_obtenida, 81);
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

      $status = ExamenCalificacionStatus::factory()->create();
      $calificacion = ExamenCalificacion::factory()
      ->for($user, 'usuario')
      ->for($status, 'status')
      ->for($examen)
      ->create();

      $response = $this->delete("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/examenes-calificaciones/{$calificacion->id}");

      $response->assertOk();
      $response->assertJsonStructure([
        'data' => [
          'id',
          'usuario',
          'examen',
          'status',
          'calificacion_maxima',
          'calificacion_obtenida',
        ],
        'message',
        'success'
      ]);
      $this->assertSoftDeleted($calificacion);
    }
}