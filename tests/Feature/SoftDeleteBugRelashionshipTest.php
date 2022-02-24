<?php

namespace Tests\Feature;

use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Periodo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SoftDeleteBugRelashionshipTest extends TestCase
{
    use DatabaseTransactions;

    public function test_relacion_especialidad_periodo ()
    {
        $periodo1 = Periodo::factory()->create();
        $periodo2 = Periodo::factory()->create();

        $especialidad1 = Especialidad::factory()->create();
        $especialidad2 = Especialidad::factory()->create();
        $especialidad3 = Especialidad::factory()->create();


        EspecialidadPeriodo::factory()
        ->for($periodo1)
        ->for($especialidad1)
        ->create();

        EspecialidadPeriodo::factory()
        ->for($periodo1)
        ->for($especialidad2)
        ->create();

        EspecialidadPeriodo::factory()
        ->for($periodo2)
        ->for($especialidad1)
        ->create();

        EspecialidadPeriodo::factory()
        ->for($periodo2)
        ->for($especialidad3)
        ->create();


        $count1 = $especialidad1->periodos()->count();
        $count2 = $especialidad2->periodos()->count();

        $this->assertEquals(2, $count1);
        $this->assertEquals(1, $count2);

        $periodo1->delete();
        $this->assertSoftDeleted($periodo1);

        $count1 = $especialidad1->periodos()->count();
        $count2 = $especialidad2->periodos()->count();

        $this->assertEquals(1, $count1);
        $this->assertEquals(0, $count2);

    }

    public function test_relacion_estados_municipios()
    {
        Estado::factory()->count(5)
        ->has(Municipio::factory()->count(5))
        ->create();

        $estado = Estado::first();
        $count = $estado->municipios()->count();

        $estado->delete();
        $this->assertSoftDeleted($estado);

        $municipio = Municipio::latest()->first();
        $estado_id = $municipio->estado_id;
        $municipio->delete();

        $this->assertSoftDeleted($municipio);
    }
}
