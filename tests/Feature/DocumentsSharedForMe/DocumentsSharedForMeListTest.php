<?php

namespace Tests\Feature\DocumentsSharedForMe;

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

class DocumentsSharedForMeListTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_documents_shared_for_me()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of Department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(4)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionsRead = Permission::findByName(Dixa::ANALYST_READ_PERMISSION);

        $documents->take(2)->each(function ($document) use ($userAnalyst, $sharePermissionsRead, $userHead) {
            $userAnalyst->share()->attach($document, [
                'permission' => $sharePermissionsRead->name,
                'granted_by' => $userHead->id,
            ]);
        });

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 2, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('url')
                ->has('permission')
                ->has('grantedBy')
                ->has('createdAt')
                ->whereType('parent', 'null')
                ->whereType('sons_count', 'integer')
                ->has('department', fn ($json) => 
                    $json->has('id')
                    ->has('name')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_documents_shared_for_me_mixed_levels()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();
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
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $users = User::factory()
        ->count(2)
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
            $userHead
        ) {
            $user->share()->attach($firstFolderRoot, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $userHead->id,
            ]);
        });

        $userAnalyst = $users->first();
        $userAnalyst->share()->attach($fileToShared, [
            'permission' => $sharePermissions->random()->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 2, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('url')
                ->has('permission')
                ->has('grantedBy')
                ->has('createdAt')
                ->whereType('parent', 'null')
                ->whereType('sons_count', 'integer')
                ->has('department', fn ($json) => 
                    $json->has('id')
                    ->has('name')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_documents_shared_for_me_mixed_levels_2()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();
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
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $users = User::factory()
        ->count(2)
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
            $userHead
        ) {
            $user->share()->attach($firstFolderRoot, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $userHead->id,
            ]);
        });

        $users->first()->share()->attach($fileToShared, [
            'permission' => $sharePermissions->random()->name,
            'granted_by' => $userHead->id,
        ]);

        $userAnalyst = $users->last();
        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 1, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('url')
                ->has('permission')
                ->has('grantedBy')
                ->has('createdAt')
                ->whereType('parent', 'null')
                ->whereType('sons_count', 'integer')
                ->has('department', fn ($json) => 
                    $json->has('id')
                    ->has('name')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_documents_shared_for_me_subfolder()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
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
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionWrite = Permission::findByName(Dixa::ANALYST_WRITE_PERMISSION);

        $firstFolderRoot = $foldersRoot[0];
        $userAnalyst->share()->attach($firstFolderRoot, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);
        $userAnalyst->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me?parent={$foldersRoot[0]->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('url')
                ->has('permission')
                ->has('grantedBy')
                ->has('createdAt')
                ->has('parent')
                ->whereType('sons_count', 'integer')
                ->has('department', fn ($json) => 
                    $json->has('id')
                    ->has('name')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_list_documents_shared_for_me_folder_into_subfolder()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //structure
        /** 
         - folder (share)
            - folder (listing)
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
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->for($typeFolder, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        $documentsThreeLevel = Document::factory()
        ->count(2)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($firstFolderSecondLevel, 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionWrite = Permission::findByName(Dixa::ANALYST_WRITE_PERMISSION);

        $firstFolderRoot = $foldersRoot[0];
        $userAnalyst->share()->attach($firstFolderRoot, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);
        $userAnalyst->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me?parent={$firstFolderSecondLevel->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 2, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('url')
                ->has('permission')
                ->has('grantedBy')
                ->has('createdAt')
                ->has('parent')
                ->whereType('sons_count', 'integer')
                ->has('department', fn ($json) => 
                    $json->has('id')
                    ->has('name')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }


    public function test_list_documents_shared_for_me_subfolder_error_policy()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
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
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionWrite = Permission::findByName(Dixa::ANALYST_WRITE_PERMISSION);

        $firstFolderRoot = $foldersRoot[0];
        $userAnalyst->share()->attach($firstFolderRoot, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);
        $userAnalyst->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        $userAnalyst2 = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst2->assignRole('analyst');

        Passport::actingAs($userAnalyst2);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me?parent={$foldersRoot[0]->id}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
            ->etc()
        );
    }

    public function test_list_documents_shared_for_me_empty()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of Department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(4)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionsRead = Permission::findByName(Dixa::ANALYST_READ_PERMISSION);

        $documents->take(2)->each(function ($document) use ($userAnalyst, $sharePermissionsRead, $userHead) {
            $userAnalyst->share()->attach($document, [
                'permission' => $sharePermissionsRead->name,
                'granted_by' => $userHead->id,
            ]);
        });

        $userAnalyst2 = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst2->assignRole('analyst');

        Passport::actingAs($userAnalyst2);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 0)
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_show_document_shared_for_me_folder_into_subfolder()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //structure
        /** 
         - folder (share)
            - folder
                - file (showing)
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
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->for($typeFolder, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        $documentsThreeLevel = Document::factory()
        ->count(2)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($firstFolderSecondLevel, 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionWrite = Permission::findByName(Dixa::ANALYST_WRITE_PERMISSION);

        $firstFolderRoot = $foldersRoot[0];
        $userAnalyst->share()->attach($firstFolderRoot, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);
        $userAnalyst->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me/{$documentsThreeLevel[0]->id}");

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
                ->has('creator', fn ($json) => 
                    $json->has('id')
                    ->has('email')
                    ->has('name')
                    ->has('lastname')
                    ->has('second_lastname')
                    ->has('role')
                    ->etc()
                )
                ->has('historical.0', fn ($json) => 
                    $json->has('id')
                    ->has('user', fn ($json) => 
                        $json->has('name')
                        ->etc()
                    )
                    ->has('action')
                    ->has('date')
                    ->etc()
                )
                ->whereType('share', 'array')
                ->has('parent')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
            ->etc()
        );
    }

    public function test_show_document_shared_for_me_folder_into_subfolder_error()
    {
        $this->seed();

        $departments = Department::all();
        $department = $departments->random();

        $userHead = User::factory()
            ->for($department)
        ->create();
        $userHead->assignRole('Head of department');

        Passport::actingAs($userHead);
        $this->assertAuthenticated();

        $documentType = DocumentType::all();
        $typeFile = $documentType->where('name', Dixa::FILE)->first();
        $typeFolder = $documentType->where('name', Dixa::FOLDER)->first();

        //structure
        /** 
         - folder (share)
            - folder
                - file
                - file
         - folder (showing)
            - file (share)
            - file
            - file
        
        */
        //level root
        $foldersRoot = Document::factory()
        ->count(2)
        ->for($typeFolder, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->create();

        //second levels first folder
        $firstFolderSecondLevel = Document::factory()
        ->for($typeFolder, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[0], 'parent')
        ->create();
        $documentsThreeLevel = Document::factory()
        ->count(2)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($firstFolderSecondLevel, 'parent')
        ->create();
        //second levels second folder
        $secondFolderSecondLevel = Document::factory()
        ->count(3)
        ->for($typeFile, 'type')
        ->for($userHead->department)
        ->for($userHead, 'creator')
        ->for($foldersRoot[1], 'parent')
        ->create();
        $fileToShared = $secondFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $userAnalyst = User::factory()
        ->for($departments->random())
        ->create();
        $userAnalyst->assignRole('analyst');

        $sharePermissionWrite = Permission::findByName(Dixa::ANALYST_WRITE_PERMISSION);

        $firstFolderRoot = $foldersRoot[0];
        $userAnalyst->share()->attach($firstFolderRoot, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);
        $userAnalyst->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me/{$foldersRoot[1]->id}");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
