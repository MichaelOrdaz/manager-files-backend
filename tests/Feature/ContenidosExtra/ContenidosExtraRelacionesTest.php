<?php

namespace Tests\Feature\ContenidosExtra;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\ContenidosExtra;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\MaterialTipo;
use App\Models\Model;
use App\Models\Periodo;
use App\Models\User;

class ContenidosExtraRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $contenidosextra;

    public function test_contenidosextra_has_one_model()
    {
      $user = User::factory()->create();

      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $EspecialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $tipo = MaterialTipo::factory()->create();

      $contenido = ContenidosExtra::factory()
      ->for($EspecialidadPeriodo, 'especialidad_periodo')
      ->for($tipo, 'MaterialesTipo')
      ->create();

      $this->assertInstanceOf(EspecialidadPeriodo::class, $contenido->especialidad_periodo);
      $this->assertInstanceOf(MaterialTipo::class, $contenido->MaterialesTipo
    );
    }
}