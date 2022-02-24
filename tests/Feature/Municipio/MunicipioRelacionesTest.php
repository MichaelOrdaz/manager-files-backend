<?php

namespace Tests\Feature\Municipio;

use App\Models\Estado;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Municipio;
use App\Models\Model;

class MunicipioRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $municipio;

    public function test_municipio_belong_estado()
    {
      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()
        ->for($estado)
      ->create();

      $this->assertInstanceOf(Estado::class, $municipio->Estado);
    }
}