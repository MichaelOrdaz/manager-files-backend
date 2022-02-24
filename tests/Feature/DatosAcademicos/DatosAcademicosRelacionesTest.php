<?php

namespace Tests\Feature\DatosAcademicos;

use App\Models\AspiranteStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\DatosAcademicos;
use App\Models\Model;
use App\Models\User;

class DatosAcademicosRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $datosacademicos;

    public function test_datosacademicos_has_one_model()
    {
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $status = AspiranteStatus::factory()->create();

      $datos = DatosAcademicos::factory()
      ->for($user)
      ->for($status, 'Status')
      ->create();

      $this->assertInstanceOf(AspiranteStatus::class,$datos->Status);
      $this->assertInstanceOf(User::class,$datos->User);
    }
}