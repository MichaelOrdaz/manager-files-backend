<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_password_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPassword = '12345678';
        $userToUpdate = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $userToUpdate->assignRole('Analyst');

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/admin/users/{$userToUpdate->id}/reset-password", [
            'new_password' => $plainPasswordNew,
            'new_password_confirmation' => $plainPasswordNew,
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->whereType('image', 'string')
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
        $userToUpdate->refresh();
        $this->assertTrue(Hash::check($plainPasswordNew, $userToUpdate->password));
    }

    public function test_user_update_password_error_validation()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPassword = '12345678';
        $userToUpdate = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $userToUpdate->assignRole('Analyst');

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/admin/users/{$userToUpdate->id}/reset-password", [
            'new_password' => $plainPasswordNew,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );

        $userToUpdate->refresh();
        $this->assertFalse(Hash::check($plainPasswordNew, $userToUpdate->password));
    }

    public function test_user_update_password_error_not_found()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $limitId = (User::count() + 1);

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/admin/users/{$limitId}/reset-password", [
            'new_password' => $plainPasswordNew,
            'new_password_confirmation' => $plainPasswordNew,
        ]);

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_user_update_password_error_policy()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/admin/users/{$user->id}/reset-password", [
            'new_password' => $plainPasswordNew,
            'new_password_confirmation' => $plainPasswordNew,
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_user_update_password_error_policy_another_user()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPassword = '12345678';
        $userToUpdate = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $userToUpdate->assignRole('Analyst');

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/admin/users/{$userToUpdate->id}/reset-password", [
            'new_password' => $plainPasswordNew,
            'new_password_confirmation' => $plainPasswordNew,
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
