<?php

namespace Tests\Feature\SharedDocuments;

use App\Helpers\Dixa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SharedPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_shared_documents_success()
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('Admin');

        Passport::actingAs($user);
        $this->assertAuthenticated();

        $response = $this->getJson("api/v1/share-permissions");

        $total = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->count();
        $response->assertOk()
        ->assertJson(fn (AssertableJson $json) => 
            $json->has('data', $total, fn ($json) => 
                $json->has('name')
            )
            ->has('message')
            ->where('success', true)
        );
    }
}
