<?php
/**
 * @author Enrique Sevilla <sevilla@puller.mx>
 * @version  1.0
 * Test para endpoint aspirantes, prueba el registro de usuarios
 * aspirantes creando sus datos académicos
 *
 * NOTAS :
 * con assertJsonStructure pueden traer mas datos pero no menos
 *
 * si se necesita ver los errores sin el handler agregar:
 *  $this->withoutExceptionHandling();
 * en la primera linea de tu función
 *
 * Sirve para hacer debug cuando se hace una petición al api
 * $response->dump();
 */

namespace Tests\Feature\Aspirantes;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Grupo;
use App\Models\Periodo;
use App\Models\User;
use App\Models\Conferencia;
use App\Models\DatosAcademicos;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AspirantesCrudTest extends TestCase
{
  use DatabaseTransactions;

  public function test_aspirante_crud_create()
  {
    $response = $this->post("/api/v1/aspirantes",[
      "email" => "adminTest@puller.mx",
      "password" => Hash::make("12345"),
      "role" => "Aspirante a ingreso",
      "activo" => true,
    ]);

    $response->assertStatus(201)
    ->assertJsonStructure([
      "data" => [
        "id",
        "email",
        "email_verified_at",
        "firebase_uid",
        "datos_generales",
        "datos_familiares",
      ]
    ]);

    $response->assertJsonStructure([
      "data" => [
        "datos_academicos" => [
          "id",
          "status_id",
          "matricula",
          "generacion",
          "usuario_id",
          "baja_id",
          "fecha_baja",
          "status",
        ]
      ]
    ]);

  }

}
