<?php

namespace Tests\Feature\AnalystPermission;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AnalystPermissionCreateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update_permission_user_analyst()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($department)
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $analyst = $users->random();

        $response = $this->postJson("api/v1/users/{$analyst->id}/permissions", [
            'permission' => Dixa::ANALYST_READ_PERMISSION
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('fullName')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->where('authorization.0', Dixa::ANALYST_READ_PERMISSION)
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_update_permission_user_analyst_change()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($department)
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $analyst = $users->random();
        $analyst->givePermissionTo(Dixa::ANALYST_READ_PERMISSION);

        $this->assertTrue($analyst->hasPermissionTo(Dixa::ANALYST_READ_PERMISSION));

        $response = $this->postJson("api/v1/users/{$analyst->id}/permissions", [
            'permission' => Dixa::ANALYST_WRITE_PERMISSION
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('fullName')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->where('authorization.0', Dixa::ANALYST_WRITE_PERMISSION)
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_update_permission_user_analyst_revoke_permission()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($department)
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $analyst = $users->random();
        $analyst->givePermissionTo(Dixa::ANALYST_READ_PERMISSION);

        $this->assertTrue($analyst->hasPermissionTo(Dixa::ANALYST_READ_PERMISSION));

        $response = $this->postJson("api/v1/users/{$analyst->id}/permissions", []);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('fullName')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->has('authorization', 0)
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_update_permission_user_analyst_head_of_another_department()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($departments->where('name', '!=', $department->name)->random())
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $analyst = $users->random();

        $response = $this->postJson("api/v1/users/{$analyst->id}/permissions", [
            'permission' => Dixa::ANALYST_READ_PERMISSION
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_update_permission_user_analyst_revoke()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($department)
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $analyst = $users->random();

        $response = $this->postJson("api/v1/users/{$analyst->id}/permissions", [
            'permission' => ''
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('fullName')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->whereType('authorization', 'array')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_update_permission_user_analyst_error_not_found()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($department)
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $analyst = $users->random();

        $response = $this->postJson("api/v1/users/{$analyst->id}/permissions", [
            'permission' => 'lorem'
        ]);

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_update_permission_user_many_analysts()
    {
        $this->seed();

        $department = Department::all()->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $users = User::factory()
        ->count(5)
        ->for($department)
        ->create();

        $permissions = collect(Dixa::ANALYST_PERMISSIONS);

        $payload = $users->map(function ($user) use ($permissions) {
            $user->assignRole('Analyst');
            return [
                'id' => $user->id,
                'permission' => $permissions->random()
            ];
        });

        $response = $this->postJson("api/v1/users/*/permissions", [
            'users' => $payload
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 5, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('fullName')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->whereType('authorization', 'array')
            )
            ->has('message')
            ->where('success', true)
        );
    }
}
