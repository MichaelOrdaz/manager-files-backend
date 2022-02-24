<?php

namespace Tests\Feature\Tutoria;

use Tests\TestCase;
use App\Models\Tema;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Unidad;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Tutoria;
use App\Models\Componente;
use App\Models\Especialidad;
use App\Models\EspecialidadPeriodo;
use App\Models\MaterialDidactico;
use App\Models\MaterialTipo;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TutoriaCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected $tutoria,$tema,$materia,$grupo,$material;

    public function test_tutoria_crud_create()
    {
      $file = UploadedFile::fake()->image('avatar.jpg');
      $response = $this->post('api/v1/tutorias', [
        'pregunta' => 'Pregunta 1',
        'descripcion' => 'descripción 1',
        'material_id' => $this->material->id,
        'tema_id' => $this->tema->id,
        'materia_id' => $this->materia->id,
        'imagenes' => [ $file ],
        'grupo_id' => $this->grupo->id,
      ]);
      $response->assertStatus(201);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'pregunta',
          'descripcion',
          'respuesta_tutoria_id',
          'usuario_id',
          'usuario',
          'material_id',
          'material',
          'tema_id',
          'tema',
          'materia_id',
          'materia',
          'grupo_id',
          'grupo',
          'imagenes',
          'respuesta_tutorias',
          'activo',
          'created_at',
        ]
      ]);
    }


    public function test_tutoria_crud_update()
    {
      $file = UploadedFile::fake()->image('avatar.jpg');
      $response = $this->post('api/v1/tutorias/'.$this->tutoria->id, [
        'pregunta' => 'Pregunta 1',
        'material_id' => $this->material->id,
        'tema_id' => $this->tema->id,
        'materia_id' => $this->materia->id,
        'imagenes' => [ $file ],
        'grupo_id' => $this->grupo->id,
      ]);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'pregunta',
          'descripcion',
          'respuesta_tutoria_id',
          'usuario_id',
          'usuario',
          'material_id',
          'material',
          'tema_id',
          'tema',
          'materia_id',
          'materia',
          'grupo_id',
          'grupo',
          'imagenes',
          'respuesta_tutorias',
          'activo',
          'created_at',
        ]
      ]);
    }

    public function test_tutoria_crud_list()
    {
      $response = $this->get('api/v1/tutorias');
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'pregunta',
            'descripcion',
            'respuesta_tutoria_id',
            'usuario_id',
            'usuario',
            'material_id',
            'material',
            'tema_id',
            'tema',
            'materia_id',
            'materia',
            'grupo_id',
            'grupo',
            'imagenes',
            'respuesta_tutorias',
            'activo',
            'created_at',
          ]
        ]
      ]);
    }

    public function test_tutoria_crud_get()
    {
      $response = $this->get('api/v1/tutorias/'.$this->tutoria->id);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'pregunta',
          'descripcion',
          'respuesta_tutoria_id',
          'usuario_id',
          'usuario',
          'material_id',
          'material',
          'tema_id',
          'tema',
          'materia_id',
          'materia',
          'grupo_id',
          'grupo',
          'imagenes',
          'respuesta_tutorias',
          'activo',
          'created_at',
        ]
      ]);
    }

    public function test_tutoria_crud_delete()
    {
      $respuestas = $this->tutoria->RespuestasTutoria;
      $response = $this->delete('api/v1/tutorias/'.$this->tutoria->id);
      $response->assertStatus(200);
      $response->assertJsonStructure([
        'data' => [
          'id',
          'pregunta',
          'descripcion',
          'respuesta_tutoria_id',
          'usuario_id',
          'usuario',
          'material_id',
          'material',
          'tema_id',
          'tema',
          'materia_id',
          'materia',
          'grupo_id',
          'grupo',
          'imagenes',
          'respuesta_tutorias',
          'activo',
          'created_at',
        ]
      ]);
      $this->assertSoftDeleted($this->tutoria);
      foreach ($respuestas as $respuesta) {
        $this->assertSoftDeleted($respuesta);
      }
    }


    public function test_tutoria_search()
    {
      $response = $this->get(
        'api/v1/tutorias:search?' .
        'materiaId=' . $this->materia->id .
        '&grupoId=' . $this->grupo->id .
        '&temaId=' .$this->tema->id
      );
      $response->assertStatus(200);

      $response->assertJsonStructure([
        'data' => [
          [
            'id',
            'pregunta',
            'descripcion',
            'respuesta_tutoria_id',
            'usuario_id',
            'usuario',
            'material_id',
            'material',
            'tema_id',
            'tema',
            'materia_id',
            'materia',
            'grupo_id',
            'grupo',
            'imagenes',
            'respuesta_tutorias',
            'activo',
            'created_at',
          ]
        ]
      ]);
    }

    public function setUp():void
    {
      parent::setUp();
      $user = User::factory()->create();
      $user->assignRole('Alumno');
      $this->actingAs($user, 'api');

      $this->materia = Materia::factory()
      ->for(Componente::factory())
      ->for(
        EspecialidadPeriodo::factory()
        ->for(Periodo::factory())
        ->for(Especialidad::factory()),
        'especialidad_periodo'
      )
      ->create();

      $this->tema = Tema::factory()
        ->for(Unidad::factory()->for($this->materia))
        ->create();

      $this->grupo = Grupo::factory()->create();

      $this->material = MaterialDidactico::factory()
        ->for($user, 'creador')
        ->for(MaterialTipo::all()->random(),'MaterialesTipo')
        ->for($this->tema, 'tema')->create();
      $this->tutoria = Tutoria::factory()
        ->for(User::factory())
        ->for($this->material, 'material')
        ->for($this->materia)
        ->for($this->grupo)
        ->for($this->tema, 'tema')
        ->create();


      Tutoria::factory()
        ->for(User::factory())
        ->for($this->material, 'material')
        ->for($this->tema, 'tema')
        ->for($this->materia)
        ->for($this->grupo)
        ->count(4)
        ->create(['respuesta_tutoria_id' => $this->tutoria->id]);

    }
}
