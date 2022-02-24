<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EndpointBajasTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_permiso_baja_existe ()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $can = $user->can('bajaTipo.show');
        $this->assertTrue($can);

        $user = User::factory()->create();
        $user->assignRole('Departamento de docentes');
        $can = $user->can('bajaTipo.show');
        $this->assertTrue($can);

        $user = User::factory()->create();
        $user->assignRole('Control Escolar');
        $can = $user->can('bajaTipo.show');
        $this->assertTrue($can);

        $user = User::factory()->create();
        $user->assignRole('Alumno');
        $can = $user->can('bajaTipo.show');
        $this->assertFalse($can);
    }

    public function test_request_baja_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $this->actingAs($user, 'api');

        $this->assertTrue($user->can('bajaTipo.show'));

        $response = $this->getJson('api/v1/bajas-tipo');

        $response->assertOk();

    }

    public function test_request_control()
    {
        $user = User::factory()->create();
        $user->assignRole('Control Escolar');
        $this->actingAs($user, 'api');

        $response = $this->getJson('api/v1/bajas-tipo');

        $response->assertOk();

    }

    public function test_request_departamento()
    {
        $user = User::factory()->create();
        $user->assignRole('Departamento de docentes');
        $this->actingAs($user, 'api');

        $response = $this->getJson('api/v1/bajas-tipo');

        $response->assertOk();

    }

    public function test_request_alumno()
    {
        $user = User::factory()->create();
        $user->assignRole('Alumno');
        $this->actingAs($user, 'api');

        $response = $this->getJson('api/v1/bajas-tipo');

        $response->assertStatus(403);

    }
}
