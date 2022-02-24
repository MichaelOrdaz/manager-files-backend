<?php

namespace Tests\Feature\ConfiguracionAdmision;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ConfiguracionAdmision;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenCalificacionStatus;
use App\Models\ExamenTipo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\User;
use App\Models\Componente;
class ConfiguracionAdmisionRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $configuracionadmision;

    public function test_configuracionadmision_has_one_model()
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
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->count(2)
      ->create();

      $config = ConfiguracionAdmision::factory()
      ->for($examen[0], 'ExamenPsicologico')
      ->for($examen[1], 'ExamenPsicometrico')
      ->create();

      $this->assertInstanceOf(Examen::class, $config->ExamenPsicologico);
      $this->assertInstanceOf(Examen::class, $config->ExamenPsicometrico);
    }

}