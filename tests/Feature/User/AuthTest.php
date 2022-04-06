<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_login()
    {
      $user = User::factory()->password1_5()->create();

      $response = $this->postJson("api/v1/login",[
        'email' => $user->email,
        'password' => '12345',
      ]);

      $response->assertStatus(200);
    }

    public function test_user_login_error()
    {
      $response = $this->postJson("api/v1/login",[
        'email' => '',
        'password' => '123456',
      ]);

      $response->assertStatus(422)
      ->assertJson(fn (AssertableJson $json) => 
        $json->where('success', false)
        ->has('errors')
        ->etc()
      );
      
    }
}