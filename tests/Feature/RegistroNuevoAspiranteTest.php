<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RegistroNuevoAspiranteTest extends TestCase
{
    use DatabaseTransactions;

    protected $faker;

    public function test_registrar_nuevo_aspirante_correo_existente()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/v1/aspirantes', [
            "email" => $user->email,
            "role" => ["Aspirante a ingreso"],
            "password" => "12345",
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors',
            'success',
        ]);
    }

    public function test_registrar_correctamente()
    {
        $this->withoutExceptionHandling();
        $email = $this->faker->email();
        $response = $this->postJson('api/v1/aspirantes', [
            "email" => $email,
            "role" => ["Aspirante a ingreso"],
            "password" => "12345",
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data',
            'success',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }
}
