<?php

namespace Tests\Feature\DocumentsSharedForMe;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentsSharedForMeLegacyPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_permission_legacy()
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
            - file (share)
            - file
            - file
        */
        //level root
        $folderRoot = Document::factory()
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
        ->for($folderRoot, 'parent')
        ->create();
        $fileToShared = $firstFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('Analyst');

        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $sharePermissionWrite = $sharePermissions->where('name', Dixa::PERMISSION_TO_WRITE_SHARED_DOCUMENT)->first();
        $sharePermissionRead = $sharePermissions->where('name', Dixa::PERMISSION_TO_READ_SHARED_DOCUMENT)->first();

        $user->share()->attach($folderRoot, [
            'permission' => $sharePermissionRead->name,
            'granted_by' => $userHead->id,
        ]);

        $user->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($user);
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
                ->where('permission', Dixa::PERMISSION_TO_READ_SHARED_DOCUMENT)
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

    public function test_check_permission_legacy_in_subfolder()
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
            - file (share)
            - file
            - file
        */
        //level root
        $folderRoot = Document::factory()
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
        ->for($folderRoot, 'parent')
        ->create();
        $fileToShared = $firstFolderSecondLevel->first();

        $departments = $departments->where('id', '!=', $userHead->department->id);

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('Analyst');

        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $sharePermissionWrite = $sharePermissions->where('name', Dixa::PERMISSION_TO_WRITE_SHARED_DOCUMENT)->first();
        $sharePermissionRead = $sharePermissions->where('name', Dixa::PERMISSION_TO_READ_SHARED_DOCUMENT)->first();

        $user->share()->attach($folderRoot, [
            'permission' => $sharePermissionRead->name,
            'granted_by' => $userHead->id,
        ]);

        $user->share()->attach($fileToShared, [
            'permission' => $sharePermissionWrite->name,
            'granted_by' => $userHead->id,
        ]);

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-documents/for-me?parent={$folderRoot->id}");

        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', 3, fn ($json) => 
                $json->has('id')
                ->has('name')
                ->has('type')
                ->has('location')
                ->has('url')
                ->where('permission', $sharePermissionWrite->name)
                ->has('grantedBy')
                ->has('createdAt')
                ->has('parent', fn ($json) => 
                    $json->where('id', $folderRoot->id)
                    ->has('name')
                    ->etc()
                )
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
}
