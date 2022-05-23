<?php

namespace Tests\Feature\SharedDocuments;

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
use MacsiDigital\Zoom\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SharedDocumentsUserCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_assing_permission_to_users_share_document_success()
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
        ->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();

        $payload = $users->map(function ($user) use ($sharePermissions) {
            return [
                'id' => $user->id,
                'permission' => $sharePermissions->random()->name,
            ];
        });

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => $payload
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('attached', 3)
                ->has('detached')
                ->has('updated')
            )
            ->has('message')
            ->where('success', true)
        );
    }


    public function test_assing_permission_updated_to_users_share_document_success()
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
        ->count(5)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();
        $users->each(fn ($user) => $user->assignRole('analyst'));

        $usersRandom = $users->random(2);
        $usersRandom->each(function ($user) use ($document, $sharePermissions) {
            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $payload = $users->map(function ($user) use ($sharePermissions) {
            return [
                'id' => $user->id,
                'permission' => $sharePermissions->random()->name,
            ];
        });

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => $payload
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('attached', 3)
                ->has('detached')
                ->has('updated', 2)
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_assing_permission_updated_quit_permission_to_users_share_document_success()
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
        ->count(6)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();
        $users->each(fn ($user) => $user->assignRole('analyst'));

        $usersRandom = $users->take(4);
        $usersRandom->each(function ($user) use ($document, $sharePermissions) {
            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $payload = $usersRandom->take(2)->merge($users->take(-2))->map(function ($user) use ($sharePermissions) {
            return [
                'id' => $user->id,
                'permission' => $sharePermissions->random()->name,
            ];
        });

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => $payload
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('attached', 2)
                ->has('detached', 2)
                ->has('updated', 2)
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_assing_permission_to_users_share_document_error_empty_list()
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

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => [
                [
                    'id' => 100,
                    'permission' => $sharePermissions->random()->name
                ],
                [
                    'id' => 200,
                    'permission' => $sharePermissions->random()->name
                ],
                [
                    'id' => 300,
                    'permission' => $sharePermissions->random()->name
                ],
            ]
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_assing_permission_to_users_share_document_error_permission()
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
        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $departments = $departments->where('id', '!=', $user->department->id);

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => [
                [
                    'id' => 100,
                    'permission' => $sharePermissions->random()->name
                ],
                [
                    'id' => 200,
                    'permission' => $sharePermissions->random()->name
                ],
                [
                    'id' => 300,
                    'permission' => $sharePermissions->random()->name
                ],
            ]
        ]);

        $response->assertStatus(403)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_assing_permission_to_users_share_document_error_validation()
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
        $sharePermissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();
        $departments = $departments->where('id', '!=', $user->department->id);

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => [
                [
                    'id' => 100,
                    'permission' => $sharePermissions->random()->name
                ],
                [
                    'id' => 200,
                ],
                [
                    'permission' => $sharePermissions->random()->name
                ],
            ]
        ]);

        $response->assertStatus(422)
        ->assertJson(fn (AssertableJson $json) => 
            $json
            ->has('errors')
            ->where('success', false)
        );
    }

    public function test_assing_permission_zero_to_users_share_document_success()
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
        ->count(5)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();
        $users->each(fn ($user) => $user->assignRole('analyst'));

        $usersRandom = $users->random(2);
        $usersRandom->each(function ($user) use ($document, $sharePermissions) {
            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users", [
            'users' => []
        ]);

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('attached', 0)
                ->has('detached', 2)
                ->has('updated', 0)
            )
            ->has('message')
            ->where('success', true)
        );
    }

    public function test_assing_permission_zero_to_users_share_document_success_without_payload()
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
        ->count(5)
        ->state(new Sequence(
            fn ($sequence) => [
                'department_id' => $departments->random()->id
            ]
        ))
        ->create();
        $users->each(fn ($user) => $user->assignRole('analyst'));

        $usersRandom = $users->random(2);
        $usersRandom->each(function ($user) use ($document, $sharePermissions) {
            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $response = $this->postJson("api/v1/share-documents/{$document->id}/users");

        $response->assertStatus(200)
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', fn ($json) => 
                $json->has('attached', 0)
                ->has('detached', 2)
                ->has('updated', 0)
            )
            ->has('message')
            ->where('success', true)
        );
    }
}
