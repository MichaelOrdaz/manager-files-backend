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
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserDocumentShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_first_level_documents_deparment_head_success()
    {
        $this->seed();

        $departments = Department::all();
        $deparment = $departments->random();

        $user = User::factory()
            ->for($deparment)
        ->create();
        $user->assignRole('Head of department');

        Passport::actingAs($user);

        $documentType = DocumentType::all();
        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->state(fn (array $attr) => [
            'location' => $attr['name']
        ])
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

        $users->each(function ($user) use ($document, $sharePermissions) {
            $user->assignRole('analyst');

            $user->share()->attach($document, [
                'permission' => $sharePermissions->random()->name,
                'granted_by' => $user->id,
            ]);
        });

        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/documents/{$document->id}");

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

        $userAnalyst = User::factory()
            ->for($deparment)
        ->create();
        $userAnalyst->assignRole('Head of department');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $documents = Document::factory()->count(3)
        ->state(new Sequence(
            fn ($sequence) => [
                'type_id' => $documentType->random()->id
            ]
        ))
        ->for($user->department)
        ->for($user, 'creator')
        ->create();

        Passport::actingAs($userAnalyst);
        $this->assertAuthenticated();
        $document = $documents->random();
        $response = $this->getJson("api/v1/documents/{$document->id}");

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
