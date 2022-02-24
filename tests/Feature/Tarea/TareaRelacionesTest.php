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
  Tema,
  TareaEnviada
};

class TareaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $tarea;

    public function test_tarea_belong_creador()
    {
      $user = User::factory()->create();

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
        "creador_id" => $user->id,
        "usuario_id" => $user->id,
      ]);

      $this->assertInstanceOf(User::class, $tarea->usuario);
      $this->assertInstanceOf(User::class, $tarea->creador);
    }

    public function test_tarea_belong_unidad()
    {
      $user = User::factory()->create();

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

      $tarea = Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        "creador_id" => $user->id,
      ]);

      $this->assertInstanceOf(Unidad::class, $tarea->unidad);
    }

    public function test_tarea_belong_materia()
    {
      $user = User::factory()->create();

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

      $tarea = Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        "creador_id" => $user->id,
      ]);

      $this->assertInstanceOf(Materia::class, $tarea->Materia);
    }

    public function test_tarea_has_tareas_enviadas()
    {
      $user = User::factory()->create();

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
        "creador_id" => $user->id,
      ]);

      TareaEnviada::factory()
      ->for(User::factory())
      ->for($tarea)
      ->for($materia)
      ->count(3)
      ->create();

      foreach ($tarea->tareasEnviada as $tareaEnviada) {
        $this->assertInstanceOf(TareaEnviada::class, $tareaEnviada);
      }
    }

}