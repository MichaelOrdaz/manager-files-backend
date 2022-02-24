<?php

namespace Tests\Feature\EspecialidadPeriodoGrupo;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Model;
use App\Models\Tarea;
use App\Models\Estado;
use App\Models\Examen;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;

use App\Models\Municipio;
use App\Models\TareaTema;
use App\Models\Componente;
use App\Models\ExamenTipo;
use App\Models\TareaGrupo;
use App\Models\AlumnoGrupo;
use App\Models\ExamenGrupo;
use App\Models\Especialidad;
use App\Models\PreguntaTipo;
use App\Models\TareaEnviada;
use App\Models\DatosGenerales;
use App\Models\DocenteMateria;
use App\Models\ExamenPregunta;
use App\Models\DatosAcademicos;
use App\Models\ExamenRespuesta;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EspecialidadPeriodoGrupoRelacionesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_especialidadperiodogrupo_has_relaciones()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

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

        $componente = Componente::factory()->create();

        $materias = Materia::factory()
        ->for($componente)
        ->for($especialidadPeriodo, 'especialidad_periodo')
        ->count(5)//aqui se añaden mas registros para materia
        ->create();

        foreach ($materias as $materia) {

            $unidades = Unidad::factory()
            ->for($materia)
            ->count(5)//aqui se anaden mas unidades
            ->create();

            foreach ($unidades as $unidad) {
                $temas = Tema::factory()
                ->for($unidad)
                ->count(5)//deben añadirse igual numero de temas y tareas, solo para este seeder
                ->create();

                $tareas = Tarea::factory()
                ->for($admin, 'creador')
                ->for(User::factory(), 'usuario')
                ->for($unidad)
                ->for($materia)
                ->count(5)//deben añadirse igual numero de temas y tareas, solo para este seeder
                ->create();

                foreach ($tareas as $idx => $tarea) {
                    TareaTema::factory()
                    ->for($tarea)
                    ->for($temas[$idx])
                    ->create();

                    TareaGrupo::factory()
                    ->for($especialidadPeriodoGrupo)
                    ->for($tarea)
                    ->create();

                    foreach ($alumnos as $alumno) {
                        TareaEnviada::factory()
                        ->for($alumno)
                        ->for($materia)
                        ->for($tarea)
                        ->count(2)
                        ->create();
                    }

                }

            }

            $examenes = Examen::factory()
            ->for(User::factory())
            ->for($materia)
            ->for(ExamenTipo::factory())
            ->count(2)
            ->create();

            foreach ($examenes as $examen) {

                $preguntas = ExamenPregunta::factory()
                ->for($examen)
                ->for($admin, 'user')
                ->for(PreguntaTipo::factory(), 'preguntaTipo')
                ->count(3)
                ->create();

                foreach ($preguntas as $pregunta) {

                    foreach ($alumnos as $alumno) {
                        $respuesta = ExamenRespuesta::factory()
                        ->for($examen)
                        ->for($pregunta)
                        ->for($alumno)
                        ->create();
                    }
                }

            }

            DocenteMateria::factory()
            ->for(User::factory())
            ->for($especialidadPeriodoGrupo, 'especialidad_periodo_grupo')
            ->for($materia)
            ->create();
        }

        ExamenGrupo::factory()
        ->for($especialidadPeriodoGrupo->first())
        ->for(Examen::factory()->for(User::factory())->for(ExamenTipo::factory()))
        ->create();

        $this->assertInstanceOf(EspecialidadPeriodo::class, $especialidadPeriodoGrupo->EspecialidadPeriodo);
        $this->assertInstanceOf(Grupo::class, $especialidadPeriodoGrupo->grupo);
        $this->assertInstanceOf(DocenteMateria::class, $especialidadPeriodoGrupo->DocentesMaterias->first());
        $this->assertInstanceOf(TareaGrupo::class, $especialidadPeriodoGrupo->tareasGrupo->first());
        $this->assertInstanceOf(Tarea::class, $especialidadPeriodoGrupo->tareas->first());
        $this->assertInstanceOf(AlumnoGrupo::class, $especialidadPeriodoGrupo->alumnosGrupos->first());
        $this->assertInstanceOf(User::class, $especialidadPeriodoGrupo->users->first());
        $this->assertInstanceOf(ExamenGrupo::class, $especialidadPeriodoGrupo->first()->ExamenesGrupo[0]);

    }
}