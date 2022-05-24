<?php

namespace Tests\Feature\AnalystPermission;

use App\Helpers\Dixa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AnalystPermissionListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_permission_to_users_admin()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/analyst-permissions");

        $total = Permission::whereIn('name', Dixa::ANALYST_PERMISSIONS)->count();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $total, fn ($json) => 
                $json->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_list_permission_to_users_head()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/analyst-permissions");

        $total = Permission::whereIn('name', Dixa::ANALYST_PERMISSIONS)->count();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $total, fn ($json) => 
                $json->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_list_permission_to_users_analyst_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');
        $user->syncPermissions([Dixa::ANALYST_WRITE_PERMISSION]);

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/analyst-permissions");

        $total = Permission::whereIn('name', Dixa::ANALYST_PERMISSIONS)->count();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $total, fn ($json) => 
                $json->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_list_permission_to_users_analyst_error()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');
        $user->syncPermissions([Dixa::ANALYST_READ_PERMISSION]);

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/analyst-permissions");

        $total = Permission::whereIn('name', Dixa::ANALYST_PERMISSIONS)->count();
        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_list_permission_to_users_analyst_error_2()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/analyst-permissions");

        $total = Permission::whereIn('name', Dixa::ANALYST_PERMISSIONS)->count();
        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }
}
