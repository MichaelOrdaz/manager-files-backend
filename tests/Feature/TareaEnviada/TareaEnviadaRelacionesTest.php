<?php

namespace Tests\Feature\TareaEnviada;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Materia;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\TareaEnviada;
use App\Models\Model;
use App\Models\Periodo;
use App\Models\Tarea;
use App\Models\Tema;
use App\Models\Unidad;
use App\Models\User;

class TareaEnviadaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tarea_enviada_belong_user()
    {
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

      $user = User::factory()->create();

      $tarea = \App\Models\Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $tareaEnviada = TareaEnviada::factory()
      ->for(User::factory())
      ->for($tarea)
      ->for($materia)
      ->create();

      $this->assertInstanceOf(User::class, $tareaEnviada->User);
    }

    public function test_tarea_enviada_belong_tarea()
    {
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

      $user = User::factory()->create();

      $tarea = \App\Models\Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $tareaEnviada = TareaEnviada::factory()
      ->for(User::factory())
      ->for($tarea)
      ->for($materia)
      ->create();

      $this->assertInstanceOf(Tarea::class, $tareaEnviada->Tarea);
    }

    public function test_tarea_enviada_belong_materia()
    {
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

      $user = User::factory()->create();

      $tarea = \App\Models\Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->create([
        'creador_id' =>  $user->id,
        'usuario_id' =>  $user->id,
      ]);

      $tareaEnviada = TareaEnviada::factory()
      ->for(User::factory())
      ->for($tarea)
      ->for($materia)
      ->create();

      $this->assertInstanceOf(Materia::class, $tareaEnviada->Materia);
    }

}