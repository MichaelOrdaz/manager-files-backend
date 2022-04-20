<?php

namespace Tests\Feature\User;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_create_success()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $role = Role::findByName('Analyst');
        $departamento = Department::all()->random();

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'celular' => '2221245678',
            'password' => '12345678',
            'imagen' => '',// optional
            'role_id' => $role->id,
            'departamento_id' => $departamento->id, // optional
        ]);

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('imagen')
                ->has('nombre')
                ->has('paterno')
                ->has('materno')
                ->has('role')
                ->has('departamento')
                ->has('celular')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_create_success_imagen()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $role = Role::findByName('Analyst');
        $departamento = Department::all()->random();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'celular' => '2221245678',
            'password' => '12345678',
            'imagen' => $file,// optional
            'role_id' => $role->id,
            'departamento_id' => $departamento->id, // optional
        ]);

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('imagen')
                ->has('nombre')
                ->has('paterno')
                ->has('materno')
                ->has('role')
                ->has('departamento')
                ->has('celular')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_create_jefe()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Head of Department');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $role = Role::findByName('Analyst');
        $departamento = Department::all()->random();

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'celular' => '2221245678',
            'password' => '12345678',
            'imagen' => '',// optional
            'role_id' => $role->id,
            'departamento_id' => $departamento->id, // optional
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_create_error_data()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'celular' => '2221245678',
            'password' => '',
            'imagen' => '',// optional
            'role_id' => '',
            'departamento_id' => null, // optional
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_create_imagen_max_size()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $file = UploadedFile::fake()->image('avatar.jpg')->size(1024 * 9);

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'celular' => '2221245678',
            'password' => '',
            'imagen' => $file,// optional
            'role_id' => '',
            'departamento_id' => null, // optional
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_create_repeat_email()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $role = Role::findByName('Analyst');

        $userActive = User::factory()->create();

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => $userActive->email,
            'celular' => '2221245678',
            'password' => '12345678',
            'imagen' => null,// optional
            'role_id' => $role->id,
            'departamento_id' => null, // optional
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_create_repeat_email_soft()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $role = Role::findByName('Analyst');

        $userSoft = User::factory()->create();
        $userSoft->delete();
        $this->assertSoftDeleted($userSoft);

        $response = $this->postJson('api/v1/users', [
            'nombre' => 'John',
            'paterno' => 'Doe',
            'materno' => 'Roblox',
            'email' => $userSoft->email,
            'celular' => '2221245678',
            'password' => '12345678',
            'imagen' => null,// optional
            'role_id' => $role->id,
            'departamento_id' => null, // optional
        ]);

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('imagen')
                ->has('nombre')
                ->has('paterno')
                ->has('materno')
                ->has('role')
                ->has('departamento')
                ->has('celular')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }
}
