<?php

namespace Tests\Feature\UserDocument;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserDocumentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_file_head_success_normal()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        $documentTypes = DocumentType::all();
        $typeFile = $documentTypes->where('name', Dixa::FILE)->first();

        $location = [];

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $ext = 'pdf';
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 10000);

        $filename = 'filename document ' . rand();
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '1002',
            'file' => $file,
        ]);

        $location[] = $filename . '.' . $ext;

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type', fn ($json) => 
                    $json->where('id', $typeFile->id)
                    ->where('name', $typeFile->name)
                )
                ->where('location', implode('/', $location))
                ->has('indentifier')
                ->has('description')
                ->whereType('tags', 'array')
                ->has('createdAt')
                ->has('creator', fn($json) => 
                    $json->has('id')
                    ->has('email')
                    ->has('name')
                    ->has('lastname')
                    ->has('second_lastname')
                    ->has('role')
                    ->etc()
                )
                ->whereType('parent', 'null')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_create_file_head_success_with_parent()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        $documentTypes = DocumentType::all();
        $typeFolder = $documentTypes->where('name', Dixa::FOLDER)->first();
        $typeFile = $documentTypes->where('name', Dixa::FILE)->first();

        $location = [];

        $folderRoot = Document::factory()
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->state(fn (array $attr) => [
            'location' => $attr['name']
        ])
        ->create();

        $location[] = $folderRoot->name;

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $ext = 'pdf';
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 10000);

        $filename = 'filename document ' . rand();
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '',
            'file' => $file,
            'parent_id' => $folderRoot->id,
        ]);

        $location[] = $filename . '.' . $ext;

        $response->assertStatus(201)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type', fn ($json) => 
                    $json->where('id', $typeFile->id)
                    ->where('name', $typeFile->name)
                )
                ->where('location', implode('/', $location))
                ->has('indentifier')
                ->has('description')
                ->whereType('tags', 'array')
                ->has('createdAt')
                ->has('creator', fn($json) => 
                    $json->has('id')
                    ->has('email')
                    ->has('name')
                    ->has('lastname')
                    ->has('second_lastname')
                    ->has('role')
                    ->etc()
                )
                ->has('parent', fn ($json) => 
                    $json->where('id', $folderRoot->id)
                    ->where('name', $folderRoot->name)
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_create_file_head_error_filename()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $ext = 'pdf';
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 10000);

        $filename = '$filename with char special#';
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '',
            'file' => $file,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_create_file_head_error_maxsize()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $ext = 'pdf';
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 124001);

        $filename = 'filename';
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '',
            'file' => $file,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_create_file_head_error_without_file()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $filename = 'filename';
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '',
            'file' => '',
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_create_file_head_error_mime_type()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $ext = 'xlsx';
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 1000);

        $filename = 'filename';
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '',
            'file' => $file,
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_create_file_analyst_error_difference_department()
    {
        $this->seed();

        $deparments = Department::all();
        $deparmentUser = $deparments->random();
        $user = User::factory()
            ->for($deparmentUser)
        ->create();
        $user->assignRole('Head of department');

        $user = User::factory()
            ->for($deparments->where('name', '!=', $deparmentUser->name)->first())
        ->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $ext = 'pdf';
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 1000);

        $filename = 'filename';
        $response = $this->postJson("api/v1/documents", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo consequatur eius delectus dolorem explicabo modi, nobis a cumque officia, doloremque amet dicta provident omnis, saepe asperiores quibusdam officiis vero rerum.',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '',
            'file' => $file,
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
