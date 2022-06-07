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

class UserDocumentUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_file_head_success_normal()
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
        $file = UploadedFile::fake()->create("document.{$ext}", $KILOBYTES = 1000);

        $document = Document::factory()
        ->state(fn (array $attr) => [
            'location' => $attr['name']
        ])
        ->for($typeFile, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $filename = 'update filename document ' . rand();
        $response = $this->postJson("api/v1/documents/{$document->id}", [
            'name' => $filename,
            'description' => 'Lorem ipsum dolor',
            'date' => '2022-05-05',
            'min_identifier' => '1000',
            'max_identifier' => '1002',
            'file' => $file,
        ]);

        $location[] = $filename . '.' . $ext;

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('name', $filename)
                ->has('type', fn ($json) => 
                    $json->where('id', $typeFile->id)
                    ->where('name', $typeFile->name)
                )
                ->where('location', implode('/', $location))
                ->where('identifier', '1000-1002')
                ->where('description', 'Lorem ipsum dolor')
                ->whereType('tags', 'array')
                ->has('createdAt')
                ->where('date', '2022-05-05')
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
}
