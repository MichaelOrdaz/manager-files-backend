<?php

namespace Tests\Feature\UserDocumentTags;

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserDocumentTagsDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_tags_success()
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

        $tagName = $document->tags[0];
        $response = $this->deleteJson("api/v1/documents/{$document->id}/tags/{$tagName}");

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
        $this->assertNotContains($tagName, $response['data']['tags']);
    }

    public function test_delete_tags_error_policy()
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

        $tagName = $document->tags[0];
        $response = $this->deleteJson("api/v1/documents/{$document->id}/tags/{$tagName}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
