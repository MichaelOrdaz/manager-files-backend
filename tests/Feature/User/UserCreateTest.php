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
        $department = Department::all()->random();

        $response = $this->postJson('api/v1/users', [
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'phone' => '2221245678',
            'password' => '12345678',
            'role_id' => $role->id,
            'department_id' => $department->id, // optional
        ]);

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->whereType('image', 'null')
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
        $department = Department::all()->random();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('api/v1/users', [
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'phone' => '2221245678',
            'password' => '12345678',
            'image_file' => $file,// optional
            'role_id' => $role->id,
            'department_id' => $department->id, // optional
        ]);

        $response->assertStatus(201)
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
        $department = Department::all()->random();

        $response = $this->postJson('api/v1/users', [
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'phone' => '2221245678',
            'password' => '12345678',
            'role_id' => $role->id,
            'department_id' => $department->id, // optional
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
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'phone' => '2221245678',
            'password' => '',
            'role_id' => '',
            'department_id' => null, // optional
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
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => 'johndoe@mail.com',
            'phone' => '2221245678',
            'password' => '',
            'imagen_file' => $file,// optional
            'role_id' => '',
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
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => $userActive->email,
            'phone' => '2221245678',
            'password' => '12345678',
            'role_id' => $role->id,
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
            'name' => 'John',
            'lastname' => 'Doe',
            'second_lastname' => 'Roblox',
            'email' => $userSoft->email,
            'phone' => '2221245678',
            'password' => '12345678',
            'role_id' => $role->id,
        ]);

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->whereType('image', 'null')
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
    }

    public function test_user_update_image_success_analyst()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson("api/v1/users/image", [
            'image' => $file,
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
    }

    public function test_user_update_image_success_admin()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson("api/v1/users/image", [
            'image' => $file,
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
    }

    public function test_user_update_image_error_empty()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->postJson("api/v1/users/image", [
            'image' => null,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_update_image_error_max_size()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $file = UploadedFile::fake()->image('avatar.jpg')->size(1024 * 10);

        $response = $this->postJson("api/v1/users/image", [
            'image' => $file,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_update_image_error_pdf()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $file = UploadedFile::fake()->image('avatar.pdf');

        $response = $this->postJson("api/v1/users/image", [
            'image' => $file,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_delete_image_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Analyst');

        Passport::actingAs($user);

        $this->assertAuthenticated();

        $response = $this->deleteJson("api/v1/users/image");

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->whereType('image', 'null')
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
    }
}
