<?php

namespace Tests\Feature\UserDocument;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserDocumentRenameTest extends TestCase
{
    use RefreshDatabase;

    public function test_rename_document_head_success_normal()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::where('name', Dixa::FOLDER)->first();
        $document = Document::factory()
        ->for($documentType, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $filename = 'filename document ' . rand();
        $response = $this->postJson("api/v1/documents/{$document->id}/:rename", [
            'name' => $filename,
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('name', $filename)
                ->has('type', fn ($json) => 
                    $json->where('id', $documentType->id)
                    ->where('name', $documentType->name)
                )
                ->has('location')
                ->has('identifier')
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

    public function test_rename_document_head_success_same_name()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::where('name', Dixa::FOLDER)->first();
        $document = Document::factory()
        ->for($documentType, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->postJson("api/v1/documents/{$document->id}/:rename", [
            'name' => $document->name,
        ]);

        $response->dump();
        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->where('name', $document->name)
                ->has('type', fn ($json) => 
                    $json->where('id', $documentType->id)
                    ->where('name', $documentType->name)
                )
                ->has('location')
                ->has('identifier')
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

    public function test_rename_document_head_error_duplicate()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::where('name', Dixa::FOLDER)->first();
        $documents = Document::factory()
        ->count(3)
        ->for($documentType, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $document = $documents[0];
        $response = $this->postJson("api/v1/documents/{$document->id}/:rename", [
            'name' => $documents[1]->name,
        ]);

        $response->dump();
        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
