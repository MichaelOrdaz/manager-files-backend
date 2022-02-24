<?php

namespace Tests\Feature\Tema;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;

use App\Models\Tarea;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\TareaTema;
use App\Models\Componente;
use App\Models\ExamenTema;
use App\Models\ExamenTipo;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\MaterialTipo;
use App\Models\MaterialDidactico;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $tema;

    public function test_tema_has_one_materia()
    {
      $this->assertInstanceOf(Materia::class,$this->tema->Materia);
    }

    public function test_tema_has_one_unidad()
    {
      $this->assertInstanceOf(Unidad::class,$this->tema->Unidad);
    }

    public function test_has_one_materialDidactico()
    {
      $this->assertInstanceOf(MaterialDidactico::class,$this->tema->materialDidactico[0]);
    }

    public function test_has_many_tutoria()
    {
      $this->assertContainsOnlyInstancesOf(Tutoria::class,$this->tema->tutorias);
    }

    public function test_has_many_tarea_tema()
    {
      $this->assertInstanceOf(TareaTema::class,$this->tema->tarea_tema[0]);
    }

    public function test_has_many_tareas()
    {
      $this->assertInstanceOf(Tarea::class,$this->tema->tareas[0]);
    }

    public function test_has_many_examenes()
    {
      $this->assertInstanceOf(Examen::class,$this->tema->Examenes[0]);
    }

    public function setUp():void{
      parent::setUp();

      $especialidad = Especialidad::factory()->create();
      $periodo = Periodo::factory()->create();
      $componente = Componente::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($periodo)
        ->for($especialidad)
        ->create();

      $materia = Materia::factory()
        ->for($especialidadPeriodo,'especialidad_periodo')
        ->for($componente)
        ->has(Unidad::factory(), 'unidades')
        ->create();

      $unidad = $materia->unidades->first();
      $this->tema = Tema::factory()->for($unidad)->create();

      $materialDidactico = MaterialDidactico::factory()
        ->for($this->tema)
        ->forCreador()
        ->for( MaterialTipo::factory() , 'MaterialesTipo' )
        ->create();

      Tutoria::factory()
        ->for(User::factory())
        ->for($materia)
        ->for($materialDidactico, 'Material')
        ->for(Grupo::factory())
        ->for($this->tema)
        ->create();

      $especialidad = Especialidad::factory()->create();
      $periodo = Periodo::factory()->create();
      $componente = Componente::factory()->create();

      $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($periodo)
        ->for($especialidad)
        ->create();

      $grupo = Grupo::Factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();

      $materia = Materia::factory()
        ->for( $especialidadPeriodo , 'especialidad_periodo' )
        ->for( $componente )
        ->has( Unidad::factory()->count(4) , 'unidades' )
        ->has( Examen::factory()->forUser()->for(ExamenTipo::factory())->count(3) , 'examenes' )
        ->create();

        Conferencia::factory()
          ->for($materia)
          ->for($unidad)
          ->for($this->tema)
          ->for($epg)
          ->count(5)
          ->create();
      TareaTema::factory()
        ->for(
          Tarea::factory()->state([
            'creador_id' => User::factory()->create()->id,
            'unidad_id' => $materia->unidades->first()->id,
            'materia_id' => $materia->id,
          ])
        )
      ->for($this->tema)
      ->create();

      ExamenTema::factory()
      ->for($materia->examenes[0])
      ->for($this->tema)
      ->create();

    }
}
