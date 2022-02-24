<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
class BugRolesDocenteDepartamentoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_permisos_docente_grupo ()
    {
        $docente = User::where('email', 'docente@puller.mx')->first();

        $roles = $docente->getRoleNames();
        $roles = $roles->toArray();

        $this->assertContains('Docente', $roles);
        $this->assertNotContains('Departamento de docentes', $roles);

        $permisos = $docente->getAllPermissions();
        $permisos = $permisos->pluck('name');

        $this->assertContains('grupo.show', $permisos);
        $this->assertNotContains('grupo.update', $permisos);
        $this->assertNotContains('grupo.create', $permisos);
        $this->assertNotContains('grupo.delete', $permisos);
    }

    public function test_permisos_departamento_docentes_grupo ()
    {
        $user = User::where('email', 'departamento_docentes@puller.mx')->first();

        $roles = $user->getRoleNames();
        $roles = $roles->toArray();

        $this->assertContains('Departamento de docentes', $roles);
        $this->assertNotContains('Docente', $roles);

        $permisos = $user->getAllPermissions();
        $permisos = $permisos->pluck('name');

        $this->assertContains('grupo.show', $permisos);
        $this->assertContains('grupo.create', $permisos);
        $this->assertContains('grupo.update', $permisos);
        $this->assertContains('grupo.delete', $permisos);
    }

}
