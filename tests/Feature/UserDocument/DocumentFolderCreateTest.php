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

class DocumentFolderCreateTest extends TestCase
{
  use RefreshDatabase;

  public function test_create_folder_head_success()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      Passport::actingAs($user);
      $this->assertAuthenticated();

      $response = $this->postJson("api/v1/folders", [
          'name' => 'new-folder-space-end',
          'parent' => '',
      ]);

      $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();

      $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('data', fn ($json) => 
              $json->has('id')
              ->has('name')
              ->has('type', fn ($json) => 
                $json->where('id', $typeFolder->id)
                ->where('name', $typeFolder->name)
              )
              ->where('location', 'new-folder-space-end')
              ->has('indentifier')
              ->has('description')
              ->whereType('tags', 'array')
              ->has('createdAt')
              ->has('creator', fn($json) => 
                  $json->has('id')
                  ->has('email')
                  ->has('name')
                  ->has('lastname')
                  ->has('second_lastname')
                  ->has('role')
                  ->etc()
              )
              ->whereType('parent', 'null')
              ->etc()
          )
          ->has('message')
          ->where('success', true)
          ->etc()
      );
  }

  public function test_create_folder_head_success_without_parent()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      Passport::actingAs($user);
      $this->assertAuthenticated();

      $response = $this->postJson("api/v1/folders", [
          'name' => 'new-folder-space-end',
      ]);

      $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();
      $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('data', fn ($json) => 
              $json->has('id')
              ->has('name')
              ->has('type', fn ($json) => 
                $json->where('id', $typeFolder->id)
                ->where('name', $typeFolder->name)
              )
              ->where('location', 'new-folder-space-end')
              ->has('indentifier')
              ->has('description')
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
              ->whereType('parent', 'null')
              ->etc()
          )
          ->has('message')
          ->where('success', true)
          ->etc()
      );
  }

  public function test_create_folder_head_success_with_parent()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();

      $location = '';
      //level root
      $folder = Document::factory()
      ->for($typeFolder, 'type')
      ->for($user->department)
      ->for($user, 'creator')
      ->create();
      $location .= $folder->name . '/';

      Passport::actingAs($user);
      $this->assertAuthenticated();

      $response = $this->postJson("api/v1/folders", [
          'name' => 'new-folder-space-end',
          'parent_id' => $folder->id,
      ]);

      $location .= 'new-folder-space-end';

      $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('data', fn ($json) => 
              $json->has('id')
              ->has('name')
              ->has('type', fn ($json) => 
                $json->where('id', $typeFolder->id)
                ->where('name', $typeFolder->name)
              )
              ->where('location', $location)
              ->has('indentifier')
              ->has('description')
              ->whereType('tags', 'array')
              ->has('createdAt')
              ->has('creator', fn($json) => 
                  $json->has('id')
                  ->has('email')
                  ->has('name')
                  ->has('lastname')
                  ->has('second_lastname')
                  ->has('role')
                  ->etc()
              )
              ->has('parent', fn ($json) => 
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

  public function test_create_folder_head_success_with_parent_level_3()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();

      $location = '';
      //level root
      $folderRoot = Document::factory()
      ->for($typeFolder, 'type')
      ->for($user->department)
      ->for($user, 'creator')
      ->create();
      $location .= $folderRoot->name . '/';
      //second level
      $folderSecondLevel = Document::factory()
      ->for($typeFolder, 'type')
      ->for($user->department)
      ->for($user, 'creator')
      ->for($folderRoot, 'parent')
      ->create();
      $location .= $folderSecondLevel->name . '/';
      //second level
      $folderThreeLevel = Document::factory()
      ->for($typeFolder, 'type')
      ->for($user->department)
      ->for($user, 'creator')
      ->for($folderSecondLevel, 'parent')
      ->create();
      $location .= $folderThreeLevel->name . '/';

      Passport::actingAs($user);
      $this->assertAuthenticated();
      
      $newFolderName = 'new-folder';
      $response = $this->postJson("api/v1/folders", [
          'name' => $newFolderName,
          'parent_id' => $folderThreeLevel->id,
      ]);

      $location .= $newFolderName;

      $response->assertStatus(201)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('data', fn ($json) => 
              $json->has('id')
              ->has('name')
              ->has('type', fn ($json) => 
                $json->where('id', $typeFolder->id)
                ->where('name', $typeFolder->name)
              )
              ->where('location', $location)
              ->has('indentifier')
              ->has('description')
              ->whereType('tags', 'array')
              ->has('createdAt')
              ->has('creator', fn($json) => 
                  $json->has('id')
                  ->has('email')
                  ->has('name')
                  ->has('lastname')
                  ->has('second_lastname')
                  ->has('role')
                  ->etc()
              )
              ->has('parent', fn ($json) => 
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

  public function test_create_folder_head_error_name_empty()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      Passport::actingAs($user);
      $this->assertAuthenticated();
      
      $response = $this->postJson("api/v1/folders", [
          'name' => '',
      ]);

      $response->assertStatus(422)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('errors')
          ->where('success', false)
          ->etc()
      );
  }

  public function test_create_folder_head_error_name_characters()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      Passport::actingAs($user);
      $this->assertAuthenticated();
      
      $response = $this->postJson("api/v1/folders", [
          'name' => 'space in folder name',
      ]);

      $response->assertStatus(422)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('errors')
          ->where('success', false)
          ->etc()
      );
  }

  public function test_create_folder_head_error_parent_not_found()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      Passport::actingAs($user);
      $this->assertAuthenticated();

      $limitId = (Document::count() + 1);
      $response = $this->postJson("api/v1/folders", [
          'name' => 'new-folder',
          'parent_id' => $limitId
      ]);

      $response->assertStatus(404)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('errors')
          ->where('success', false)
          ->etc()
      );
  }

  public function test_create_folder_head_error_parent_string()
  {
      $this->seed();

      $deparment = Department::all()->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('Head of department');

      Passport::actingAs($user);
      $this->assertAuthenticated();

      $limitId = (Document::count() + 1);
      $response = $this->postJson("api/v1/folders", [
          'name' => 'new-folder',
          'parent_id' => 'value_error'
      ]);

      $response->assertStatus(422)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('errors')
          ->where('success', false)
          ->etc()
      );
  }

  public function test_create_folder_analyst_error_policy()
  {
      $this->seed();

      $deparments = Department::all();
      $deparment = $deparments->random();
      $user = User::factory()
          ->for($deparment)
      ->create();
      $user->assignRole('analyst');

      Passport::actingAs($user);
      $this->assertAuthenticated();

      $response = $this->postJson("api/v1/folders", [
          'name' => 'new-folder',
      ]);

      $response->assertStatus(403)
      ->assertJson(fn (AssertableJson $json) => 
          $json->has('errors')
          ->where('success', false)
          ->etc()
      );
  }

}