<?php

namespace Tests\Feature\User;

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;
    
    protected $seeder = RolesSeeder::class;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_users_list()
    {
        $this->handleValidationExceptions();
        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->create();
        $user = $users->first();
        $user->assignRole('Administrador');

        Passport::actingAs($user);

        $response = $this->getJson('api/v1/users');

        $this->assertAuthenticated();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 6, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('imagen')
                ->has('nombre')
                ->has('paterno')
                ->has('materno')
                ->has('role')
                ->has('departamento')
                ->has('celular')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_users_list_search()
    {
        $this->handleValidationExceptions();
        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->create();
        $user = $users->first();
        $user->assignRole('Administrador');

        Passport::actingAs($user);

        $response = $this->getJson('api/v1/users');

        $this->assertAuthenticated();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 6, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('imagen')
                ->has('nombre')
                ->has('paterno')
                ->has('materno')
                ->has('role')
                ->has('departamento')
                ->has('celular')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }
}
