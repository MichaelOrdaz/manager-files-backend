<?php

namespace Database\Seeders;

use Database\Seeders\Permissions\ActividadPermissionsSeeder;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Database\Seeders\Permissions\EstadoPermissionsSeeder;
use Database\Seeders\Permissions\ExamenTipoPermissionsSeeder;
use Database\Seeders\Permissions\MunicipioPermissionsSeeder;
use Database\Seeders\Permissions\VistasPermissionsSeeder;
use Database\Seeders\Permissions\UsuarioPermissionsSeeder;
use Database\Seeders\Permissions\TareaPermissionsSeeder;
use Database\Seeders\Permissions\EncuestaPermissionsSeeder;
use Database\Seeders\Permissions\ConfiguracionPermissionsSeeder;
use Database\Seeders\Permissions\UnidadPermissionsSeeder;
use Database\Seeders\Permissions\ConferenciaPermissionsSeeder;
use Database\Seeders\Permissions\ConfiguracionAdmisionPermissionsSeeder;
use Database\Seeders\Permissions\ExamenPermissionSeeder;
use Database\Seeders\Permissions\AvisoPermissionsSeeder;
use Database\Seeders\Permissions\BajasTipoPermissionsSeeder;
use Database\Seeders\Permissions\ComponentePermissionsSeeder;
use Database\Seeders\Permissions\MaterialTipoPermissionsSeeder;
use Database\Seeders\Permissions\EspecialidadPermissionsSeeder;
use Database\Seeders\Permissions\EncuestaRespuestaPermissionsSeeder;
use Database\Seeders\Permissions\EncuestaPreguntaPermissionsSeeder;
use Database\Seeders\Permissions\TareaEnviadaPermissionsSeeder;
use Database\Seeders\Permissions\PreguntaTipoPermissionsSeeder;
use Database\Seeders\Permissions\TemasPermissionsSeeder;
use Database\Seeders\Permissions\TutoriaPermissionsSeeder;
use Database\Seeders\Permissions\ExamenesBancoPreguntasPermissionsSeeder;
use Database\Seeders\Permissions\ContenidosExtraPermissionsSeeder;
use Database\Seeders\Permissions\MaterialDidacticoPermissionSeeder;

use Database\Seeders\Permissions\GrupoPermissionsSeeder;
use Database\Seeders\Permissions\PeriodoPermissionsSeeder;
use Database\Seeders\Permissions\MateriaPermissionsSeeder;
use Database\Seeders\Permissions\DatosGeneralesPermissionsSeeder;
use Database\Seeders\Permissions\ExamenPreguntaPermissionsSeeder;
use Database\Seeders\Permissions\ExamenRespuestaPermissionsSeeder;
use Database\Seeders\Permissions\DatosFamiliaresPermissionsSeeder;
use Database\Seeders\Permissions\DatosAcademicosPermissionsSeeder;
use Database\Seeders\Permissions\ExamenesCalificacionesPermissionsSeeder;
use Database\Seeders\Permissions\PermisosPermissionsSeeder;
use Database\Seeders\Permissions\RolPermissionsSeeder;
use Database\Seeders\Permissions\MarcaDeTiempoPermissionsSeeder;
use Database\Seeders\Permissions\DocenteMateriaPermissionsSeeder;
use Database\Seeders\Permissions\AlumnoGrupoPermissionsSeeder;
use Database\Seeders\Permissions\AsistenciaAlumnoPermissionsSeeder;
use Database\Seeders\Permissions\RubricaPermissionsSeeder;
use Database\Seeders\Permissions\CalificacionParcialPermissionsSeeder;
use Database\Seeders\Permissions\EventoPermissionsSeeder;
use Database\Seeders\Permissions\InfraccionPermissionsSeeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'Alumno']); // Alumno
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Control escolar']);
        Role::create(['name' => 'Departamento de docentes']);
        Role::create(['name' => 'Docente']);
        Role::create(['name' => 'Prefecto']);
        Role::create(['name' => 'Padre de familia']);
        Role::create(['name' => 'Aspirante a ingreso']);
        Role::create(['name' => 'Deshabilitado']);

        $this->call([
            VistasPermissionsSeeder::class,
            EstadoPermissionsSeeder::class,
            UsuarioPermissionsSeeder::class,
            MunicipioPermissionsSeeder::class,
            PermisosPermissionsSeeder::class,
            RolPermissionsSeeder::class
        ]);

    }
}
