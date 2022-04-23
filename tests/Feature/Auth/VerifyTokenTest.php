<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class VerifyTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_auth()
    {
        $response = $this->getJson('api/v1/verify-auth');
        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('isAuth', false)
        );
    }

    public function test_valid_auth()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();
        
        $response = $this->getJson('api/v1/verify-auth');
        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('isAuth', true)
        );
    }
}