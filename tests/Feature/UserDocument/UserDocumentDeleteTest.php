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

class UserDocumentDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_file_head_success_normal()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $documents = Document::factory()->count(3)
        ->for($user->department)
        ->for($typeFile, 'type')
        ->for($user, 'creator')
        ->create();

        $document = $documents->random();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $this->assertDatabaseCount('documents', 3);
        $response = $this->deleteJson("api/v1/documents/{$document->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
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

        $this->assertSoftDeleted($document);
    }


    public function test_delete_folder_head_success_normal()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //level root
        $folderRoot = Document::factory()
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();
        //second level
        $folderSecondLevel = Document::factory()
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->for($folderRoot, 'parent')
        ->create();
        //three level
        $folderThreeLevel = Document::factory()
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->for($folderSecondLevel, 'parent')
        ->create();
        $documents = Document::factory()
        ->count(5)
        ->for($typeFile, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->for($folderThreeLevel, 'parent')
        ->create();

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $this->assertDatabaseCount('documents', 8);
        $response = $this->deleteJson("api/v1/documents/{$folderSecondLevel->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->has('parent')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->where('total', 7)
            ->etc()
        );

        $this->assertSoftDeleted($folderSecondLevel);
        $this->assertSoftDeleted($documents->random());
    }

    public function test_delete_folder_head_success_normal_several_levels()
    {
        $this->seed();

        $deparment = Department::all()->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //level root
        $folderRoot = Document::factory()
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();
        //second level
        $folderSecondLevel = Document::factory()
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->for($folderRoot, 'parent')
        ->create();
        //three level
        $foldersThreeLevel = Document::factory()
        ->count(5)
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->for($folderSecondLevel, 'parent')
        ->create();
        
        $foldersThreeLevel->each(function ($folder) use (
            $typeFile,
            $user
        ) {
            Document::factory()
            ->count(5)
            ->for($typeFile, 'type')
            ->for($user->department)
            ->for($user, 'creator')
            ->for($folder, 'parent')
            ->create();
        });

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $this->assertDatabaseCount('documents', 32);
        $response = $this->deleteJson("api/v1/documents/{$folderSecondLevel->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->has('parent')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->where('total', 31)
            ->etc()
        );

        $this->assertSoftDeleted($folderSecondLevel);
        $this->assertSoftDeleted($foldersThreeLevel->random());
    }

    public function test_delete_file_head_error_policy()
    {
        $this->seed();

        $deparments = Department::all();
        $deparment = $deparments->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $documents = Document::factory()->count(3)
        ->for($user->department)
        ->for($typeFile, 'type')
        ->for($user, 'creator')
        ->create();

        $document = $documents->random();

        $userTwo = User::factory()
            ->for($deparments->where('name', '!=', $deparment->name)->first())
        ->create();
        $userTwo->assignRole('Head of department');

        Passport::actingAs($userTwo);
        $this->assertAuthenticated();

        $response = $this->deleteJson("api/v1/documents/{$document->id}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );

    }

    public function test_delete_file_head_error_not_found()
    {
        $this->seed();

        $deparments = Department::all();
        $deparment = $deparments->random();
        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $documents = Document::factory()->count(3)
        ->for($user->department)
        ->for($typeFile, 'type')
        ->for($user, 'creator')
        ->create();

        $limitId = (Document::count() + 1);

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->deleteJson("api/v1/documents/{$limitId}");

        $response->assertStatus(404)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );

    }
}
