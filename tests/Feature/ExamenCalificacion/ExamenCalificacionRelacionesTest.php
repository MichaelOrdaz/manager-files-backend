<?php

namespace Tests\Feature\ExamenCalificacion;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ExamenCalificacion;
use App\Models\Model;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenCalificacionStatus;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\ExamenTipo;
use App\Models\User;


class ExamenCalificacionRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examencalificacion;

    public function test_examencalificacion_has_one_model()
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

      $this->assertInstanceOf(User::class, $calificacion->usuario);
      $this->assertInstanceOf(ExamenCalificacionStatus::class, $calificacion->status);
      $this->assertInstanceOf(Examen::class, $calificacion->examen);
    }

}