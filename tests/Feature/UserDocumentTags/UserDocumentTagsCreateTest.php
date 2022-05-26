<?php

namespace Tests\Feature\UserDocumentTags;

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
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserDocumentTagsCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_tags_success()
    {
        $this->seed();

        $departments = Department::all();
        $deparment = $departments->random();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $document = Document::factory()
        ->for($documentType->random(), 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $response = $this->postJson("api/v1/documents/{$document->id}/tags", [
            'tags' => [
                'lorem',
                'ipsum',
                'dolor'
            ]
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('description')
                ->has('type')
                ->has('location')
                ->has('identifier')
                ->has('date')
                ->where('tags.0', 'lorem')
                ->where('tags.1', 'ipsum')
                ->where('tags.2', 'dolor')
                ->has('createdAt')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_update_tags_empty_success()
    {
        $this->seed();

        $departments = Department::all();
        $deparment = $departments->random();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $document = Document::factory()
        ->for($documentType->random(), 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $response = $this->postJson("api/v1/documents/{$document->id}/tags", [
            'tags' => []
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('description')
                ->has('type')
                ->has('location')
                ->has('identifier')
                ->has('date')
                ->whereType('tags', 'array')
                ->has('createdAt')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_update_tags_with_tags_success()
    {
        $this->seed();

        $departments = Department::all();
        $deparment = $departments->random();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $document = Document::factory()
        ->for($documentType->random(), 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $response = $this->postJson("api/v1/documents/{$document->id}/tags", [
        ]);

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('description')
                ->has('type')
                ->has('location')
                ->has('identifier')
                ->has('date')
                ->whereType('tags', 'array')
                ->has('createdAt')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_update_tags_with_tags_error_validate()
    {
        $this->seed();

        $departments = Department::all();
        $deparment = $departments->random();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $document = Document::factory()
        ->for($documentType->random(), 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $response = $this->postJson("api/v1/documents/{$document->id}/tags", [
            'tags' => [
                'perrito',
                's'
            ]
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_update_tags_with_tags_error_policy()
    {
        $this->seed();

        $departments = Department::all();
        $deparment = $departments->random();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $document = Document::factory()
        ->for($documentType->random(), 'type')
        ->for($departments->where('id', '!=', $deparment->id)->random())
        ->for($user, 'creator')
        ->create();

        $response = $this->postJson("api/v1/documents/{$document->id}/tags", [
            'tags' => [
                'perrito',
                's'
            ]
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }
}
