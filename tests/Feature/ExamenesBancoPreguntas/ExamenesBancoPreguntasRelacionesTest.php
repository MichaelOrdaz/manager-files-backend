<?php

namespace Tests\Feature\ExamenesBancoPreguntas;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\ExamenesBancoPreguntas;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\PreguntaTipo;
use App\Models\User;

class ExamenesBancoPreguntasRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examenesbancopreguntas;

    public function test_examenesbancopreguntas_has_one_model()
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

      $preguntaTipo = PreguntaTipo::factory()->create();

      $banco = ExamenesBancoPreguntas::factory()
      ->for($materia)
      ->for($user)
      ->for($preguntaTipo)
      ->create();

      $this->assertInstanceOf(User::class, $banco->User);
      $this->assertInstanceOf(PreguntaTipo::class, $banco->PreguntaTipo);
      $this->assertInstanceOf(Materia::class, $banco->Materia);
    }
}