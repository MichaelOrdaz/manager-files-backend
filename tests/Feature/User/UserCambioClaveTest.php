<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserCambioClaveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_cambio_exitoso()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');

      $passwordNuevo = '12345678';
      $response = $this->post("api/v1/usuarios/cambio-clave",[
        'clave_actual' => '12345',
        'clave_nueva' => $passwordNuevo,
        'clave_nueva_repetida' => $passwordNuevo
      ]);
      $response->assertStatus(200)
      ->assertJsonStructure([
        'message',
        'success',
      ]);

      $check = Hash::check($passwordNuevo, $user->password);
      $this->assertTrue($check);
    }

    public function test_user_cambio_clave_nueva_igual()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');
      
      $response = $this->post("api/v1/usuarios/cambio-clave",[
        'clave_actual' => '12345678',
        'clave_nueva' => '12345678',
        'clave_nueva_repetida' => '12345678'
      ]);

      $response->assertStatus(422)
      ->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_user_cambio_clave_diferente()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');
      
      $response = $this->post("api/v1/usuarios/cambio-clave",[
        'clave_actual' => '12345678',
        'clave_nueva' => '123456789',
        'clave_nueva_repetida' => '1234567'
      ]);
      $response->assertStatus(422)
      ->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_user_cambio_clave_erronea()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');
      
      $response = $this->post("api/v1/usuarios/cambio-clave",[
        'clave_actual' => 'inventada',
        'clave_nueva' => '123456789',
        'clave_nueva_repetida' => '123456789'
      ]);

      $response->assertStatus(403)
      ->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_user_cambio_clave_igual_actual()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');
      
      $response = $this->post("api/v1/usuarios/cambio-clave",[
        'clave_actual' => '12345',
        'clave_nueva' => '12345',
        'clave_nueva_repetida' => '12345'
      ]);

      $response->assertStatus(422)
      ->assertJsonStructure([
        'errors',
        'success',
      ]);
    }

    public function test_cambio_pass_before_login()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');

      $user->password = Hash::make('test123');
      $user->save();

      $response = $this->postJson('api/v1/auth/login', [
          'email' => $user->email,
          'password' => 'test123'
      ]);

      $response->assertStatus(200);

      $response->assertJsonStructure([
          'message',
          'success',
      ]);

      $this->assertTrue($response['success']);
    }
}