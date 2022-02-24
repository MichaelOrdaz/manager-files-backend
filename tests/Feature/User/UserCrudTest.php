<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserCrudTest extends TestCase
{
    use DatabaseTransactions;

    private $user;

    public function test_user_crud_create()
    {
      $response = $this->post("api/v1/usuarios",[
        'email' => 'loremipsumdolor@laravelpulller.net',
        'password' => '12345',
        'remember_token' => 'yes',
        'firebase_uid' => '9302bd8e-f115-55d4-99b4-84d71ee50303',
        'role' => ['Admin'],
        'activo' => '1',
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['email' => 'loremipsumdolor@laravelpulller.net'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'email',
          'firebase_uid',
        ]
      ]);
    }

    public function test_user_crud_list()
    {
      $response = $this->get("api/v1/usuarios");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' =>[
          [
            'id',
            'email',
            'firebase_uid',
            'datos_generales',
            'datos_familiares',
            'datos_academicos',
          ]
        ],
        "success",
      ]);
    }

    public function test_user_crud_get()
    {
      $response = $this->get("api/v1/usuarios/{$this->user->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'email',
          'firebase_uid',
          'datos_generales',
          'datos_familiares',
          'datos_academicos',
        ],
        "success",
      ]);
    }

    public function test_user_crud_update()
    {

      $user = User::factory()->create();

      $response = $this->post("api/v1/usuarios/{$user->id}",[
        'email' => $user->email,
        'password' => '12345',
        'remember_token' => 'no',
        'firebase_uid' => '616b4bed-cb44-5321-8046-16c87d1ef87d',
        'role' => ['Admin'],
        'activo' => '1',
      ]);

      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'email',
          'firebase_uid',
          'datos_generales',
          'datos_familiares',
          'datos_academicos',
        ],
        "success",
      ]);

      $user = $user->refresh();
      $this->assertEquals($user->firebase_uid, "616b4bed-cb44-5321-8046-16c87d1ef87d");
    }

    public function test_user_crud_delete()
    {
      $response = $this->delete("api/v1/usuarios/{$this->user->id}");
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'email',
          'firebase_uid',
          'datos_generales',
          'datos_familiares',
          'datos_academicos',
        ],
        "success",
      ]);

      $userDelete = User::find($this->user->id);
      $this->assertEquals($userDelete, null);
    }

    public function test_user_create_padre()
    {
      $user = User::factory()->create();
      $user->assignRole('Alumno');

      $response = $this->post("api/v1/usuarios",[
        'email' => 'padre_de_familia@net.com',
        'remember_token' => 'yes',
        'firebase_uid' => 'dsfa465asd49a8sd798asd7f98as',
        'role' => ['Padre de familia'],
        'activo' => '1',
        'tutorado_id' => $user->id
      ]);

      $response->assertStatus(201)
      ->assertJsonFragment(['email' => 'padre_de_familia@net.com'])
      ->assertJsonStructure([
        'data' => [
          'id',
          'email',
          'firebase_uid',
        ]
      ]);

      $user->refresh();

      $this->assertInstanceOf(User::class,$user->tutor);
      $tutor = $user->tutor;
      foreach ($tutor->tutorados as $tutorado) {
        $this->assertInstanceOf(User::class,$tutorado);

      }
    }

    public function test_user_update_padre()
    {
      $tutor = User::factory()->create();
      $tutor->assignRole('Padre de familia');
      
      $alumno = User::factory()
      ->for($tutor, 'tutor')
      ->create();
      $alumno->assignRole('Alumno');
      
      $response = $this->post("api/v1/usuarios/{$tutor->id}",[
        'email' => $tutor->email,
        'remember_token' => 'yes',
        'firebase_uid' => 'dsfa465asd49a8sd798asd7f98as',
        'role' => ['Padre de familia'],
        'activo' => '1',
      ]);

      $response->assertStatus(200)
      ->assertJsonFragment(['email' => $tutor->email])
      ->assertJsonStructure([
        'data' => [
          'id',
          'email',
          'firebase_uid',
        ]
      ]);
    }

    public function setUp(): void{
      parent::setUp();
      // Usuario que se usara para las peticiones
      $this->user = User::factory()->create();
      // Asignación de rol con todos los permisos crud
      $this->user->assignRole('Admin');
      //Se esa este usuario como el usuario autenticado en cada petición
      $this->actingAs($this->user, 'api');
    }
}