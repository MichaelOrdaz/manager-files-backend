<?php

namespace Tests\Feature\SharedDocuments;

use App\Helpers\Dixa;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ShareDocumentsUsersListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_list_with_permission_of_share_document_success()
    {
        $this->seed();

        $departments = Department::all();

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('head of department');
        
        Passport::actingAs($user);
        $this->assertAuthenticated();
        
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

        $departments = $departments->where('id', '!=', $user->department->id);

        $users = User::factory()
        ->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();

        $users->each(fn ($user) => $user->assignRole('analyst'));

        $document = $documents->random();
        
        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();

        $userWithPermission = $users->random();
        $userWithPermission->share()->attach($document, [
            'permission' => $sharePermissions->random()->name,
            'granted_by' => $user->id,
        ]);

        $response = $this->getJson("api/v1/share-documents/{$document->id}/users");

        $total = User::count();
        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $total, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->whereType('image', 'null')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->whereType('share', 'array')
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_list_with_permission_all_of_share_document_success()
    {
        $this->seed();

        $departments = Department::all();

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('head of department');
        
        Passport::actingAs($user);
        $this->assertAuthenticated();
        
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
        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $departments = $departments->where('id', '!=', $user->department->id);

        $users = User::factory()
        ->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();

        $users->each(function ($user) use ($document, $sharePermissions) {
            $user->assignRole('analyst');

            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $response = $this->getJson("api/v1/share-documents/{$document->id}/users");

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data.10', fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('share', 1, fn ($json) => 
                    $json->has('id')
                    ->has('permission')
                    ->has('createdAt')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_list_with_permission_all_of_share_document_by_department_success()
    {
        $this->seed();

        $departments = Department::all();

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('head of department');
        
        Passport::actingAs($user);
        $this->assertAuthenticated();
        
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
        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $departments = $departments->where('id', '!=', $user->department->id);

        $users = User::factory()
        ->count(10)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();

        $users->each(function ($user) use ($document, $sharePermissions) {
            $user->assignRole('analyst');

            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $department = $departments->random();
        User::doesntHave('share')->delete();
        $totalUser = User::where('department_id', $department->id)->count();
        
        $response = $this->getJson("api/v1/share-documents/{$document->id}/users?department_id={$department->id}");

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $totalUser, fn ($json) => 
                $json->has('id')
                ->has('email')
                ->has('image')
                ->has('name')
                ->has('lastname')
                ->has('second_lastname')
                ->has('role')
                ->has('department')
                ->has('share', 1, fn ($json) => 
                    $json->has('id')
                    ->has('permission')
                    ->has('createdAt')
                    ->etc()
                )
                ->etc()
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_user_list_with_permission_of_share_document_error_policy()
    {
        $this->seed();

        $departments = Department::all();

        $user = User::factory()
        ->for($departments->random())
        ->create();
        $user->assignRole('analyst');
        
        Passport::actingAs($user);
        $this->assertAuthenticated();
        
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

        $response = $this->getJson("api/v1/share-documents/{$document->id}/users");

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('errors')
            ->where('success', false)
            ->etc()
        );
    }
}
