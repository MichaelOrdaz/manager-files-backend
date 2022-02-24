<?php

namespace Tests\Feature\DatosGenerales;

use App\Models\AspiranteStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\DatosGenerales;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class DatosGeneralesCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_datosgenerales_crud_create()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create();

      $foto = UploadedFile::fake()->image('avatar.jpg');
      $ine = UploadedFile::fake()->image('ine.jpg');

      $response = $this->post("api/v1/usuarios/{$user->id}/datos-generales",[
        'municipio_origen_id' => $municipio->id,
        'estado_origen_id' => $estado->id,
        'nombre' => 'Juanito',
        'apellido_paterno' => 'Perez',
        'apellido_materno' => 'Gonzales',
        'curp' => 'NLAOQQME12389791q',
        'semblanza' => 'Lorem ipsum dolor sit amet consectetur.',
        'telefono_fijo' => '1247896585',
        'telefono_celular' => '1247896585',
        'imagen' => $foto,
        'ine' => $ine,
        'edad' => '30',
        'sexo' => 'M',
        'fecha_nacimiento' => '1990-05-14',
        'promedio_secundaria' => '8.5',
        'calle' => '2 poniente',
        'numero_exterior' => '200',
        'numero_interior' => '3',
        'colonia' => 'Centro',
        'localidad' => 'Puebla',
        'activo' => '1',
      ]);

      $response->assertStatus(201)
      ->assertJsonStructure([
        'data' => [
            "usuario",
            "usuario_id",
            "imagen_url",
            "ine_url",
            "nombre",
            "apellido_materno",
            "apellido_paterno",
            "semblanza",
            "curp",
            "municipio",
            "estado",
            "telefono_fijo",
            "telefono_celular",
            "edad",
            "sexo",
            "fecha_nacimiento",
            "hermanos",
            "hermanas",
            "promedio_secundaria",
            "calle",
            "numero_ext",
            "numero_int",
            "colonia",
            "localidad",
        ]
      ]);
    }

    public function test_datosgenerales_crud_get()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create();

      $datos = DatosGenerales::factory()
      ->for($user, 'usuario')
      ->for($estado)
      ->for($municipio)
      ->create();

      $response = $this->get("api/v1/usuarios/{$user->id}/datos-generales/{$datos->id}");
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "usuario",
            "usuario_id",
            "imagen_url",
            "ine_url",
            "nombre",
            "apellido_materno",
            "apellido_paterno",
            "semblanza",
            "curp",
            "municipio",
            "estado",
            "telefono_fijo",
            "telefono_celular",
            "edad",
            "sexo",
            "fecha_nacimiento",
            "hermanos",
            "hermanas",
            "promedio_secundaria",
            "calle",
            "numero_ext",
            "numero_int",
            "colonia",
            "localidad",
        ]
      ]);
    }

    public function test_datosgenerales_crud_update()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create();

      $datos = DatosGenerales::factory()
      ->for($user, 'usuario')
      ->for($estado)
      ->for($municipio)
      ->create();

      $foto = UploadedFile::fake()->image('avatar2.jpg');
      $ine = UploadedFile::fake()->image('ine2.jpg');

      $response = $this->post("api/v1/usuarios/{$user->id}/datos-generales/{$datos->id}",[
        'municipio_origen_id' => $municipio->id,
        'estado_origen_id' => $estado->id,
        'nombre' => 'Juan',
        'apellido_paterno' => 'Perez',
        'apellido_materno' => 'Gonzales',
        'curp' => 'NLAOQQME12389791q',
        'semblanza' => 'Lorem ipsum dolor sit amet consectetur.',
        'telefono_fijo' => '1247896585',
        'telefono_celular' => '1247896585',
        'imagen' => $foto,
        'ine' => $ine,
        'edad' => '30',
        'sexo' => 'M',
        'fecha_nacimiento' => '1990-05-15',
        'promedio_secundaria' => '8.5',
        'calle' => '2 poniente',
        'numero_exterior' => '200',
        'numero_interior' => '3',
        'colonia' => 'Centro',
        'localidad' => 'Puebla',
        'activo' => '1',
      ]);

      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "usuario",
            "usuario_id",
            "imagen_url",
            "ine_url",
            "nombre",
            "apellido_materno",
            "apellido_paterno",
            "semblanza",
            "curp",
            "municipio",
            "estado",
            "telefono_fijo",
            "telefono_celular",
            "edad",
            "sexo",
            "fecha_nacimiento",
            "hermanos",
            "hermanas",
            "promedio_secundaria",
            "calle",
            "numero_ext",
            "numero_int",
            "colonia",
            "localidad",
        ]
      ]);

      $datos->refresh();
      $this->assertEquals($datos->nombre, 'Juan');
      $this->assertEquals($datos->localidad, 'Puebla');
    }

    public function test_datosgenerales_consulta_crud_get()
    {
      $this->withoutExceptionHandling();
      $user = User::factory()->create();
      $user->assignRole('Admin');
      $this->actingAs($user, 'api');

      $estado = Estado::factory()->create();
      $municipio = Municipio::factory()->for($estado)->create();

      $datos = DatosGenerales::factory()
      ->for($user, 'usuario')
      ->for($estado)
      ->for($municipio)
      ->create();

      $response = $this->get("api/v1/aspirantes/*/". $datos->curp );
      $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          "curp_existe",
        ]
      ]);
    }

}