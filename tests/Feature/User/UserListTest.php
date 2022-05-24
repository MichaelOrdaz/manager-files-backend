<?php

namespace Tests\Feature\User;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_users_list_normal()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->create();
        $user = $users->first();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $response = $this->getJson('api/v1/users');

        $this->assertAuthenticated();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 6, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
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
            ->etc()
        );
    }

    public function test_users_list_search_clear()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->count(20)->create();
        $user = $users->first();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/users/search');

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 10, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->etc()
            )
            ->has('meta', fn ($json) => 
                $json->has('current_page')
                ->has('total')
                ->where('total', User::count())
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_users_list_search_by_page()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->count(50)->create();
        $user = $users->first();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $perPage = 20;
        $response = $this->getJson("api/v1/users/search?page=2&perPage={$perPage}&sortBy=name");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $perPage, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->etc()
            )
            ->has('meta', fn ($json) => 
                $json->has('current_page')
                ->where('total', User::count())
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_users_list_search_name()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->count(50)->create();
        $user = $users->first();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $search = 'no';
        $total = User::where('name', 'like', "%{$search}%")
        ->orWhere('lastname', 'like', "%{$search}%")
        ->orWhere('second_lastname', 'like', "%{$search}%")
        ->count();
        $totalPerPage = $total > 10 ? 10 : $total;
        $response = $this->getJson('api/v1/users/search?name=' . $search);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $totalPerPage, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->etc()
            )
            ->has('meta', fn ($json) => 
                $json->has('current_page')
                ->has('total')
                ->where('total', $total)
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_users_list_search_role()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $users = User::factory()->count(50)->create();
        $user = $users->first();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $role = Role::all()->random()->id;
        $total = User::whereHas('roles', function ($query) use ($role) {
            $query->where('id', $role);
        })->count();
        $totalPerPage = $total > 10 ? 10 : $total;
        $response = $this->getJson('api/v1/users/search?role=' . $role);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $totalPerPage, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('phone')
                ->etc()
            )
            ->has('meta', fn ($json) => 
                $json->has('current_page')
                ->has('total')
                ->where('total', $total)
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_has_permissions_user()
    {
        $this->seed();

        $this->assertDatabaseCount('roles', 3);

        $user = User::factory()->create();
        $user->assignRole('Admin');
        Passport::actingAs($user);
        $this->assertAuthenticated();
        $this->assertTrue($user->can('user.show'));
        $this->assertTrue($user->can('user.create'));
        $this->assertTrue($user->can('user.update'));
        $this->assertTrue($user->can('user.delete'));
    }

    public function test_users_show_success_admin()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $userToQuery = User::all()->random();

        $response = $this->getJson("api/v1/users/{$userToQuery->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
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

    public function test_users_list_by_department_success_normal()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);

        $departments = Department::all();
        $department = $departments->random();

        $users =User::factory()
        ->for($department)
        ->count(3)
        ->create();
        $users->each(fn ($user) => $user->assignRole('Analyst'));

        $totalUser = User::whereHas('department', fn ($query) =>
            $query->where('id', $department->id)
        )->count();

        $this->assertAuthenticated();
        $response = $this->getJson("api/v1/users?department_id={$department->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $totalUser, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
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
            ->etc()
        );
    }

    public function test_users_list_head_error_403()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $departments = Department::all();

        $user = User::factory()
        ->create();
        $user->assignRole('analyst');

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson('api/v1/users');

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_users_list_head_success()
    {
        $this->seed();

        $this->assertDatabaseCount('users', 5);

        $departments = Department::all();
        $department = $departments->random();
        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('analyst');
        $user->syncPermissions(Dixa::ANALYST_WRITE_PERMISSION);

        $user = User::factory()
        ->for($department)
        ->create();
        $user->assignRole('head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/users?department_id={$department->id}");

        $total = User::where('department_id', $department->id)->count();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $total, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
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
            ->etc()
        );
    }

}
