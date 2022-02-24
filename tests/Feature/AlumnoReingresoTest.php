<?php

namespace Tests\Feature;

use App\Models\AlumnoGrupo;
use App\Models\BajasTipo;
use App\Models\Componente;
use App\Models\DatosAcademicos;
use App\Models\DatosGenerales;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\EspecialidadPeriodoGrupo;
use App\Models\Estado;
use App\Models\Examen;
use App\Models\ExamenPregunta;
use App\Models\ExamenRespuesta;
use App\Models\ExamenTipo;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Municipio;
use App\Models\Periodo;
use App\Models\PreguntaTipo;
use App\Models\Tarea;
use App\Models\TareaEnviada;
use App\Models\TareaGrupo;
use App\Models\TareaTema;
use App\Models\Tema;
use App\Models\Unidad;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlumnoReingresoTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_reingresar_alumno_con_baja_temporal()
    {
        $dd = User::factory()->create();
        $dd->assignRole('Departamento de docentes');
        $this->actingAs($dd, 'api');

        $bajaTmp = BajasTipo::where('nombre', 'Baja temporal')->first();

        $estado = Estado::factory()->create();
        $municipio = Municipio::factory()->for($estado)->create();

        $alumnos = User::factory()
        ->password1_5()
        ->has(
            DatosAcademicos::factory()
            ->for($bajaTmp)
        )
        ->has(
            DatosGenerales::factory()
            ->for($estado)
            ->for($municipio)
        )
        ->count(2)
        ->create();

        foreach ($alumnos as $alumno) {
            $alumno->assignRole('Alumno');
            $alumno->assignRole('Deshabilitado');
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
                ->for($dd, 'creador')
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
                ->for($dd, 'user')
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
        }

        foreach ($alumnos as $alumno) {
            
            $this->assertDatabaseHas('users', [
                'email' => $alumno->email,
            ]);
        }
        
        $response = $this->postJson("api/v1/usuarios/{$alumnos->first()->id}/:reingreso", []);
        
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'nombre',
                'apellido_materno',
                'apellido_paterno',
                'email',
            ],
            'message',
            'success',
        ]);

        $alumno = $alumnos->first();
        $alumno->refresh();
        $this->assertNull($alumno->datosAcademicos->BajasTipo);
        $this->assertTrue($alumno->hasRole('Alumno'));
        $this->assertFalse($alumno->hasRole('Deshabilitado'));
    }
}
