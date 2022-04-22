<?php

namespace Tests\Feature\User;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_success_without_department()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $plainPassword = '12345678';
        $userToUpdate = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $userToUpdate->assignRole('analyst');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $userToUpdate->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $userToUpdate->second_lastname)
                ->where('phone', $userToUpdate->phone)
                ->has('department')
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );

        $userToUpdate->refresh();
        $this->assertTrue(Hash::check($plainPassword, $userToUpdate->password));
    }

    public function test_user_update_success_with_new_deparment()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $userToUpdate = User::factory()->create();
        $userToUpdate->assignRole('analyst');

        $department = Department::all()->random();

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
            'department_id' => $department->id,
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $userToUpdate->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $userToUpdate->second_lastname)
                ->where('phone', $userToUpdate->phone)
                ->has('department', fn ($json) => 
                    $json->where('id', $department->id)
                    ->etc()
                )
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_update_success_with_null_deparment()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $userToUpdate = User::factory()->create();
        $userToUpdate->assignRole('analyst');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
            'department_id' => '',
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $userToUpdate->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $userToUpdate->second_lastname)
                ->where('phone', $userToUpdate->phone)
                ->whereType('department', 'null')
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_update_success_with_image()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $userToUpdate = User::factory()->create();
        $userToUpdate->assignRole('analyst');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
            'imagen_file' => $file,
            'department_id' => null,
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $userToUpdate->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $userToUpdate->second_lastname)
                ->where('phone', $userToUpdate->phone)
                ->whereType('department', 'null')
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_update_error_validation()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $userToUpdate = User::factory()->create();
        $userToUpdate->assignRole('analyst');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
        );
    }

    public function test_user_update_error_not_found()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $limitId = (User::count() + 1);

        $role = Role::all()->random();

        $response = $this->postJson("api/v1/users/{$limitId}", [
            'name' => 'name',
            'lastname' => 'surname',
            'second_lastname' => 'surname',
            'email' => 'testfaker@puller.mx',
            'phone' => '54987987',
            'role_id' => $role->id,
            'department_id' => null,
        ]);

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_user_update_error_other_analyst()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('analyst');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $userToUpdate = User::factory()->create();
        $userToUpdate->assignRole('analyst');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_user_update_success_youlself_user()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('analyst');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $response = $this->postJson("api/v1/users/{$user->id}", [
            'name' => $user->name,
            'lastname' => 'surname',
            'second_lastname' => $user->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => '123489787',
            'role_id' => $user->roles()->first()->id,
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $user->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $user->second_lastname)
                ->where('phone', '123489787')
                ->has('department')
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_update_success_with_new_password()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $userToUpdate = User::factory()->create();
        $userToUpdate->assignRole('analyst');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
            'password' => 'jkhdsafasdy',
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $userToUpdate->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $userToUpdate->second_lastname)
                ->where('phone', $userToUpdate->phone)
                ->has('department')
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );

        $userToUpdate->refresh();
        $this->assertTrue(Hash::check('jkhdsafasdy', $userToUpdate->password));
    }

    public function test_user_update_success_with_null_password()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        
        Passport::actingAs($user);
        
        $this->assertAuthenticated();
        
        $plainPassword = '12345678';
        $userToUpdate = User::factory()->create([
            'password' => Hash::make($plainPassword)
        ]);
        $userToUpdate->assignRole('analyst');

        $response = $this->postJson("api/v1/users/{$userToUpdate->id}", [
            'name' => $userToUpdate->name,
            'lastname' => 'surname',
            'second_lastname' => $userToUpdate->second_lastname,
            'email' => 'testfaker@puller.mx',
            'phone' => $userToUpdate->phone,
            'role_id' => $userToUpdate->roles()->first()->id,
            'password' => '',
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('email', 'testfaker@puller.mx')
                ->whereType('image', 'string')
                ->where('name', $userToUpdate->name)
                ->where('lastname', 'surname')
                ->where('second_lastname', $userToUpdate->second_lastname)
                ->where('phone', $userToUpdate->phone)
                ->has('department')
                ->has('role')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );

        $userToUpdate->refresh();
        $this->assertTrue(Hash::check($plainPassword, $userToUpdate->password));
    }
}
