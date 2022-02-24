<?php

namespace Tests\Feature\Estado;

use App\Models\DatosGenerales;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\User;

class EstadoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_estado_has_municipios()
    {
      $estados = Estado::factory()->count(3)
      ->has(Municipio::factory()->count(3), 'municipios')
      ->create();

      foreach ($estados as $estado) {
        $this->assertInstanceOf(Estado::class, $estado);
        foreach ($estado->municipios as $municipio) {
          $this->assertInstanceOf(Municipio::class, $municipio);
        }
      }
    }

    public function test_estado_has_datos_generales()
    {
      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()
        ->for($estado)
      ->create();

      $user = User::factory()
      ->has(
        DatosGenerales::factory()
        ->for($municipio, 'municipio')
        ->for($estado, 'estado')
      )
      ->create();

      $this->assertInstanceOf(DatosGenerales::class, $estado->datosGenerales);
    }

    public function test_estado_has_municipio()
    {
      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()
        ->for($estado)
      ->create();

      $user = User::factory()
      ->has(
        DatosGenerales::factory()
        ->for($municipio, 'municipio')
        ->for($estado, 'estado')
      )
      ->create();

      $this->assertInstanceOf(Municipio::class, $estado->municipio);
    }

}