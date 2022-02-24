<?php

namespace Tests\Feature\Tarea;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\{
  Tarea,
  User,
  Materia,
  Componente,
  Especialidad,
  EspecialidadPeriodo,
  Periodo,
  Unidad,
  Tema
};
use Illuminate\Http\UploadedFile;

class TareaCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tarea_crud_create()
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

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $file = UploadedFile::fake()->image('avatar.jpg');

      $response = $this->post(
        "api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades/".$unidad->id.
        "/tareas",
        [
        'titulo' => 'titulo de la tarea',
        'descripcion' => 'descripcion breve',
        'archivo' => $file,
        'activo' => 1,
      ]);
      $response->assertStatus(201)
      ->assertJsonFragment(['titulo' => 'titulo de la tarea'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'titulo',
          'descripcion',
          'grupos',
          'temas',
          'creador_id',
          'creador',
          'usuario_id',
          'usuario',
          'unidad_id',
          'unidad',
          'materia_id',
          'materia',
          'archivo_url',
        ]
      ]);
    }

    public function test_tarea_crud_list()
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

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $tarea = \App\Models\Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->count(3)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $response = $this->get(
        "api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades/".$unidad->id.
        "/tareas"
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'titulo',
            'descripcion',
            'grupos',
            'temas',
            'creador_id',
            'creador',
            'usuario_id',
            'usuario',
            'unidad_id',
            'unidad',
            'materia_id',
            'materia',
            'archivo_url',
          ]
        ],
        "success",
      ]);

    }

    public function test_tarea_crud_get()
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

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $tarea = Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $response = $this->get(
        "api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades/".$unidad->id.
        "/tareas/" . $tarea->id
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'titulo',
          'descripcion',
          'grupos',
          'temas',
          'creador_id',
          'creador',
          'usuario_id',
          'usuario',
          'unidad_id',
          'unidad',
          'materia_id',
          'materia',
          'archivo_url',
        ],
        "success",
      ]);
    }

    public function test_tarea_crud_update()
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

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $tarea = Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $file = UploadedFile::fake()->image('avatar2.jpg');

      $response = $this->post(
          "api/v1/especialidades/".
          $materia->especialidad_periodo->id .
          "/materias/".$materia->id.
          "/unidades/".$unidad->id.
          "/tareas/" . $tarea->id,
        [
        'titulo' => 'titulo de la tarea update',
        'descripcion' => 'descripcion breve update',
        'unidad_id' => $tema->id,
        'materia_id' => $materia->id,
        'creador_id' => $user->id,
        'archivo' => $file,
        'activo' => 1,
      ]);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'titulo',
          'descripcion',
          'grupos',
          'temas',
          'creador_id',
          'creador',
          'usuario_id',
          'usuario',
          'unidad_id',
          'unidad',
          'materia_id',
          'materia',
          'archivo_url',
        ],
        "success",
      ]);

      $tarea->refresh();
      $this->assertEquals($tarea->titulo, 'titulo de la tarea update');
      $this->assertEquals($tarea->descripcion, 'descripcion breve update');
    }

    public function test_tarea_crud_copy()
    {

        $user = User::factory()->create();
        $user->assignRole('Admin');
        $this->actingAs($user, 'api');

        $periodo = Periodo::factory()
        ->create();

        $materia = Materia::factory()
        ->for(Componente::factory())
        ->for(
          EspecialidadPeriodo::factory()
          ->for($periodo)
          ->for(Especialidad::factory()),
          'especialidad_periodo'
        )
        ->create();


        $unidad = Unidad::factory()
        ->for($materia)
        ->create();

        $tema = Tema::factory()
        ->for($unidad)
        ->create();

        $tarea = Tarea::factory()
        ->for($unidad)
        ->for($materia)
        ->create([
          'creador_id' =>  $user->id,
          'usuario_id' =>  $user->id,
        ]);


        $urlCopy = "api/v1/especialidades/".
        $materia->especialidad_periodo->especialidad_id .
        "/periodos/" . $periodo->id.
        "/materias/".$materia->id.
        "/unidades/".$unidad->id.
        "/tareas/" . $tarea->id. ':copy';
        $response = $this->post($urlCopy);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
              'id',
              'titulo',
              'descripcion',
              'grupos',
              'temas',
              'creador_id',
              'creador',
              'usuario_id',
              'usuario',
              'unidad_id',
              'unidad',
              'materia_id',
              'materia',
              'archivo_url',
            ],
            "success",
          ]);
    }

    public function test_tarea_crud_delete()
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

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $tarea = \App\Models\Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->count(3)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $response = $this->delete(
        "api/v1/especialidades/".
        $materia->especialidad_periodo->id .
        "/materias/".$materia->id.
        "/unidades/".$unidad->id.
        "/tareas/" . $tarea[0]->id
      );
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'titulo',
          'descripcion',
          'grupos',
          'temas',
          'creador_id',
          'creador',
          'usuario_id',
          'usuario',
          'unidad_id',
          'unidad',
          'materia_id',
          'materia',
          'archivo_url',
        ],
        "success",
      ]);

      $this->assertSoftDeleted($tarea[0]);
    }

}
