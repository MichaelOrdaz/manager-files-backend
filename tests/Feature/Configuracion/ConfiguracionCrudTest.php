<?php

namespace Tests\Feature\Configuracion;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class ConfiguracionCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_configuracion_crud_create()
    {
      $user = User::factory()->create();
      $user->assignRole('Control escolar');
      $this->actingAs($user, 'api');

      $file = UploadedFile::fake()->image('logo.png')->size(10);

      $response = $this->post("api/v1/configuraciones",[
        'nombre' => 'nombre del logo',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'tipo' => 'image',
        'activo' => '1',
        'archivo' => $file,
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]
      ]);
    }

    public function test_configuracion_crud_list()
    {
      $user = User::factory()->create();
      $user->assignRole('Control escolar');
      $this->actingAs($user, 'api');

      Configuracion::factory()->create([
        'activo' => 1
      ]);

      $response = $this->get("api/v1/configuraciones");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]]
      ]);
    }

    public function test_configuracion_crud_list_docente()
    {
      $user = User::factory()->create();
      $user->assignRole('Docente');
      $this->actingAs($user, 'api');

      Configuracion::factory()->create([
        'activo' => 1
      ]);

      $response = $this->get("api/v1/configuraciones");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]]
      ]);
    }

    public function test_configuracion_crud_list_prefecto()
    {
      $user = User::factory()->create();
      $user->assignRole('Prefecto');
      $this->actingAs($user, 'api');

      Configuracion::factory()->create([
        'activo' => 1
      ]);

      $response = $this->get("api/v1/configuraciones");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]]
      ]);
    }

    public function test_configuracion_crud_list_guest()
    {
      Configuracion::factory()->count(3)->create([
        'activo' => 1
      ]);

      $response = $this->get("api/v1/configuraciones");
      
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [[
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]]
      ]);
    }

    public function test_configuracion_crud_get()
    {
      $user = User::factory()->create();
      $user->assignRole('Control escolar');
      $this->actingAs($user, 'api');

      $configuracion = Configuracion::factory()->create([
        'activo' => 1
      ]);

      $response = $this->get("api/v1/configuraciones/{$configuracion->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]
      ]);
    }

    public function test_configuracion_crud_get_guest()
    {
      $configuracion = Configuracion::factory()->create([
        'activo' => 1
      ]);

      $response = $this->get("api/v1/configuraciones/{$configuracion->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]
      ]);
    }

    public function test_configuracion_crud_update()
    {
      $user = User::factory()->create();
      $user->assignRole('Control escolar');
      $this->actingAs($user, 'api');

      $configuracion = Configuracion::factory()->create();

      $file = UploadedFile::fake()->image('avatar.png')->size(10);

      $response = $this->post("api/v1/configuraciones/{$configuracion->id}",[
        'nombre' => 'nuevo titulo',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'tipo' => 'image',
        'activo' => '1',
        'archivo' => $file,
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]
      ]);
      $response->assertSee('public\/Configuraciones');
      $configuracion->refresh();
      $this->assertEquals($configuracion->nombre, "nuevo titulo");
    }

    public function test_configuracion_crud_update_sin_foto()
    {
      $user = User::factory()->create();
      $user->assignRole('Control escolar');
      $this->actingAs($user, 'api');

      $configuracion = Configuracion::factory()->create([
        'valor_default' => 'public/Configuraciones/logo.png'
      ]);

      $response = $this->post("api/v1/configuraciones/{$configuracion->id}",[
        'nombre' => 'nuevo titulo',
        'descripcion' => 'Lorem ipsum dolor sit amet.',
        'tipo' => 'image',
        'activo' => '1',
        'archivo' => '',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]
      ]);
      $response->assertSee(str_replace('/', '\/', $configuracion->valor_default));
      $configuracion->refresh();
      $this->assertEquals($configuracion->nombre, "nuevo titulo");
      $this->assertEquals($configuracion->valor, '');
    }

    public function test_configuracion_crud_delete()
    {
      $user = User::factory()->create();
      $user->assignRole('Control escolar');
      $this->actingAs($user, 'api');

      $configuracion = Configuracion::factory()->create();

      $response = $this->delete("api/v1/configuraciones/{$configuracion->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          'id',
          'nombre',
          'descripcion',
          'tipo',
          'url',
        ]
      ]);
      $this->assertSoftDeleted($configuracion);
    }
}