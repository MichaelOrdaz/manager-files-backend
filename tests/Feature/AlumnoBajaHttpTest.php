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

class AlumnoBajaHttpTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_baja_alumno_ok()
    {
        $this->handleValidationExceptions();

        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

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
        }

        $baja = BajasTipo::find(2);

        $response = $this->postJson("api/v1/usuarios/{$alumnos->first()->id}/:baja", [
            'baja_id' => $baja->id,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'email',
                'fecha_baja',
                'baja',
            ],
            'message',
            'success',
        ]);
    }

    public function test_baja_alumno_sin_grupo_ok()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

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

        $baja = BajasTipo::find(2);

        $response = $this->postJson("api/v1/usuarios/{$alumnos->first()->id}/:baja", [
            'baja_id' => $baja->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'email',
                'fecha_baja',
                'baja',
            ],
            'message',
            'success',
        ]);
    }

    public function test_baja_alumno_sin_baja()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $alumno = User::factory()
        ->create();

        $response = $this->postJson('api/v1/usuarios/' . $alumno->id . '/:baja', []);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors', 'success',
        ]);
    }

    public function test_baja_alumno_not_found()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $fakeId = 100000;
        $baja = BajasTipo::find(2);
        $response = $this->postJson('api/v1/usuarios/' . $fakeId . '/:baja', [
            'baja_id' => $baja->id,
        ]);

        $response->assertStatus(404);

        $response->assertJsonStructure([
            'errors', 'success',
        ]);
    }

    public function test_baja_alumno_role_not()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $estado = Estado::factory()->create();
        $municipio = Municipio::factory()->for($estado)->create();

        $docente = User::factory()
        ->has(DatosAcademicos::factory()->state(['fecha_baja' => null]))
        ->has(
            DatosGenerales::factory()
            ->for($estado)
            ->for($municipio)
        )
        ->create();

        $docente->assignRole('Docente');

        $baja = BajasTipo::find(2);

        $response = $this->postJson('api/v1/usuarios/' . $docente->id . '/:baja', [
            'baja_id' => $baja->id,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'email',
                'fecha_baja',
                'baja',
            ],
            'message',
            'success'
        ]);
    }

    public function test_baja_alumno_con_baja()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $baja = BajasTipo::find(2);

        $alumno = User::factory()
        ->has(
            DatosAcademicos::factory()
            ->for($baja)
        )
        ->create();

        $this->assertDatabaseHas('users', [
            'email' => $alumno->email,
        ]);

        $this->assertEquals(2, $alumno->datosAcademicos->baja_id, 'la baja no es igual');

        $alumno->assignRole('Deshabilitado');

        $response = $this->postJson('api/v1/usuarios/' . $alumno->id . '/:baja', [
            'baja_id' => $baja->id,
        ]);

        $response->assertStatus(404);

        $response->assertJsonStructure([
            'errors', 'success',
        ]);
    }

    public function test_baja_alumno_login_con_baja()
    {
        $baja = BajasTipo::find(2);

        $alumno = User::factory()
        ->password1_5()
        ->has(
            DatosAcademicos::factory()
            ->for($baja)
        )
        ->create();

        $this->assertDatabaseHas('users', [
            'email' => $alumno->email,
        ]);

        $this->assertEquals(2, $alumno->datosAcademicos->baja_id, 'la baja no es igual');

        $alumno->assignRole('Deshabilitado');

        $response = $this->postJson('api/v1/auth/login', [
            'email' => $alumno->email,
            'password' => '12345',
        ]);

        $response->assertStatus(403);

    }

}
