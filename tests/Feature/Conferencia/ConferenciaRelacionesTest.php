<?php

namespace Tests\Feature\Conferencia;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Unidad;
use App\Models\Tema;

use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConferenciaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $conferencia;

    public function test_conferencia_has_one_model()
    {
      $user = User::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      $grupo = Grupo::factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $conferencia = Conferencia::factory()
      ->for($user)
      ->for($materia)
      ->for($epg)
      ->for($unidad)
      ->for($tema)
      ->create();

      $this->assertInstanceOf(User::class, $conferencia->User);
      $this->assertInstanceOf(Grupo::class, $conferencia->EspecialidadPeriodoGrupo->grupo);
      $this->assertInstanceOf(Materia::class, $conferencia->Materia);
      $this->assertInstanceOf(Unidad::class, $conferencia->Unidad);
      $this->assertInstanceOf(Tema::class, $conferencia->Tema);
    }
}
