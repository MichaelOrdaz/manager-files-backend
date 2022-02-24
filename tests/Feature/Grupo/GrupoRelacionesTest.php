<?php

namespace Tests\Feature\Grupo;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Materia;

use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\DatosAcademicos;
use App\Models\EspecialidadPeriodo;
use Illuminate\Support\Facades\Hash;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrupoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $periodo;

    public function test_grupo_has_many_especialidadesPeriodosGrupos(){
      $this->assertInstanceOf(EspecialidadPeriodoGrupo::class,$this->grupo->especialidadesPeriodosGrupos[0]);
    }



    /**
     * @author Enrique Sevilla <sevilla@puller.mx>
     * @version  1.0
     * Primer función a ejecutar del test,
     * Dentro de esta función iniciamos una transacción y
     * creamos todos las entidades que interactúan con nuestro modelo
     */
    public function setUp():void
    {
      parent::setUp();

      $this->grupo = Grupo::factory()->create();
      $usuario = User::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory())
        ->create();

      $materia = Materia::factory()
        ->for(Componente::factory())
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->create();

      EspecialidadPeriodoGrupo::factory()
        ->for($especialidadPeriodo)
        ->for($this->grupo)
        ->create();
    }
}