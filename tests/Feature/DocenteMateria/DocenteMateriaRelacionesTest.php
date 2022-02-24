<?php

namespace Tests\Feature\DocenteMateria;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\{
  User,
  Periodo,
  EspecialidadPeriodo,
  Especialidad, 
  EspecialidadPeriodoGrupo,
  Materia,
  Componente,
  DocenteMateria,
  Grupo
};

class DocenteMateriaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $docentemateria;

    public function test_docentemateria_belongs_relationship()
    {
      $periodo = Periodo::factory()->create();
      $especialidad = Especialidad::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for($periodo)
      ->for($especialidad)
      ->create();

      $grupo = Grupo::factory()->create();
      
      $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
        ->for($especialidadPeriodo)
        ->for($grupo)
        ->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        $especialidadPeriodo,
        'especialidad_periodo'
      )
      ->create();

      $docente = User::factory()->create();
      $docente->assignRole('Docente');

      $dm = DocenteMateria::factory()
      ->for($docente)
      ->for($materia)
      ->for($especialidadPeriodoGrupo, 'especialidad_periodo_grupo')
      ->create();

      $this->assertInstanceOf(User::class, $dm->User);
      $this->assertInstanceOf(Materia::class, $dm->Materia);
      $this->assertInstanceOf(EspecialidadPeriodoGrupo::class, $dm->especialidad_periodo_grupo);
    }
}