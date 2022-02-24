<?php

namespace Database\Seeders\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VistasPermissionsSeeder extends Seeder
{
  public function run ()
  {
    // SECTION: Create permissions for role
    Permission::create(['name' => 'Gestion de Usuarios', 'is_view' => '/users']);
    Permission::create(['name' => 'Dashboard', 'is_view' => '/dashboard']);
    Permission::create(['name' => 'Lista exámenes', 'is_view' => '/examenes/lista']);
    Permission::create(['name' => 'Crear examen', 'is_view' => '/examenes/crear-examen']);
    Permission::create(['name' => 'Lista aspirantes', 'is_view' => '/aspirantes/lista']);
    Permission::create(['name' => 'Calificar aspirante', 'is_view' => '/aspirantes/calificar']);
    Permission::create(['name' => 'Datos aspirante', 'is_view' => '/aspirante/datos']);
    Permission::create(['name' => 'Departamento docentes', 'is_view' => '/departamento-docentes']);
    Permission::create(['name' => 'Convocatoria', 'is_view' => '/control-escolar/convocatoria']);
    Permission::create(['name' => 'Aspirante admision', 'is_view' => '/aspirante/proceso-admision']);
    Permission::create(['name' => 'Planes de estudios', 'is_view' => '/departamento-docentes/planes-estudios']);
    Permission::create(['name' => 'Detalle de plan de estudio', 'is_view' => '/departamento-docentes/planes-estudios/:planId']);
    Permission::create(['name' => 'Detalle de semestre', 'is_view' => '/departamento-docentes/planes-estudios/:planId/periodo/:periodoId']);
    Permission::create(['name' => 'Detalle de materia', 'is_view' => '/departamento-docentes/planes-estudios/:planId/periodo/:periodoId/materia/:materiaId']);
    Permission::create(['name' => 'Detalle de Unidad', 'is_view' => '/departamento-docentes/planes-estudios/:planId/periodo/:periodoId/materia/:materiaId/unidad/:unidadId']);
    Permission::create(['name' => 'Detalle de Tema', 'is_view' => '/departamento-docentes/planes-estudios/:planId/periodo/:periodoId/materia/:materiaId/unidad/:unidadId/tema/:temaId']);
    Permission::create(['name' => 'Departamento docentes grupos', 'is_view' => '/departamento-docentes/grupos']);
    Permission::create(['name' => 'Departamento docentes usuarios', 'is_view' => '/departamento-docentes/usuarios']);
    Permission::create(['name' => 'Crear Plan Estudios', 'is_view' => '/departamento-docentes/crear-plan-estudios']);
    Permission::create(['name' => 'Dashboard de docentes', 'is_view' => '/dashboard-docentes']);
    Permission::create(['name' => 'Crear convocatoria', 'is_view' => '/controlEscolar-admision']);
    Permission::create(['name' => 'Configuracion de Admision', 'is_view' => '/control-escolar/convocatoria/:idConvocatoria?']);
    Permission::create(['name' => 'Dashboard control escolar', 'is_view' => '/control-escolar-dashboard']);
    Permission::create(['name' => 'Dashboard de docentes / Materia', 'is_view' => '/dashboard-docentes/especialidad/:planId/materia/:materiaId']);
    Permission::create(['name' => 'Dashboard de docentes / Unidad', 'is_view' => '/dashboard-docentes/especialidad/:planId/materia/:materiaId/unidad/:unidadId']);
    Permission::create(['name' => 'Dashboard de docentes / Tarea', 'is_view' => '/dashboard-docentes/especialidad/:planId/materia/:materiaId/unidad/:unidadId/tarea/:tareaId']);
    Permission::create(['name' => 'Dashboard de docentes / Tema', 'is_view' => '/dashboard-docentes/especialidad/:planId/materia/:materiaId/unidad/:unidadId/tema/:temaId']);
    Permission::create(['name' => 'Dashboard de docentes / Material', 'is_view' => '/dashboard-docentes/especialidad/:planId/materia/:materiaId/unidad/:unidadId/tema/:temaId?/material/:materialId?']);
    Permission::create(['name' => 'Docente contenido', 'is_view' => '/dashboard-docentes/contenido']);
    Permission::create(['name' => 'Gestión usuarios', 'is_view' => '/usuarios']);
    Permission::create(['name' => 'Departamento docentes crear grupos', 'is_view' => '/departamento-docentes/crear-editar-grupo/:grupoId?/:esGrupoAspirante?']);
    Permission::create(['name' => 'Aspirante admision examenes mensaje de registro', 'is_view' => '/aspirante/proceso-admision/registro-exitoso']);
    Permission::create(['name' => 'Aspirante admision examenes termino de examen', 'is_view' => '/aspirante/proceso-admision/examen-terminado']);
    Permission::create(['name' => 'Aspirante admision examenes', 'is_view' => '/aspirante/proceso-admision/examenes']);
    Permission::create(['name' => 'Dashboard de alumno', 'is_view' => '/dashboard-alumno']);
    Permission::create(['name' => 'Alumno Tareas', 'is_view' => '/alumno/tareas']);
    Permission::create(['name' => 'Alumno Tarea Detalle', 'is_view' => '/alumno/tareas/:tareasId?']);
    Permission::create(['name' => 'Alumno Examenes', 'is_view' => '/alumno/examenes']);
    Permission::create(['name' => 'Alumno Examen Detalle', 'is_view' => '/alumno/examenes/:examenId?']);
    Permission::create(['name' => 'Alumno Materia Detalle', 'is_view' => '/alumno/materias/:materiaId?']);
    Permission::create(['name' => 'Alumno Conferencias', 'is_view' => '/alumno/conferencias']);
    Permission::create(['name' => 'Alumno Presentar Examen', 'is_view' => '/alumno/presentar-examen/:examenId?']);
    Permission::create(['name' => 'Docente Conferencia', 'is_view' => '/conferencia/:conferenciaId?']);
    Permission::create(['name' => 'Alumno Conferencia', 'is_view' => '/conferencia/:conferenciaId?']);
    Permission::create(['name' => 'Docente Conferencias', 'is_view' => '/docente/conferencias']);
    Permission::create(['name' => 'Alumno Clases en Vivo', 'is_view' => '/alumno/clases-en-vivo/']);
    Permission::create(['name' => 'Docente lista de tutorias', 'is_view' => '/docentes/tutorias']);
    Permission::create(['name' => 'Docente ver tutoria', 'is_view' => '/docentes/tutorias/:tutoriaId']);
    Permission::create(['name' => 'Calificar alumnos', 'is_view' => '/calificar']);
    Permission::create(['name' => 'Control escolar lista de alumnos', 'is_view' => '/control-escolar/lista-de-alumnos']);
    Permission::create(['name' => 'Departamento docentes lista de alumnos', 'is_view' => '/departamento-docentes/lista-de-alumnos']);
    Permission::create(['name' => 'Calificar alumno detalle', 'is_view' => '/calificar/alumno-detalle/:alumnoId']);
    Permission::create(['name' => 'Calificar tarea alumnos', 'is_view' => '/calificar/grupo/:grupoId/tareas/:tareaId']);
    Permission::create(['name' => 'Control escolar alumno documentos', 'is_view' => '/control-escolar/documentos-alumno/:alumnoId']);
    Permission::create(['name' => 'Departamento docentes alumno documentos', 'is_view' => '/departamento-docentes/documentos-alumno/:alumnoId']);
    Permission::create(['name' => 'Control escolar captura de calificaciones', 'is_view' => '/control-escolar/captura-calificaciones']);
    Permission::create(['name' => 'Docente captura de calificaciones', 'is_view' => '/docente/captura-calificaciones']);
    Permission::create(['name' => 'Calificar examen alumnos', 'is_view' => '/grupo/:grupoId/examenes/:examenId']);
    Permission::create(['name' => 'Mis calificaciones', 'is_view' => '/mis-calificaciones']);
    Permission::create(['name' => 'historial academico', 'is_view' => '/historial-academico']);
    Permission::create(['name' => 'Control escolar seguimiento evaluativo', 'is_view' => '/control-escolar/seguimiento-evaluativo']);
    Permission::create(['name' => 'Control escolar chat', 'is_view' => '/control-escolar/chat']);
    Permission::create(['name' => 'Alumno chat', 'is_view' => '/alumno/chat']);
    Permission::create(['name' => 'Control escolar avisos', 'is_view' => '/control-escolar/avisos']);
    Permission::create(['name' => 'Departamento docentes avisos', 'is_view' => '/departamento-docentes/avisos']);
    Permission::create(['name' => 'Dashboard de prefecto', 'is_view' => '/dashboard-prefecto']);
    Permission::create(['name' => 'Dashboard de padre de familia', 'is_view' => '/dashboard-padre-de-familia']);
    Permission::create(['name' => 'Padre de familia avisos', 'is_view' => '/padre-de-familia/avisos rol']);
    Permission::create(['name' => 'Padre de familia reportes', 'is_view' => '/padre-de-familia/reportes']);
    Permission::create(['name' => 'Prefecto reportes de alumnos', 'is_view' => '/prefecto/reportes-de-alumnos']);
    Permission::create(['name' => 'Prefecto reportes de docentes', 'is_view' => '/prefecto/reportes-de-docentes']);
    Permission::create(['name' => 'Prefecto crear reporte', 'is_view' => '/prefecto/crear-reporte']);
    Permission::create(['name' => 'Docente gestion de encuestas', 'is_view' => '/docentes/gestion-de-encuestas']);
    Permission::create(['name' => 'Control escolar configuracion', 'is_view' => '/control-escolar/configuracion']);
    Permission::create(['name' => 'Perfil de usuario', 'is_view' => '/perfil']);
    Permission::create(['name' => 'Alumno reportes', 'is_view' => '/alumno/reportes']);
    Permission::create(['name' => 'Docente reportes', 'is_view' => '/docente/reportes']);

    $admin = Role::where('name', 'Admin')->first();
    $admin->givePermissionTo(Permission::all());

    $teacher = Role::where('name', 'Docente')->first();
    $teacher->givePermissionTo('Gestion de Usuarios');
    $teacher->givePermissionTo('Dashboard');
    $teacher->givePermissionTo('Lista exámenes');
    $teacher->givePermissionTo('Crear examen');
    $teacher->givePermissionTo('Departamento docentes');
    $teacher->givePermissionTo('Dashboard de docentes');
    $teacher->givePermissionTo('Dashboard de docentes / Materia');
    $teacher->givePermissionTo('Dashboard de docentes / Unidad');
    $teacher->givePermissionTo('Dashboard de docentes / Tarea');
    $teacher->givePermissionTo('Dashboard de docentes / Tema');
    $teacher->givePermissionTo('Dashboard de docentes / Material');
    $teacher->givePermissionTo('Docente Conferencia');
    $teacher->givePermissionTo('Docente contenido');
    $teacher->givePermissionTo('Docente Conferencias');
    $teacher->givePermissionTo('Docente lista de tutorias');
    $teacher->givePermissionTo('Docente ver tutoria');
    $teacher->givePermissionTo('Calificar alumnos');
    $teacher->givePermissionTo('Calificar alumno detalle');
    $teacher->givePermissionTo('Calificar tarea alumnos');
    $teacher->givePermissionTo('Docente captura de calificaciones');
    $teacher->givePermissionTo('Calificar examen alumnos');
    $teacher->givePermissionTo('Docente gestion de encuestas');
    $teacher->givePermissionTo('Perfil de usuario');
    $teacher->givePermissionTo('Docente reportes');

    $controlEscolar = Role::where('name', 'Control Escolar')->first();
    $controlEscolar->givePermissionTo('Gestion de Usuarios');
    $controlEscolar->givePermissionTo('Dashboard');
    $controlEscolar->givePermissionTo('Lista exámenes');
    $controlEscolar->givePermissionTo('Crear examen');
    $controlEscolar->givePermissionTo('Convocatoria');
    $controlEscolar->givePermissionTo('Crear Plan Estudios');
    $controlEscolar->givePermissionTo('Departamento docentes usuarios');
    $controlEscolar->givePermissionTo('Crear convocatoria');
    $controlEscolar->givePermissionTo('Configuracion de Admision');
    $controlEscolar->givePermissionTo('Dashboard control escolar');
    $controlEscolar->givePermissionTo('Gestión usuarios');
    $controlEscolar->givePermissionTo('Control escolar lista de alumnos');
    $controlEscolar->givePermissionTo('Departamento docentes lista de alumnos');
    $controlEscolar->givePermissionTo('Control escolar alumno documentos');
    $controlEscolar->givePermissionTo('Control escolar captura de calificaciones');
    $controlEscolar->givePermissionTo('Control escolar seguimiento evaluativo');
    $controlEscolar->givePermissionTo('Control escolar chat');
    $controlEscolar->givePermissionTo('Control escolar avisos');
    $controlEscolar->givePermissionTo('Control escolar configuracion');
    $controlEscolar->givePermissionTo('Perfil de usuario');

    $aspirante = Role::where('name', 'Aspirante a ingreso')->first();
    $aspirante->givePermissionTo('Datos aspirante');
    $aspirante->givePermissionTo('Aspirante admision');
    $aspirante->givePermissionTo('Aspirante admision examenes');
    $aspirante->givePermissionTo('Aspirante admision examenes mensaje de registro');
    $aspirante->givePermissionTo('Aspirante admision examenes termino de examen');
    $aspirante->givePermissionTo('Perfil de usuario');

    $departamentoDocentes = Role::where('name', 'Departamento de docentes')->first();
    $departamentoDocentes->givePermissionTo('Planes de estudios');
    $departamentoDocentes->givePermissionTo('Aspirante admision');
    $departamentoDocentes->givePermissionTo('Detalle de plan de estudio');
    $departamentoDocentes->givePermissionTo('Detalle de semestre');
    $departamentoDocentes->givePermissionTo('Detalle de materia');
    $departamentoDocentes->givePermissionTo('Departamento docentes');
    $departamentoDocentes->givePermissionTo('Detalle de Unidad');
    $departamentoDocentes->givePermissionTo('Detalle de Tema');
    $departamentoDocentes->givePermissionTo('Departamento docentes grupos');
    $departamentoDocentes->givePermissionTo('Crear Plan Estudios');
    $departamentoDocentes->givePermissionTo('Departamento docentes usuarios');
    $departamentoDocentes->givePermissionTo('Gestión usuarios');
    $departamentoDocentes->givePermissionTo('Departamento docentes crear grupos');
    $departamentoDocentes->givePermissionTo('Control escolar lista de alumnos');
    $departamentoDocentes->givePermissionTo('Departamento docentes lista de alumnos');
    $departamentoDocentes->givePermissionTo('Departamento docentes alumno documentos');
    $departamentoDocentes->givePermissionTo('Departamento docentes avisos');
    $departamentoDocentes->givePermissionTo('Perfil de usuario');

    $student = Role::where('name', 'Alumno')->first();
    $student->givePermissionTo('Dashboard de alumno');
    $student->givePermissionTo('Alumno Tareas');
    $student->givePermissionTo('Alumno Tarea Detalle');
    $student->givePermissionTo('Alumno Examenes');
    $student->givePermissionTo('Alumno Examen Detalle');
    $student->givePermissionTo('Alumno Materia Detalle');
    $student->givePermissionTo('Alumno Conferencias');
    $student->givePermissionTo('Alumno Conferencia');
    $student->givePermissionTo('Alumno Presentar Examen');
    $student->givePermissionTo('Alumno Clases en Vivo');
    $student->givePermissionTo('Mis calificaciones');
    $student->givePermissionTo('historial academico');
    $student->givePermissionTo('Alumno chat');
    $student->givePermissionTo('Perfil de usuario');
    $student->givePermissionTo('Alumno reportes');

    $Prefecto = Role::where('name','Prefecto')->first();
    $Prefecto->givePermissionTo('Dashboard de prefecto');
    $Prefecto->givePermissionTo('Prefecto reportes de docentes');
    $Prefecto->givePermissionTo('Prefecto reportes de alumnos');
    $Prefecto->givePermissionTo('Prefecto crear reporte');
    $Prefecto->givePermissionTo('Perfil de usuario');

    $PadreFamilia = Role::where('name','Padre de familia')->first();
    $PadreFamilia->givePermissionTo('Dashboard de padre de familia');
    $PadreFamilia->givePermissionTo('Padre de familia avisos');
    $PadreFamilia->givePermissionTo('Padre de familia reportes');
    $PadreFamilia->givePermissionTo('Perfil de usuario');

    $roles = Role::all();
    foreach ($roles as $role) {
        $role->givePermissionTo('Dashboard');
    }

  }
}
