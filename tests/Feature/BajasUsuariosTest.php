<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

class BajasUsuariosTest extends TestCase
{
    use DatabaseTransactions;

    public function test_baja_usuario_ok()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $estado = Estado::factory()->create();
        $municipio = Municipio::factory()->for($estado)->create();

        $user = User::factory()
        ->has(DatosAcademicos::factory()->state(['fecha_baja' => null]))
        ->has(
            DatosGenerales::factory()
            ->for($estado)
            ->for($municipio)
        )
        ->create();

        $user->assignRole('Docente');

        $baja = BajasTipo::where('nombre', 'Baja definitiva')->first();

        $response = $this->postJson("api/v1/usuarios/{$user->id}/:baja", [
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

        $user->refresh();
        $this->assertInstanceOf(BajasTipo::class, $user->datosAcademicos->BajasTipo);
        $this->assertTrue($user->hasRole('Deshabilitado'));
    }

    public function test_baja_usuario_sin_baja()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $user = User::factory()
        ->create();

        $response = $this->postJson("api/v1/usuarios/{$user->id}/:baja", []);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors', 'success',
        ]);
    }

    public function test_baja_usuario_not_found()
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

    public function test_baja_usuario_role_not()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');
        $this->actingAs($admin, 'api');

        $estado = Estado::factory()->create();
        $municipio = Municipio::factory()->for($estado)->create();

        $alumno = User::factory()
        ->has(DatosAcademicos::factory()->state(['fecha_baja' => null]))
        ->has(
            DatosGenerales::factory()
            ->for($estado)
            ->for($municipio)
        )
        ->create();

        $alumno->assignRole('Alumno');

        $baja = BajasTipo::find(2);

        $response = $this->postJson('api/v1/usuarios/' . $alumno->id . '/:baja', [
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

    public function test_baja_usuario_login_con_baja()
    {
        $baja = BajasTipo::find(2);

        $user = User::factory()
        ->password1_5()
        ->has(
            DatosAcademicos::factory()
            ->for($baja)
        )
        ->create();
        $user->assignRole(['Docente', 'Deshabilitado']);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);

        $this->assertEquals(2, $user->datosAcademicos->baja_id, 'la baja no es igual');

        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => '12345',
        ]);

        $response->assertStatus(403);

    }

    public function test_reingresar_usuario_con_baja_temporal()
    {
        $dd = User::factory()->create();
        $dd->assignRole('Departamento de docentes');
        $this->actingAs($dd, 'api');

        $bajaTmp = BajasTipo::where('nombre', 'Baja temporal')->first();

        $estado = Estado::factory()->create();
        $municipio = Municipio::factory()->for($estado)->create();

        $user = User::factory()
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
        ->create();

        $user->assignRole('Docente');
        $user->assignRole('Deshabilitado');
        
        $response = $this->postJson("api/v1/usuarios/{$user->id}/:reingreso", []);
        
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'nombre',
                'apellido_materno',
                'apellido_paterno',
                'email',
            ],
            'message',
            'success',
        ]);

        $user->refresh();
        $this->assertNull($user->datosAcademicos->BajasTipo);
        $this->assertFalse($user->hasRole('Deshabilitado'));
        $this->assertTrue($user->hasRole('Docente'));
    }
}
