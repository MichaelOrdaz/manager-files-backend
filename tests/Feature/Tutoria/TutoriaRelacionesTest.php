<?php

namespace Tests\Feature\Tutoria;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Unidad;

use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\MaterialDidactico;
use App\Models\MaterialTipo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TutoriaRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $tutoria;

    public function test_tutoria_belongs_to_user()
    {
      $this->assertInstanceOf(User::class,$this->tutoria->User);
    }

    public function test_tutoria_belongs_to_tema()
    {
      $this->assertInstanceOf(Tema::class,$this->tutoria->tema);
    }

    public function test_tutoria_belongs_to_grupo()
    {
      $this->assertInstanceOf(Grupo::class,$this->tutoria->grupo);
    }

    public function test_tutoria_has_many_respuestas_tutoria()
    {
      $this->assertInstanceOf(Tutoria::class,$this->tutoria->RespuestasTutoria->first());
    }

    public function test_tutoria_belongs_to_materia()
    {
      $this->assertInstanceOf(Materia::class,$this->tutoria->Materia);
    }

    public function setUp():void
    {
      parent::setUp();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();
      $tema = Tema::factory()->for(Unidad::factory()->for($materia))->create();
      $material = MaterialDidactico::factory()->for(User::factory(), 'creador')
        ->for(MaterialTipo::all()->random(),'MaterialesTipo')
        ->for($tema, 'tema')->create();

      $this->tutoria = Tutoria::factory()
        ->for(User::factory())
        ->for($material, 'material')
        ->for($tema, 'tema')
        ->for($materia)
        ->for(Grupo::factory())
        ->create();

        Tutoria::factory()
        ->for(User::factory())
        ->for($material, 'material')
        ->for($tema, 'tema')
        ->for($materia)
        ->for(Grupo::factory())
        ->count(4)
        ->create(['respuesta_tutoria_id' => $this->tutoria->id]);

    }

}
