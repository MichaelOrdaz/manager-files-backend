<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login()
    {
      $this->artisan('passport:install');
      $user = User::factory()->password1_5()->create();

      $response = $this->postJson("api/v1/login",[
        'email' => $user->email,
        'password' => '12345',
      ]);
      $response->assertStatus(200)
      ->assertJson(fn (AssertableJson $json) => 
        $json->has('data', fn ($json) => 
          $json->has('user', fn ($json) => 
            $json->whereType('id', 'integer')
            ->whereType('nombre', 'string')
            ->where('email', $user->email)
            ->etc()
          )
          ->has('token')
        )
        ->has('message')
        ->has('success')
      );
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

    public function test_logout_user()
    {
      $this->seed(RolesSeeder::class);
      
      $user = User::factory()->create();
      $user->assignRole('Administrador');
      
      Passport::actingAs($user);

      $response = $this->postJson('api/v1/logout');
      
      $response->assertOk()
      ->assertJson(fn (AssertableJson $json) => 
        $json->has('data')
        ->has('message')
        ->where('success', true)
      );
    }

    public function test_account_admin()
    {
      $this->seed(RolesSeeder::class);
      
      $user = User::factory()->create();
      $user->assignRole('Administrador');
      
      Passport::actingAs($user);

      $response = $this->getJson('api/v1/account');
      
      $response->assertOk()
      ->assertJson(fn (AssertableJson $json) => 
        $json->has('data', fn ($json) => 
          $json->has('user', fn ($json) => 
            $json->whereType('id', 'integer')
            ->whereType('nombre', 'string')
            ->etc()
          )
          ->has('permissions')
          ->has('views')
          ->whereType('roles', 'array')
        )
        ->has('message')
        ->has('success')
      );
    }

    public function test_account_analista()
    {
      $this->seed(RolesSeeder::class);
      
      $user = User::factory()->create();
      $user->assignRole('Analista');
      
      Passport::actingAs($user);

      $response = $this->getJson('api/v1/account');
      
      $response->assertOk()
      ->assertJson(fn (AssertableJson $json) => 
        $json->has('data', fn ($json) => 
          $json->has('user', fn ($json) => 
            $json->whereType('id', 'integer')
            ->whereType('nombre', 'string')
            ->etc()
          )
          ->has('permissions')
          ->has('views')
          ->whereType('roles', 'array')
        )
        ->has('message')
        ->has('success')
      );
    }

    public function test_account_jefe()
    {
      $this->seed(RolesSeeder::class);
      
      $user = User::factory()->create();
      $user->assignRole('Jefe de departamento');
      
      Passport::actingAs($user);

      $response = $this->getJson('api/v1/account');
      
      $response->assertOk()
      ->assertJson(fn (AssertableJson $json) => 
        $json->has('data', fn ($json) => 
          $json->has('user', fn ($json) => 
            $json->whereType('id', 'integer')
            ->whereType('nombre', 'string')
            ->etc()
          )
          ->has('permissions')
          ->has('views')
          ->whereType('roles', 'array')
        )
        ->has('message')
        ->has('success')
      );
    }
}