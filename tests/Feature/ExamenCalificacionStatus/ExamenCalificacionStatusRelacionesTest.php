<?php

namespace Tests\Feature\ExamenCalificacionStatus;

use App\Models\Examen;
use App\Models\ExamenCalificacion;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenCalificacionStatus;
use App\Models\Model;
use App\Models\User;
use App\Models\{
  Materia,
  Componente,
  Especialidad,
  EspecialidadPeriodo,
  Periodo,
  ExamenTipo,
  
};

class ExamenCalificacionStatusRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examencalificacionstatus;

    public function test_examencalificacionstatus_has_relation()
    {
      $status = ExamenCalificacionStatus::factory()
      ->create();

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

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      ExamenCalificacion::factory()
      ->for($status, 'status')
      ->for($examen)
      ->for($user, 'usuario')
      ->count(3)
      ->create();

      foreach ($status->examenes_calificaciones as $test) {
        $this->assertInstanceOf(ExamenCalificacion::class, $test);
      }
    }
}