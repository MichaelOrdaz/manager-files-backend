<?php

namespace Tests\Feature\CalificacionParcial;

use Tests\TestCase;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Componente;
use App\Models\Especialidad;
use Illuminate\Http\UploadedFile;
use App\Models\CalificacionParcial;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CalifiacionParcialRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $calificacionParcial,$materia;

    public function test_califacion_parcial_belongs_to_materia()
    {
      $this->assertInstanceOf(Materia::class,$this->calificacionParcial->Materia);
      $this->assertEquals($this->calificacionParcial->materia_nombre,$this->materia->nombre);
    }

    public function test_califacion_parcial_belongs_to_alumno()
    {
      $this->assertInstanceOf(User::class,$this->calificacionParcial->User);
    }

    public function setUp():void
    {
      parent::setUp();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $grupo = Grupo::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $this->materia = Materia::factory()
      ->for(Componente::factory())
      ->for($especialidadPeriodo,'especialidad_periodo')
      ->create();

      $alumno = User::factory()->create();

      $this->calificacionParcial = CalificacionParcial::factory()
      ->ponerMateriaNombre($this->materia->nombre)
      ->ponerEspecialidadPeriodoGrupo($epg)
      ->for($epg)
      ->for($this->materia)
      ->for($alumno)
      ->create();

    }
}
