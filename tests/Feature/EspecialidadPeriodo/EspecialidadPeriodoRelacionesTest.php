<?php

namespace Tests\Feature\EspecialidadPeriodo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Model;
use App\Models\ExamenCalificacion;
use App\Models\Componente;
use App\Models\ContenidosExtra;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use App\Models\Examen;
use App\Models\ExamenCalificacionStatus;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\ExamenTipo;
use App\Models\Grupo;
use App\Models\MaterialTipo;
use App\Models\User;


class EspecialidadPeriodoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $especialidadperiodo;

    public function test_especialidadperiodo_has_one_model()
    {
      $user = User::factory()->create();

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadperiodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->has(
        ContenidosExtra::factory()
        ->for(MaterialTipo::factory(), 'MaterialesTipo'),
        'contenidoExtra'
      )
      ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadperiodo, 'especialidad_periodo')
      ->create();

      EspecialidadPeriodoGrupo::factory()
      ->for($especialidadperiodo, 'EspecialidadPeriodo')
      ->for(Grupo::factory())
      ->create();

      $this->assertInstanceOf(Periodo::class, $especialidadperiodo->periodo);
      $this->assertInstanceOf(Especialidad::class, $especialidadperiodo->especialidad);
      $this->assertInstanceOf(Materia::class, $especialidadperiodo->materias[0]);
      $this->assertInstanceOf(ContenidosExtra::class, $especialidadperiodo->contenidoExtra[0]);
      $this->assertInstanceOf(EspecialidadPeriodoGrupo::class, $especialidadperiodo->especialidadPeriodoGrupo[0]);
    }
}