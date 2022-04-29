<?php

namespace Tests\Feature\UserDocument;

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

class UserDocumentShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_first_level_documents_deparment_head_success()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $document = $documents->random();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents/{$document->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
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

    public function test_show_first_level_documents_deparment_analyst_success()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $document = $documents->random();

        $userAnalyst = User::factory()
            ->for($deparment)
        ->create();
        $userAnalyst->assignRole('Head of department');

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents/{$document->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
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

    public function test_show_first_level_documents_deparment_analyst_error_policy()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        $document = $documents->random();

        $userAnalyst = User::factory()
            ->for(Department::where('id', '!=', $deparment->id)->get()->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents/{$document->id}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_show_first_level_documents_deparment_analyst_error_not_found()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $limit = Document::count() + 1;

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents/{$limit}");

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}