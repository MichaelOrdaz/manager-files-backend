<?php

namespace Tests\Feature\Examen;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\{
    Materia,
    Componente,
    ConfiguracionAdmision,
    EspecialidadPeriodo,
    Especialidad,
    Periodo,
    User,
    Examen,
    ExamenCalificacion,
    ExamenCalificacionStatus,
    ExamenPregunta,
    ExamenRespuesta,
    ExamenTipo,
    PreguntaTipo,
    ExamenTema,
    Tema,
    Unidad,
    ExamenGrupo,
    EspecialidadPeriodoGrupo,
    Grupo,
};

class ExamenRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $examen;

    public function test_examen_has_belongs_relations()
    {
      $user = User::factory()->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->has(Unidad::factory(), 'unidades')
      ->create();

      $examenTipo = ExamenTipo::factory()->create();
      $examen = Examen::factory()
      ->for($user)
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $unidad = $materia->unidades->first();
      $tema = Tema::factory()->for($unidad)->create();

      ExamenTema::factory()
      ->for($examen)
      ->for($tema)
      ->create();

      ExamenGrupo::factory()
      ->for(
        EspecialidadPeriodoGrupo::factory()
        ->for(
          EspecialidadPeriodo::factory()
          ->for(Periodo::factory())
          ->for(Especialidad::factory())
        )
        ->for(Grupo::factory())
      )
      ->for($examen)
      ->create();

      $this->assertInstanceOf(User::class,$examen->User);
      $this->assertInstanceOf(ExamenTipo::class,$examen->examenTipo);
      $this->assertInstanceOf(Materia::class,$examen->materia);
      $this->assertInstanceOf(Tema::class,$examen->Temas[0]);
      $this->assertInstanceOf(ExamenGrupo::class,$examen->ExamenesGrupo[0]);

    }

    public function test_examen_has_config_admision()
    {
      $user = User::factory()->create();

      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $examenTipo = ExamenTipo::factory()->create();

      $examen2 = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $examen3 = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->create();

      $examen = Examen::factory()
      ->for(User::factory())
      ->for($materia)
      ->for($examenTipo)
      ->has(ConfiguracionAdmision::factory()->for($examen2, 'ExamenPsicologico')->for($examen3, 'ExamenPsicometrico'))
      ->create();

      $examenPregunta = ExamenPregunta::factory()
      ->for($user)
      ->for($examen)
      ->for(PreguntaTipo::factory())
      ->create();

      $examenRespuesta = ExamenRespuesta::factory()
      ->for($user)
      ->for($examenPregunta)
      ->for($examen)
      ->create();

      $status = ExamenCalificacionStatus::factory()->create();
      $calificacion = ExamenCalificacion::factory()
      ->for($user, 'usuario')
      ->for($status, 'status')
      ->for($examen)
      ->create();

      $this->assertInstanceOf(ConfiguracionAdmision::class, $examen->configuracionAdmision);
      $this->assertInstanceOf(ExamenCalificacion::class, $examen->examenCalificacion->first());
      $this->assertInstanceOf(ExamenPregunta::class, $examen->examenPreguntas[0]);
      $this->assertInstanceOf(ExamenRespuesta::class, $examen->examenRespuesta);

    }
}