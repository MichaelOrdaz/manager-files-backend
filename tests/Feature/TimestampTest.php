<?php

namespace Tests\Feature;

use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\Examen;
use App\Models\ExamenTipo;
use App\Models\MarcaDeTiempo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TimestampTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_starttimestamp()
    {
        $user = User::factory()->create();
        $user->assignRole('Aspirante a ingreso');
        $this->actingAs($user, 'api');

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
        $examen = Examen::factory()
        ->for(User::factory())
        ->for($materia)
        ->for($examenTipo)
        ->create();
        $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/timestamp:start");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'timestamp'
            ],
            'success',
        ]);
    }

    public function test_endtimestamp_no_found()
    {
        $user = User::factory()->create();
        $user->assignRole('Aspirante a ingreso');
        $this->actingAs($user, 'api');

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
        $examen = Examen::factory()
        ->for(User::factory())
        ->for($materia)
        ->for($examenTipo)
        ->create();
        $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/timestamp:end");
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'errors',
            'success',
        ]);
    }

    public function test_endtimestamp_ok()
    {
        $user = User::factory()->create();
        $user->assignRole('Aspirante a ingreso');
        $this->actingAs($user, 'api');

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
        $examen = Examen::factory()
        ->for(User::factory())
        ->for($materia)
        ->for($examenTipo)
        ->create();

        $time = MarcaDeTiempo::factory()
        ->for($user)
        ->for($examen)
        ->create();

        $response = $this->post("api/v1/usuarios/{$user->id}/examenes/{$examen->id}/timestamp:end");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'success',
        ]);
    }
}
