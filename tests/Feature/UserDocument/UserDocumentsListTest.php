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

class UserDocumentsListTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_first_level_documents_deparment_head_success()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        Document::factory()->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data.0', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->whereType('parent', 'null')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_first_level_documents_deparment_analyst_success()
    {
        $this->seed();

        $deparment = Department::all()->random();
        
        $userHead = User::factory()
            ->for($deparment)
        ->create();
        $userHead->assignRole('Head of department');

        $documentType = DocumentType::all();
        Document::factory()->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data.0', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->whereType('parent', 'null')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_first_level_documents_deparment_analyst_success_empty()
    {
        $this->seed();

        $deparment = Department::all()->random();
        
        $userHead = User::factory()
            ->for($deparment)
        ->create();
        $userHead->assignRole('Head of department');

        $documentType = DocumentType::all();
        Document::factory()->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        $deparmentAnalyst = Department::where('id', '!=', $deparment->id)->first();
        $user = User::factory()
            ->for($deparmentAnalyst)
        ->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 0)
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }
}
