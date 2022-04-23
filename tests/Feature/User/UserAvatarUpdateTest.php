<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserAvatarUpdateTest extends TestCase
{
    use RefreshDatabase;

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
