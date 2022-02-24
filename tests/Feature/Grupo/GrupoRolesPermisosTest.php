<?php

namespace Tests\Feature\Grupo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Spatie\Permission\Models\Role;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrupoRolesPermisosTest extends TestCase
{
    use DatabaseTransactions;

    public function test_grupo_alumno_crud()
    {
      $alumno = Role::where("name","Alumno")->first();
      $this->assertTrue($alumno->hasPermissionTo('grupo.show'));
      $this->assertFalse($alumno->hasPermissionTo('grupo.update'));
      $this->assertFalse($alumno->hasPermissionTo('grupo.create'));
      $this->assertFalse($alumno->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_admin_crud()
    {
      $admin = Role::where("name","Admin")->first();
      $this->assertTrue($admin->hasPermissionTo('grupo.show'));
      $this->assertTrue($admin->hasPermissionTo('grupo.update'));
      $this->assertTrue($admin->hasPermissionTo('grupo.create'));
      $this->assertTrue($admin->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_control_escolar_crud()
    {
      $controlEscolar = Role::where("name","Control escolar")->first();
      $this->assertTrue($controlEscolar->hasPermissionTo('grupo.show'));
      $this->assertTrue($controlEscolar->hasPermissionTo('grupo.update'));
      $this->assertTrue($controlEscolar->hasPermissionTo('grupo.create'));
      $this->assertTrue($controlEscolar->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_departamento_docentes_crud()
    {
      $departamentoDocentes = Role::where("name","Departamento de docentes")->first();
      $this->assertTrue($departamentoDocentes->hasPermissionTo('grupo.show'));
      $this->assertTrue($departamentoDocentes->hasPermissionTo('grupo.update'));
      $this->assertTrue($departamentoDocentes->hasPermissionTo('grupo.create'));
      $this->assertTrue($departamentoDocentes->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_docente_crud()
    {
      $docente = Role::where("name","Docente")->first();
      $this->assertTrue($docente->hasPermissionTo('grupo.show'));
      $this->assertFalse($docente->hasPermissionTo('grupo.update'));
      $this->assertFalse($docente->hasPermissionTo('grupo.create'));
      $this->assertFalse($docente->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_prefecto_crud()
    {
      $prefecto = Role::where("name","Docente")->first();
      $this->assertTrue($prefecto->hasPermissionTo('grupo.show'));
      $this->assertFalse($prefecto->hasPermissionTo('grupo.update'));
      $this->assertFalse($prefecto->hasPermissionTo('grupo.create'));
      $this->assertFalse($prefecto->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_padre_familia_crud()
    {
      $padreFamilia = Role::where("name","Padre de familia")->first();
      $this->assertTrue($padreFamilia->hasPermissionTo('grupo.show'));
      $this->assertFalse($padreFamilia->hasPermissionTo('grupo.update'));
      $this->assertFalse($padreFamilia->hasPermissionTo('grupo.create'));
      $this->assertFalse($padreFamilia->hasPermissionTo('grupo.delete'));
    }

    public function test_grupo_aspirante_crud()
    {
      $aspirante = Role::where("name","Aspirante a ingreso")->first();
      $this->assertFalse($aspirante->hasPermissionTo('grupo.show'));
      $this->assertFalse($aspirante->hasPermissionTo('grupo.update'));
      $this->assertFalse($aspirante->hasPermissionTo('grupo.create'));
      $this->assertFalse($aspirante->hasPermissionTo('grupo.delete'));
    }

}