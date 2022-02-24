<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Aviso;
use App\Models\Grupo;
use App\Models\Model;
use App\Models\Tarea;
use App\Models\Estado;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\Encuesta;
use App\Models\Actividad;
use App\Models\Municipio;
use App\Models\Componente;
use App\Models\ExamenTipo;
use App\Models\AlumnoGrupo;
use App\Models\Conferencia;
use App\Models\Especialidad;
use App\Models\MaterialTipo;
use App\Models\PreguntaTipo;
use App\Models\TareaEnviada;
use App\Models\DatosGenerales;
use App\Models\ExamenPregunta;
use Illuminate\View\Component;
use App\Models\AspiranteStatus;

use App\Models\DatosAcademicos;
use App\Models\DatosFamiliares;
use App\Models\ExamenRespuesta;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use App\Models\MaterialDidactico;
use App\Models\ExamenCalificacion;
use App\Models\EspecialidadPeriodo;
use Database\Factories\GrupoFactory;
use App\Models\AlumnoMaterialDidactico;
use App\Models\EspecialidadPeriodoGrupo;
use App\Models\ExamenCalificacionStatus;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    public function test_user_has_actividad()
    {
      $users = User::factory()->count(3)
      ->has(Actividad::factory(), 'actividades')
      ->create();

      foreach ($users as $user) {
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Actividad::class, $user->actividades[0]);
      }
    }

    public function test_user_has_aviso()
    {
      $users = User::factory()->count(3)
      ->has(Aviso::factory())
      ->create();

      foreach ($users as $user) {
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Aviso::class, $user->avisos[0]);
      }

    }

    public function test_user_has_conferencia()
    {
      $especialidadPeriodo = EspecialidadPeriodo::factory()
      ->for(Periodo::factory())
      ->for(Especialidad::factory())
      ->create();

      $grupo = Grupo::Factory()->create();

      $epg = EspecialidadPeriodoGrupo::factory()
      ->for($especialidadPeriodo, 'EspecialidadPeriodo')
      ->for($grupo)
      ->create();
      $materia = Materia::factory()
        ->for(Componente::factory())
        ->for(
            $especialidadPeriodo,
            'especialidad_periodo'
        )->create();
      $user = User::factory()
      ->create();
      $unidad = Unidad::factory()->for($materia)->create();
      $tema = Tema::factory()->for($unidad)->create();

      Conferencia::factory()
        ->for($user)
        ->for($materia)
        ->for($unidad)
        ->for($tema)
        ->for($epg)
        ->count(3)
        ->create();
      foreach ($user->conferencias as $confe)
        $this->assertInstanceOf(Conferencia::class, $confe);
    }

    public function test_user_has_datos_academicos()
    {
      $user = User::factory()
      ->has(
        DatosAcademicos::factory()
        ->for(AspiranteStatus::factory(), 'Status')
      )
      ->create();

      $this->assertInstanceOf(DatosAcademicos::class, $user->datosAcademicos);
    }

    public function test_user_has_datos_familiares()
    {
      $user = User::factory()
      ->has(DatosFamiliares::factory()->count(1))
      ->create();

      foreach ($user->datosFamiliares as $item) {
        $this->assertInstanceOf(DatosFamiliares::class, $item);
      }
    }

    public function test_user_has_datos_generales()
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

      $this->assertInstanceOf(DatosGenerales::class, $user->datosGenerales);
    }

    public function test_user_has_encuesta()
    {
      $user = User::factory()
      ->has(Encuesta::factory())
      ->create();

      foreach ($user->encuestas as $encuesta)
        $this->assertInstanceOf(Encuesta::class, $encuesta);
    }

    public function test_user_has_encuesta_pregunta()
    {
      $user = User::factory()
      ->has(
        EncuestaPregunta::factory()
        ->for(Encuesta::factory()->for(User::factory()), 'Encuesta')
        ->for(User::factory(), 'User')
        ->for(PreguntaTipo::factory(), 'PreguntasTipo')
      )
      ->create();

      foreach ($user->encuestaPreguntas as $preg)
        $this->assertInstanceOf(EncuestaPregunta::class, $preg);
    }

    public function test_user_has_encuesta_respuesta()
    {
      $user = User::factory()->create();
      $this->assertInstanceOf(User::class, $user);
      $encuesta = Encuesta::factory()->for($user)->create();
      $this->assertInstanceOf(Encuesta::class, $encuesta);
      $encuestaPregunta = EncuestaPregunta::factory()
      ->for($encuesta, 'Encuesta')
      ->for($user, 'User')
      ->for(PreguntaTipo::factory(), 'PreguntasTipo')
      ->create();
      $this->assertInstanceOf(EncuestaPregunta::class, $encuestaPregunta);

      $encuestaRespuesta = EncuestaRespuesta::factory()
      ->for($encuestaPregunta)
      ->for($encuesta)
      ->for($user)
      ->create();
      $this->assertInstanceOf(EncuestaRespuesta::class, $encuestaRespuesta);

      $usercito = User::factory()
      ->has(
        EncuestaRespuesta::factory()
        ->for($encuestaPregunta)
        ->for($encuesta)
        ->for($user)
      )
      ->create();
      $this->assertInstanceOf(User::class, $usercito);

      foreach ($usercito->encuestaRespuestas as $res)
        $this->assertInstanceOf(EncuestaRespuesta::class, $res);
    }

    public function test_user_has_examenes()
    {
      $user = User::factory()
      ->has(
        Examen::factory()
        ->for(User::factory())
        ->for(
          Materia::factory()
          ->for(Componente::factory())
          ->for(
            EspecialidadPeriodo::factory()
            ->for(Periodo::factory())
            ->for(Especialidad::factory()),
            'especialidad_periodo'
          )
        )
        ->for(ExamenTipo::factory()),
        'examenes'
      )
      ->create();

      foreach ($user->examenes as $examen) {
        $this->assertInstanceOf(Examen::class, $examen);
      }
    }

    public function test_user_has_examenes_calificacion()
    {
      $examen = Examen::factory()
      ->for(User::factory())
      ->for(
        Materia::factory()
        ->for(Componente::factory())
        ->for(
          EspecialidadPeriodo::factory()
          ->for(Periodo::factory())
          ->for(Especialidad::factory()),
          'especialidad_periodo'
        )
      )
      ->for(ExamenTipo::factory())
      ->create();
      $this->assertInstanceOf(Examen::class, $examen);

      $user = User::factory()
      ->has(
        ExamenCalificacion::factory()
        ->for(User::factory(), 'usuario')
        ->for($examen)
        ->for(ExamenCalificacionStatus::factory(), 'status'),
        'examenCalificaciones'
      )
      ->create();

      foreach ($user->examenCalificaciones as $calificacion) {
        $this->assertInstanceOf(ExamenCalificacion::class, $calificacion);
      }
    }

    public function test_user_has_examen_pregunta()
    {
      $examen = Examen::factory()
      ->for(User::factory())
      ->for(
        Materia::factory()
        ->for(Componente::factory())
        ->for(
          EspecialidadPeriodo::factory()
          ->for(Periodo::factory())
          ->for(Especialidad::factory()),
          'especialidad_periodo'
        )
      )
      ->for(ExamenTipo::factory())
      ->create();
      $this->assertInstanceOf(Examen::class, $examen);

      $user = User::factory()
      ->has(
        ExamenPregunta::factory()
        ->for($examen)
        ->for(User::factory(), 'user')
        ->for(PreguntaTipo::factory(), 'preguntaTipo')
      )
      ->create();

      foreach ($user->examenPreguntas as $pregunta)
        $this->assertInstanceOf(ExamenPregunta::class, $pregunta);
    }

    public function test_user_has_examen_respuesta()
    {
      $examen = Examen::factory()
      ->for(User::factory())
      ->for(
        Materia::factory()
        ->for(Componente::factory())
        ->for(
          EspecialidadPeriodo::factory()
          ->for(Periodo::factory())
          ->for(Especialidad::factory()),
          'especialidad_periodo'
        )
      )
      ->for(ExamenTipo::factory())
      ->create();
      $this->assertInstanceOf(Examen::class, $examen);

      $pregunta = ExamenPregunta::factory()
      ->for($examen)
      ->for(User::factory(), 'user')
      ->for(PreguntaTipo::factory(), 'preguntaTipo')
      ->create();
      $this->assertInstanceOf(ExamenPregunta::class, $pregunta);

      $user = User::factory()
      ->has(
        ExamenRespuesta::factory()
        ->for($examen)
        ->for($pregunta)
        ->for(User::factory())
      )
      ->create();

      foreach ($user->examenRespuestas as $respuesta)
        $this->assertInstanceOf(ExamenRespuesta::class, $respuesta);
    }

    public function test_user_has_tarea()
    {
      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $user = User::factory()
      ->has(
        Tarea::factory()
        ->forCreador()
        ->for($unidad)
        ->for($materia)
      )
      ->create();
      foreach ($user->tareas as $tarea)
        $this->assertInstanceOf(Tarea::class, $tarea);
    }

    public function test_user_has_tarea_enviada()
    {
      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $tarea = Tarea::factory()
      ->for($unidad)
      ->for($materia)
      ->forCreador();

      $user = User::factory()
      ->has(
        TareaEnviada::factory()
        ->for(User::factory())
        ->for($tarea)
        ->for($materia),
        // 'tareasEnviadas'
      )
      ->create();
      foreach ($user->tareaEnviadas as $te)
        $this->assertInstanceOf(TareaEnviada::class, $te);
    }

    public function test_user_has_tutoria()
    {
      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $material = MaterialDidactico::factory()
          ->for(User::factory(), 'creador')
          ->for($tema)->create();
      $user = User::factory()
      ->has(
          Tutoria::factory()
          ->for(User::factory())
          ->for($tema)
          ->for($materia)
          ->for($material, 'material')
          ->for(Grupo::factory())
          )
          ->create();

      foreach ($user->tutorias as $tuto) {
        $this->assertInstanceOf(Tutoria::class, $tuto);
      }
    }

    public function test_user_has_alumno_grupos_especialidad_periodo_grupo()
    {
        $estado = Estado::factory()->create();
        $municipio = Municipio::factory()->for($estado)->create();

        $alumnos = User::factory()
        ->has(DatosAcademicos::factory()->state(['fecha_baja' => null]))
        ->has(
            DatosGenerales::factory()
            ->for($estado)
            ->for($municipio)
        )
        ->count(2)
        ->create();

        foreach ($alumnos as $alumno) {
            $alumno->assignRole('Alumno');
        }

        $periodo = Periodo::factory()->create();
        $especialidad = Especialidad::factory()->create();

        $especialidadPeriodo = EspecialidadPeriodo::factory()
        ->for($periodo)
        ->for($especialidad)
        ->create();

        $grupo = Grupo::factory();

        $especialidadPeriodoGrupo = EspecialidadPeriodoGrupo::factory()
        ->for($especialidadPeriodo)
        ->for($grupo)
        ->create();

        foreach ($alumnos as $alumno) {
            $alumnoGrupo = AlumnoGrupo::factory()
            ->for($alumno)
            ->for($especialidadPeriodoGrupo)
            ->create();
        }

        $alumnos->each(function($alumno) {
          $this->assertInstanceOf(AlumnoGrupo::class, $alumno->alumnoGrupo);
          $this->assertInstanceOf(AlumnoGrupo::class, $alumno->alumnoGrupoEnCurso);
          $this->assertInstanceOf(EspecialidadPeriodoGrupo::class, $alumno->especialidadPeriodoGrupo->first());
        });
    }

    public function test_user_has_many_AlumnoMaterialDidactico()
    {
      $materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $unidad = Unidad::factory()
      ->for($materia)
      ->create();

      $tema = Tema::factory()
      ->for($unidad)
      ->create();

      $materialDidactico = MaterialDidactico::factory()
        ->for($tema)
        ->forCreador()
        ->for(User::factory(),'Usuario')
        ->for( MaterialTipo::factory() , 'MaterialesTipo' )
        ->create(['nombre' => 'puller materia didáctico test']);

      $usuario = User::factory()->create();
      AlumnoMaterialDidactico::factory()
        ->for($usuario)
        ->for($materialDidactico)
        ->create();

      $this->assertInstanceOf(AlumnoMaterialDidactico::class, $usuario->AlumnoMaterialDidactico->first());
    }

}
