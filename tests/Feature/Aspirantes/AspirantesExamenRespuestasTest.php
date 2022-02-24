<?php
/**
 * @author Enrique Sevilla <sevilla@puller.mx>
 * @version  1.0
 * Test para endpoint aspirantes, prueba el flujo de cuando un aspirante
 * realiza los exámenes psicológico y psicótico para el ingreso al instituto
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

use App\Models\User;
use App\Models\Examen;
use App\Models\ExamenPregunta;
use App\Models\DatosAcademicos;

use App\Models\ExamenRespuesta;
use App\Models\ExamenCalificacion;
use Database\Seeders\TestsSeeders;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AspirantesExamenRespuestasTest extends TestCase
{
    use DatabaseTransactions;

    protected $examenPsicologico;
    protected $examenPsicometrico;
    protected $usuario;

    protected $respuestasCorrectas;
    protected $respuestasErroneas;

    public function test_aspirante_examen_no_completado()
    {
      $pregunta= ExamenPregunta::where("examen_id",$this->examenPsicologico->id)->first();
      $response = $this->post(
        "api/v1/aspirantes/".$this->usuario->id.
        "/examenes/".$this->examenPsicologico->id.
        "/examenes-preguntas/".$pregunta->id.
        "/examenes-respuestas",
        ["respuesta" => "Opción 1"]
      );

      $response->assertStatus(200)
      ->assertJsonStructure([
        "data" => [
          "id",
          "usuario_id",
          "usuario",
          "pregunta_id",
          "pregunta",
          "examen_id",
          "examen",
          "respuesta",
          "calificacion",
          "aprobado",
          "activo",
        ]
      ]);

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicologico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'En proceso');
    }

    public function test_aspirante_examen_psicologico_aprobado()
    {
      $preguntas = ExamenPregunta::where("examen_id",$this->examenPsicologico->id)->get();
      $this->assertCount(5,$preguntas->toArray());

      foreach ($preguntas as $pregunta) {
        $response = $this->post(
          "api/v1/aspirantes/".$this->usuario->id.
          "/examenes/".$this->examenPsicologico->id.
          "/examenes-preguntas/".$pregunta->id.
          "/examenes-respuestas",
          ["respuesta" => $this->respuestasCorrectas[$pregunta->tipo_id], ]
        );
        $response->assertStatus(200);
      }
      $this->assertTrue($response!=Null);
      $response->assertJsonFragment(["aprobado" => true]);
      $dato = DatosAcademicos::where("usuario_id",$this->usuario->id)->first();
      $this->assertEquals(optional($dato->status)->nombre,"No calificado");

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicologico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'En proceso');
    }

    public function test_aspirante_examen_psicologico_reprobado()
    {
      $preguntas = ExamenPregunta::where("examen_id",$this->examenPsicologico->id)->get();
      $this->assertTrue(count($preguntas)>0);

      foreach ($preguntas as $pregunta) {
        $response = $this->post(
          "api/v1/aspirantes/".$this->usuario->id.
          "/examenes/".$this->examenPsicologico->id.
          "/examenes-preguntas/".$pregunta->id.
          "/examenes-respuestas",
          ["respuesta" => $this->respuestasErroneas[$pregunta->tipo_id], ]
        );
        $response->assertStatus(200);
      }
      $this->assertTrue($response!=Null);
      $response->assertJsonFragment(['aprobado' => false]);
      $datos = DatosAcademicos::where("usuario_id",$this->usuario->id)->first();
      $this->assertEquals(optional($datos->status)->nombre,"Rechazado");

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicologico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'Reprobado');
    }

    public function test_aspirante_examen_psicometrico_aprobado()
    {
      $preguntas = ExamenPregunta::where("examen_id",$this->examenPsicologico->id)->get();
      foreach ($preguntas as $pregunta) {
        $response = $this->post(
          "api/v1/aspirantes/".$this->usuario->id.
          "/examenes/".$this->examenPsicologico->id.
          "/examenes-preguntas/".$pregunta->id.
          "/examenes-respuestas",
          ["respuesta" => $this->respuestasCorrectas[$pregunta->tipo_id], ]
        );
        $response->assertStatus(200);
      }
      $response->assertJsonFragment(['aprobado' => true]);

      $preguntas = ExamenPregunta::where("examen_id",$this->examenPsicometrico->id)->get();

      $this->assertFalse($this->examenPsicologico->id == $this->examenPsicometrico->id);
      foreach ($preguntas as $pregunta) {
        $response = $this->post(
          "api/v1/aspirantes/".$this->usuario->id.
          "/examenes/".$this->examenPsicometrico->id.
          "/examenes-preguntas/".$pregunta->id.
          "/examenes-respuestas",
          ["respuesta" => $this->respuestasCorrectas[$pregunta->tipo_id], ]
        );
        $response->assertStatus(200);
      }
      $response->assertJsonFragment(['aprobado' => true]);

      $dato = DatosAcademicos::where("usuario_id",$this->usuario->id)->first();
      $this->assertEquals(optional($dato->status)->nombre,"No calificado");

      $examanesContestados = ExamenRespuesta::select('usuario_id','examen_id')
            ->where('usuario_id',$this->usuario->id)
            ->select('examen_id')
            ->distinct('examen_id')
            ->count();
      $this->assertTrue(2==$examanesContestados);

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicologico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'En proceso');

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicometrico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'En proceso');
    }

    public function test_aspirante_examen_psicometrico_reprobado()
    {
      $preguntas = ExamenPregunta::where("examen_id",$this->examenPsicologico->id)->get();
      foreach ($preguntas as $pregunta) {
        $response = $this->post(
          "api/v1/aspirantes/".$this->usuario->id.
          "/examenes/".$this->examenPsicologico->id.
          "/examenes-preguntas/".$pregunta->id.
          "/examenes-respuestas",
          ["respuesta" => $this->respuestasCorrectas[$pregunta->tipo_id], ]
        );
        $response->assertStatus(200);
      }
      $response->assertJsonFragment(['aprobado' => true]);

      $preguntas = ExamenPregunta::where("examen_id",$this->examenPsicometrico->id)->get();
      foreach ($preguntas as $pregunta) {
        $response = $this->post(
          "api/v1/aspirantes/".$this->usuario->id.
          "/examenes/".$this->examenPsicometrico->id.
          "/examenes-preguntas/".$pregunta->id.
          "/examenes-respuestas",
          ["respuesta" => $this->respuestasErroneas[$pregunta->tipo_id], ]
        );
        $response->assertStatus(200);
      }
      $this->assertTrue($response!=Null);

      $response->assertJsonFragment(['aprobado' => false]);
      $datos = DatosAcademicos::where("usuario_id",$this->usuario->id)->first();
      $this->assertEquals(optional($datos->status)->nombre,"Rechazado");

      $examanesContestados = ExamenRespuesta::select('usuario_id','examen_id')
            ->where('usuario_id',$this->usuario->id)
            ->select('examen_id')
            ->distinct('examen_id')
            ->count();
      $this->assertTrue(2==$examanesContestados);

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicologico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'En proceso');

      $examenCalificacion =ExamenCalificacion::where([
        'usuario_id' => $this->usuario->id,
        'examen_id' => $this->examenPsicometrico->id,
      ])->first();
      $this->assertEquals(optional($examenCalificacion->status)->nombre,'Reprobado');
    }

    public function setUp():void
    {
      parent::setUp();
      $this->usuario = User::factory()->has(DatosAcademicos::factory())->create();
      $this->usuario->assignRole("Aspirante a ingreso");
      //uso ese usuario como el usuario autenticado en cada petición
      $this->actingAs($this->usuario, "api");

      $this->examenPsicologico = Examen::factory()->forUser()->forExamenTipo()->create([
          "nombre" => "test-puller Examen psicológico",
          "puntaje_minimo" => 150,
        ]);

      $this->examenPsicometrico = Examen::factory()->forUser()->forExamenTipo()->create([
          "nombre" => "test-puller Examen psicometrico",
          "puntaje_minimo" => 150,
        ]);

      $this->examenPsicologico->examenPreguntas()->createMany($this->questionsForExamn());
      $this->examenPsicometrico->examenPreguntas()->createMany($this->questionsForExamn());

      $this->respuestasCorrectas = $this->obtener_respuesta_correctas();
      $this->respuestasErroneas = $this->obtener_respuestas_erroneas();
    }

    public function obtener_respuesta_correctas()
    {
      $preguntaTipo = config("constantes.TIPO_PREGUNTA");
      return  [
        $preguntaTipo["PREGUNTA_ABIERTA"] => "Lorem ipsum dolor",
        $preguntaTipo["PREGUNTA_VERDADERO_FALSO"] => "true",
        $preguntaTipo["PREGUNTA_OPCION_MULTIPLE"] => ["Atracar establecimientos"],
        $preguntaTipo["PREGUNTA_COLUMNAS"] =>
        [
          ["columna_a" => "Articulo 3","columna_b" => "Escuela pública y gratuita"],
          ["columna_a" => "Día de muerte de Lovecraft","columna_b" => "15 de Marzo"],
          ["columna_a" => "Colores del arcoíris","columna_b" => "7"],
        ],
        $preguntaTipo["PREGUNTA_ESCALA"] => 5,
      ];
    }

    public function obtener_respuestas_erroneas()
    {
      $preguntaTipo = config("constantes.TIPO_PREGUNTA");
      return  [
        $preguntaTipo["PREGUNTA_ABIERTA"] => "Lorem ipsum dolor",
        $preguntaTipo["PREGUNTA_VERDADERO_FALSO"] => "false",
        $preguntaTipo["PREGUNTA_OPCION_MULTIPLE"] => ["false"],
        $preguntaTipo["PREGUNTA_COLUMNAS"] =>[ ["columna_a" => "false","columna_b" => "false"],],
        $preguntaTipo["PREGUNTA_ESCALA"] => 0,
      ];
    }

    private function questionsForExamn()
    {
      return [
        [
          "usuario_id" => 1,
          "tipo_id" => 1,
          "pregunta" => "¿Qué es la ética?",
          "valor" => 50,
          "activo" => 1,
        ],
        [
          "usuario_id" => 1,
          "tipo_id" => 2,
          "pregunta" => "¿Un policía debe ser malo?",
          'respuestas' => json_encode([
            "respuesta" => true,
          ]),
          "valor" => 50,
          "activo" => 1,
        ],
        [
          "usuario_id" => 1,
          "tipo_id" => 3,
          "pregunta" => "¿Qué no debe hacer un policía?",
          "valor" => 50,
          "respuestas" => json_encode([
            "lista" => [
                ["texto" => "Estar alerta"],
                ["texto" => "Drogarse"],
                ["texto" => "Atracar establecimientos"],
                ["texto" => "Ser moralmente correcto"],
            ],
            "respuestas" => [
              "Atracar establecimientos"
            ],
          ]),
          "activo" => 1,
        ],
        [
          "usuario_id" => 1,
          "tipo_id" => 4,
          "pregunta" => "Relaciona las columnas",
          "respuestas" => '{
          "0": {"columna_a": "Articulo 3","columna_b": "Escuela pública y gratuita"},
          "1": {"columna_a": "Día de muerte de Lovecraft","columna_b": "15 de Marzo"},
          "2": {"columna_a": "Colores del arcoíris","columna_b": "7"}
          }',
          "valor" => 50,
        ],
        [
          "usuario_id" => 1,
          "tipo_id" => 5,
          "pregunta" => "Relaciona las columnas",
          "respuestas" => json_encode([
            "escala" => 10,
            "maximo" => "mucho",
            "minimo" => "poco "
          ]),
          "valor" => 50,
        ],
      ];
    }
}