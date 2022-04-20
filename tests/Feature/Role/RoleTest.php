<?php

namespace Tests\Feature\Role;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_role_list_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/roles');

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_role_list_success_analyst()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/roles');

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_role_list_success_head()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('head of department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/roles');

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_role_show_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $role = Role::all()->random();

        $response = $this->getJson("api/v1/roles/{$role->id}");

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data',  fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_role_show_error()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $roleId = Role::count();
        $roleId++;

        $response = $this->getJson("api/v1/roles/{$roleId}");

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
