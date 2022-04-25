<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_delete_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('analyst');

        $response = $this->deleteJson("api/v1/users/{$userToDelete->id}");

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );

        $this->assertSoftDeleted($userToDelete);
    }

    public function test_user_delete_error_policy()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('head of department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('analyst');

        $response = $this->deleteJson("api/v1/users/{$userToDelete->id}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );

    }

    public function test_user_delete_error_policy_2()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('analyst');

        $response = $this->deleteJson("api/v1/users/{$userToDelete->id}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_delete_error_not_found()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $limitId = (User::count() + 1);

        $response = $this->deleteJson("api/v1/users/{$limitId}");

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_user_delete_error_not_found_user_already_soft()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $userToDelete = User::factory()->create();
        $userToDelete->assignRole('analyst');
        $userToDelete->delete();
        $this->assertSoftDeleted($userToDelete);

        $response = $this->deleteJson("api/v1/users/{$userToDelete->id}");

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
