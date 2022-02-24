<?php

namespace Tests\Feature\DatosGenerales;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\DatosGenerales;
use App\Models\Estado;
use App\Models\Model;
use App\Models\Municipio;
use App\Models\User;

class DatosGeneralesRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $datosgenerales;

    public function test_datosgenerales_has_relation()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create(); 

      $datos = DatosGenerales::factory()
      ->for($user, 'usuario')
      ->for($estado)
      ->for($municipio)
      ->create();

      $this->assertInstanceOf(User::class, $datos->usuario);
      $this->assertInstanceOf(Estado::class, $datos->estado);
      $this->assertInstanceOf(Municipio::class, $datos->municipio);
    }

}