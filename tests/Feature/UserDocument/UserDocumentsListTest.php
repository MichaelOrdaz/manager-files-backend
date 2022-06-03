<?php

namespace Tests\Feature\UserDocument;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
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
        Document::factory()->count(25)
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

        $response = $this->getJson("api/v1/documents?perPage=10");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 10, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->whereType('parent', 'null')
                ->whereType('sons_count', 'integer')
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
            $json->has('data', 10, fn ($json) => 
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

    public function test_list_second_level_documents_deparment_analyst_success()
    {
        $this->seed();

        $deparment = Department::all()->random();
        
        $userHead = User::factory()
            ->for($deparment)
        ->create();
        $userHead->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();
        
        $folderRoot = Document::factory()
        ->for($typeFolder, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        $documents = Document::factory()->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($folderRoot, 'parent')
        ->create();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('analyst');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents?parent={$folderRoot->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data.0', fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->has('parent', fn ($json) => 
                    $json->where('id', $folderRoot->id)
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_second_level_documents_deparment_analyst_success_sons()
    {
        $this->seed();

        $deparment = Department::all()->random();
        
        $userHead = User::factory()
            ->for($deparment)
        ->create();
        $userHead->assignRole('Head of department');

        $documentType = DocumentType::all();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();
        
        $folderRoot = Document::factory()
        ->for($typeFolder, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        $documents = Document::factory()->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($folderRoot, 'parent')
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
                ->whereType('sons_count', 'integer')
                ->whereType('parent', 'null')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_advances_filters_tags_success()
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

        /**
         * structure
         *  -folder
         *      -folder
         *          -file
         *          -file
         *      -folder
         *          -file
         *          -file
         *      - file
         *      - file
         *  -folder
         *      -folder
         *          -file
         *          -file
         *      -folder
         *          -file
         *          -file
         *      - file
         *      - file
         */

        //level root
        $foldersRoot = Document::factory()
        ->count(10)
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();
        $foldersRoot->each(function ($folderRoot) use ($typeFile, $typeFolder, $user) {
            //second level
            $foldersSecondLevel = Document::factory()
            ->count(2)
            ->for($typeFolder, 'type')
            ->for($user->department)
            ->for($user, 'creator')
            ->for($folderRoot, 'parent')
            ->create();
            //second level
            $filesSecondLevel = Document::factory()
            ->count(2)
            ->for($typeFile, 'type')
            ->for($user->department)
            ->for($user, 'creator')
            ->for($folderRoot, 'parent')
            ->create();

            $foldersSecondLevel->each(function ($folderSecondLevel) use ($typeFile, $user) {
                //three level
                $folderThreeLevel = Document::factory()
                ->for($typeFile, 'type')
                ->for($user->department)
                ->for($user, 'creator')
                ->for($folderSecondLevel, 'parent')
                ->create();
            });
        });

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $tags = $foldersRoot->take(3)->map(fn ($item) => $item->tags[0]);
        $queryString = Arr::query([
            'all' => true,
            'type' => Dixa::FOLDER,
            'tags' => $tags,
        ]);
        $response = $this->getJson("api/v1/documents?{$queryString}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data.0', fn ($json) => 
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
            ->etc()
        );
    }

    public function test_list_advances_filters_identifiers_success()
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

        /**
         * structure
         *  -folder
         *      -folder
         *          -file
         *          -file
         *      -folder
         *          -file
         *          -file
         *      - file
         *      - file
         *  -folder
         *      -folder
         *          -file
         *          -file
         *      -folder
         *          -file
         *          -file
         *      - file
         *      - file
         */

        //level root
        $identifiers = [];
        $foldersRoot = Document::factory()
        ->count(10)
        ->for($typeFolder, 'type')
        ->for($user->department)
        ->for($user, 'creator')
        ->create();
        $foldersRoot->each(function ($folderRoot) use ($typeFile, $typeFolder, $user, &$identifiers) {
            //second level
            $foldersSecondLevel = Document::factory()
            ->count(2)
            ->for($typeFolder, 'type')
            ->for($user->department)
            ->for($user, 'creator')
            ->for($folderRoot, 'parent')
            ->create();
            //second level
            $filesSecondLevel = Document::factory()
            ->count(2)
            ->for($typeFile, 'type')
            ->for($user->department)
            ->for($user, 'creator')
            ->for($folderRoot, 'parent')
            ->create();

            if (count($identifiers) <= 2) {
                $identifiers[] = $filesSecondLevel->random()->min_identifier;
            }

            $foldersSecondLevel->each(function ($folderSecondLevel) use ($typeFile, $user, &$identifiers) {
                //three level
                $fileThreeLevel = Document::factory()
                ->count(2)
                ->for($typeFile, 'type')
                ->for($user->department)
                ->for($user, 'creator')
                ->for($folderSecondLevel, 'parent')
                ->create();
            });
        });

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $queryString = Arr::query([
            'all' => true,
            'type' => Dixa::FILE,
            'identifiers' => $identifiers,
        ]);
        $response = $this->getJson("api/v1/documents?{$queryString}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data.0', fn ($json) => 
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
            ->etc()
        );
    }
}
