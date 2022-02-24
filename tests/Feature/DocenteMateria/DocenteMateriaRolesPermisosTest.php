<?php

namespace Tests\Feature\DocenteMateria;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Spatie\Permission\Models\Role;

class DocenteMateriaRolesPermisosTest extends TestCase
{
    use DatabaseTransactions;

    public function test_docentemateria_role_crud()
    {
        $user = User::factory()->create();
        $user->assignRole('Admin');
        $this->assertTrue($user->can('usuario.create'));

        $user = User::factory()->create();
        $user->assignRole('Departamento de docentes');
        $this->assertTrue($user->can('usuario.create'));

        $user = User::factory()->create();
        $user->assignRole('Control Escolar');
        $this->assertTrue($user->can('usuario.create'));

        $user = User::factory()->create();
        $user->assignRole('Docente');
        $this->assertTrue($user->can('usuario.create'));

        $user = User::factory()->create();
        $user->assignRole('Prefecto');
        $this->assertFalse($user->can('usuario.create'));

        $user = User::factory()->create();
        $user->assignRole('Padre de familia');
        $this->assertFalse($user->can('usuario.create'));

        $user = User::factory()->create();
        $user->assignRole('Alumno');
        $this->assertFalse($user->can('baja_tipo.create'));
    }
}