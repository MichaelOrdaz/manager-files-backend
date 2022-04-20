<?php

namespace Tests\Feature\Department;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_department_list_success_admin()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/departments');

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 4, fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_department_list_success_analyst()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/departments');

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 4, fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_department_list_success_head()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('head of department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/departments');

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 4, fn ($json) => 
                $json->has('id')
                ->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_department_show_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $department = Department::all()->random();

        $response = $this->getJson("api/v1/departments/{$department->id}");
        
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

    public function test_deparment_show_error()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $departmentId = Department::count();
        $departmentId++;

        $response = $this->getJson("api/v1/departments/{$departmentId}");

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
