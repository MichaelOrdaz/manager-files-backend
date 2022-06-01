<?php

namespace Tests\Feature\DocumentsSharedByMe;

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

class DocumentsSharedByMeListTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_documents_shared_by_me_head()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userLogged = User::factory()
            ->for($department)
        ->create();
        $userLogged->assignRole('Head of department');

        Passport::actingAs($userLogged);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->create();

        $departments = $departments->where('id', '!=', $userLogged->department->id);

        $users = User::factory()
        ->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();

        $users->each(fn ($user) => $user->assignRole('analyst'));

        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $documents->each(function ($document) use ($users, $sharePermissions, $userLogged) {

            $users->each(function ($user) use ($document, $sharePermissions, $userLogged) {
                $user->share()->attach($document, [
                    'permission' => $sharePermissions->random()->name,
                    'granted_by' => $userLogged->id,
                ]);
            });

        });

        $response = $this->getJson("api/v1/share-documents/by-me");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
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

    public function test_list_documents_shared_by_me_mixed_parents()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userLogged = User::factory()
            ->for($department)
        ->create();
        $userLogged->assignRole('Head of department');

        Passport::actingAs($userLogged);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //structure
        /** 
         - folder (share)
            - file
            - file
            - file
         - folder
            - file (share)
            - file
            - file
        
        */
        //level root
        $foldersRoot = Document::factory()
        ->count(2)
        ->for($typeFolder, 'type')
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userLogged->department->id);

        $users = User::factory()
        ->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();
        $users->each(fn ($user) => $user->assignRole('analyst'));

        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();

        $firstFolderRoot = $foldersRoot[0];
        $users->each(function ($user) use (
            $firstFolderRoot, 
            $sharePermissions, 
            $userLogged
        ) {
            $user->share()->attach($firstFolderRoot, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $userLogged->id,
            ]);
        });

        $users->first()->share()->attach($fileToShared, [
            'permission' => $sharePermissions->random()->name,
            'granted_by' => $userLogged->id,
        ]);

        $response = $this->getJson("api/v1/share-documents/by-me");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 2, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->has('parent')
                ->whereType('sons_count', 'integer')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_documents_shared_by_me_subfolder()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userLogged = User::factory()
            ->for($department)
        ->create();
        $userLogged->assignRole('Head of department');

        Passport::actingAs($userLogged);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //structure
        /** 
         - folder (share) (listing)
            - file
            - file
            - file
         - folder
            - file (share)
            - file
            - file
        
        */
        //level root
        $foldersRoot = Document::factory()
        ->count(2)
        ->for($typeFolder, 'type')
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userLogged->department->id);

        $users = User::factory()
        ->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();
        $users->each(fn ($user) => $user->assignRole('analyst'));

        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();

        $firstFolderRoot = $foldersRoot[0];
        $users->each(function ($user) use (
            $firstFolderRoot, 
            $sharePermissions, 
            $userLogged
        ) {
            $user->share()->attach($firstFolderRoot, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $userLogged->id,
            ]);
        });

        $users->first()->share()->attach($fileToShared, [
            'permission' => $sharePermissions->random()->name,
            'granted_by' => $userLogged->id,
        ]);

        $response = $this->getJson("api/v1/share-documents/by-me?parent={$foldersRoot[0]->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('createdAt')
                ->has('parent')
                ->whereType('sons_count', 'integer')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_documents_shared_by_me_head_by_department()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userLogged = User::factory()
            ->for($department)
        ->create();
        $userLogged->assignRole('Head of Department');

        Passport::actingAs($userLogged);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userLogged->department)
        ->for($userLogged, 'creator')
        ->create();

        $departments = $departments->where('id', '!=', $userLogged->department->id)->values();
        $userA = User::factory()
        ->for($departments[0])
        ->create();
        $userA->assignRole('Analyst');

        $userB = User::factory()
        ->for($departments[1])
        ->create();
        $userB->assignRole('Analyst');

        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();

        $documents->take(2)->each(function ($document) use (
            $userA,
            $userB,
            $sharePermissions,
            $userLogged,
        ) {

            $userA->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $userLogged->id,
            ]);

        });

        $response = $this->getJson("api/v1/share-documents/by-me?department_id={$departments[0]->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 2, fn ($json) => 
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
}
