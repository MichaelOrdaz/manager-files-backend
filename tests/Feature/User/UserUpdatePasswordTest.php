<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserUpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_password_success_with_role_admin()
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

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}/change-password", [
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
            'new_password' => $plainPasswordNew,
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

    public function test_user_update_your_own_password_user_success_role_admin()
    {
        $this->seed();

        $plainPassword = '12345678';
        $user = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/users/{$user->id}/change-password", [
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
            'new_password' => $plainPasswordNew,
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
        $user->refresh();
        $this->assertTrue(Hash::check($plainPasswordNew, $user->password));
    }

    public function test_user_update_your_own_password_user_success_role_head()
    {
        $this->seed();

        $plainPassword = '12345678';
        $user = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $user->assignRole('head of department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/users/{$user->id}/change-password", [
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
            'new_password' => $plainPasswordNew,
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
        $user->refresh();
        $this->assertTrue(Hash::check($plainPasswordNew, $user->password));
    }

    public function test_user_update_your_own_password_user_success_role_analyst()
    {
        $this->seed();

        $plainPassword = '12345678';
        $user = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $user->assignRole('analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/users/{$user->id}/change-password", [
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
            'new_password' => $plainPasswordNew,
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
        $user->refresh();
        $this->assertTrue(Hash::check($plainPasswordNew, $user->password));
    }

    public function test_user_update_another_user_password_role_head_error_policy()
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

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}/change-password", [
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
            'new_password' => $plainPasswordNew,
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );

        $user->refresh();
        $this->assertFalse(Hash::check($plainPasswordNew, $user->password));
    }

    public function test_user_update_another_user_password_role_head_error_validation()
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

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}/change-password", [
            'password' => $plainPassword,
            'password_confirmation' => '457',
            'new_password' => '1234',
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );

        $user->refresh();
        $this->assertFalse(Hash::check($plainPasswordNew, $user->password));
    }

    public function test_user_update_own_password_role_head_error_password()
    {
        $this->seed();

        $plainPassword = '12345678';
        $user = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $user->assignRole('head of department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $plainPasswordNew = '987654321';

        $response = $this->postJson("api/v1/users/{$user->id}/change-password", [
            'password' => '123456789',
            'password_confirmation' => '123456789',
            'new_password' => '1234567890',
        ]);
        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );

        $user->refresh();
        $this->assertFalse(Hash::check($plainPasswordNew, $user->password));
    }
}
