    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;


    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
    */

    Route::prefix('v1')->group(function () {
        Route::post('aspirantes', 'App\Http\Controllers\SpecialMethods\AspirantesController@create');
        Route::post('confirmar-email', 'App\Http\Controllers\MailerController@cofirmEmail');
        Route::post('veficar-email', 'App\Http\Controllers\SpecialMethods\UsuariosController@VerifyEmail');
        Route::get('configuracion-admision/{configuracion_admision_id}','App\Http\Controllers\Api\ConfiguracionAdmisionController@get');
        Route::post('generar-pdf', 'App\Http\Controllers\SpecialMethods\CalificacionParcialController@generarPdf'); //  Code::Fixme > Issue #416

        Route::group([
            'prefix' => 'configuraciones',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\ConfiguracionesController@list');
            Route::get('/{configuracion_id}','App\Http\Controllers\Api\ConfiguracionesController@get');
        });

    Route::prefix('auth')->group(function () {
        Route::post('login', [App\Http\Controllers\AuthController::class,'login']);
        Route::get('getRImage/{name}', ['App\Http\Controllers\ResourceController', 'showResource']);

        Route::middleware(['auth:api'])->group(function() {

            Route::post('updateResource', ['App\Http\Controllers\ResourceController', 'createUpdate']);
            Route::get('allResource', ['App\Http\Controllers\ResourceController', 'index']);
            Route::get('getResource/{id}', ['App\Http\Controllers\ResourceController', 'show']);
            Route::delete('resource/{id}', ['App\Http\Controllers\ResourceController', 'delete']);
            Route::get('caruselResource', ['App\Http\Controllers\ResourceController', 'showCarusel']);
            Route::get('default/{id}', ['App\Http\Controllers\ResourceController', 'default']);

            Route::get('logout', [App\Http\Controllers\AuthController::class,'logout']);
            Route::get('user', [App\Http\Controllers\AuthController::class,'user']);
            Route::get('account_data', [App\Http\Controllers\AuthController::class,'account_data']);

            Route::post('signup', [App\Http\Controllers\AuthController::class,'signup']);

            Route::get('permissions/{id}', ['App\Http\Controllers\UserController', 'permissions']);
            Route::get('views/{id}', ['App\Http\Controllers\UserController', 'views']);
            Route::get('roles', ['App\Http\Controllers\Api\RolesController', 'list']);
            Route::get('roles/{id}', ['App\Http\Controllers\UserController', 'roles']);
        });
    });

    Route::middleware(['auth:api'])->group(function() {

        Route::group([
            'prefix' => 'configuraciones',
        ], function () {
            Route::post('/', 'App\Http\Controllers\Api\ConfiguracionesController@create');
            Route::post('/{configuracion_id}', 'App\Http\Controllers\Api\ConfiguracionesController@update');
            Route::delete('/{configuracion_id}','App\Http\Controllers\Api\ConfiguracionesController@delete');
        });

        Route::group([
            'prefix' => 'usuarios',
        ], function () {

            Route::post('/cambio-clave', [App\Http\Controllers\SpecialMethods\UsuariosController::class, 'cambioClave']);
            Route::get('/', 'App\Http\Controllers\Api\UsuariosController@list');
            Route::get('/{usuario_id}','App\Http\Controllers\Api\UsuariosController@get');
            Route::post('/', 'App\Http\Controllers\Api\UsuariosController@create');
            Route::post('/{usuario_id}', 'App\Http\Controllers\Api\UsuariosController@update');
            Route::delete('/{usuario}','App\Http\Controllers\Api\UsuariosController@delete');

            Route::group([
                'prefix' => '{usuario_id}/datos-generales',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\DatosGeneralesController@list');
                Route::get('/{datosGenerales_id}','App\Http\Controllers\Api\DatosGeneralesController@get');
                Route::post('/', 'App\Http\Controllers\Api\DatosGeneralesController@create');
                Route::post('/{datosGenerales_id}', 'App\Http\Controllers\Api\DatosGeneralesController@update');
                Route::delete('/{datosGenerales_id}','App\Http\Controllers\Api\DatosGeneralesController@delete');
            });

            Route::group([
                'prefix' => '{usuario_id}/datos-familiares',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\DatosFamiliaresController@list');
                Route::get('/{datosFamiliares_id}','App\Http\Controllers\Api\DatosFamiliaresController@get');
                Route::post('/', 'App\Http\Controllers\Api\DatosFamiliaresController@create');
                Route::put('/{datosFamiliares_id}', 'App\Http\Controllers\Api\DatosFamiliaresController@update');
                Route::delete('/{datosFamiliares_id}','App\Http\Controllers\Api\DatosFamiliaresController@delete');
            });

            Route::group([
                'prefix' => '{usuario_id}/datos-academicos',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\DatosAcademicosController@list');
                Route::get('/{datosAcademicos_id}','App\Http\Controllers\Api\DatosAcademicosController@get');
                Route::post('/', 'App\Http\Controllers\Api\DatosAcademicosController@create');
                Route::put('/{datosAcademicos_id}', 'App\Http\Controllers\Api\DatosAcademicosController@update');
                Route::delete('/{datosAcademicos_id}','App\Http\Controllers\Api\DatosAcademicosController@delete');
            });

            Route::group([
                'prefix' => '{usuario_id}/examenes',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\ExamenesController@list');
                Route::get('/{examen_id}','App\Http\Controllers\Api\ExamenesController@get');
                Route::post('/', 'App\Http\Controllers\Api\ExamenesController@create');
                Route::put('/{examen_id}', 'App\Http\Controllers\Api\ExamenesController@update');
                Route::delete('/{examen_id}','App\Http\Controllers\Api\ExamenesController@delete');





                Route::group([
                    'prefix' => '{examen_id}/examenes-preguntas',
                ], function () {
                    Route::get('/', 'App\Http\Controllers\Api\ExamenPreguntasController@list');
                    Route::get('/{examenes_preguntas_id}','App\Http\Controllers\Api\ExamenPreguntasController@get');
                    Route::post('/', 'App\Http\Controllers\Api\ExamenPreguntasController@create');
                    Route::post('/{examenes_preguntas_id}', 'App\Http\Controllers\Api\ExamenPreguntasController@update');
                    Route::delete('/{examenes_preguntas_id}','App\Http\Controllers\Api\ExamenPreguntasController@delete');

                    Route::group([
                        'prefix' => '{examenes_preguntas_id}/examenes-respuestas',
                    ], function () {
                        Route::get('/{examenes_respuestas_id}','App\Http\Controllers\Api\ExamenRespuestasController@get');
                        Route::post('/', 'App\Http\Controllers\Api\ExamenRespuestasController@create');
                        Route::put('/{examenes_respuestas_id}', 'App\Http\Controllers\Api\ExamenRespuestasController@update');
                        Route::delete('/{examenes_respuestas_id}','App\Http\Controllers\Api\ExamenRespuestasController@delete');
                    });
                });

                Route::group([
                    'prefix' => '{examen_id}/examenes-respuestas',
                ], function () {
                    Route::get('/', 'App\Http\Controllers\Api\ExamenRespuestasController@list');
                });

                Route::group([
                    'prefix' => '{examen_id}/examenes-calificaciones',
                ], function () {
                    Route::get('/', 'App\Http\Controllers\Api\ExamenCalificacionesController@list');
                    Route::get('/{examen_calificacion_id}','App\Http\Controllers\Api\ExamenCalificacionesController@get');
                    Route::post('/', 'App\Http\Controllers\Api\ExamenCalificacionesController@create');
                    Route::put('/{examen_calificacion_id}', 'App\Http\Controllers\Api\ExamenCalificacionesController@update');
                    Route::delete('/{examen_calificacion_id}','App\Http\Controllers\Api\ExamenCalificacionesController@delete');
                });

            });

            Route::group([
                'prefix' => '{usuario_id}/actividades',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\ActividadesController@list');
                Route::get('/{actividad_id}','App\Http\Controllers\Api\ActividadesController@get');
                Route::post('/', 'App\Http\Controllers\Api\ActividadesController@create');
                Route::put('/{actividad_id}', 'App\Http\Controllers\Api\ActividadesController@update');
                Route::delete('/{actividad_id}','App\Http\Controllers\Api\ActividadesController@delete');
            });
        });

        Route::group([
            'prefix' => 'tutorias',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\TutoriasController@list');
            Route::get('/{tutoria_id}','App\Http\Controllers\Api\TutoriasController@get');
            Route::post('/', 'App\Http\Controllers\Api\TutoriasController@create');
            Route::post('/{tutoria_id}', 'App\Http\Controllers\Api\TutoriasController@update');
            Route::delete('/{tutoria_id}','App\Http\Controllers\Api\TutoriasController@delete');
            Route::delete('/{tutoria_id}/imagen/{uuid}','App\Http\Controllers\Api\TutoriasController@deleteImage');
        });

        Route::group([
            'prefix' => 'estados',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\EstadosController@list');
            Route::get('/{estado_id}','App\Http\Controllers\Api\EstadosController@get');
            Route::post('/', 'App\Http\Controllers\Api\EstadosController@create');
            Route::put('/{estado_id}', 'App\Http\Controllers\Api\EstadosController@update');
            Route::delete('/{estado_id}','App\Http\Controllers\Api\EstadosController@delete');

            Route::group([
                'prefix' => '{estado_id}/municipios',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\MunicipiosController@list');
                Route::get('/{municipio_id}','App\Http\Controllers\Api\MunicipiosController@get');
                Route::post('/', 'App\Http\Controllers\Api\MunicipiosController@create');
                Route::put('/{municipio_id}', 'App\Http\Controllers\Api\MunicipiosController@update');
                Route::delete('/{municipio_id}','App\Http\Controllers\Api\MunicipiosController@delete');

            });
        });

        Route::group([
            'prefix' => 'examenes-tipo',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\ExamenTiposController@list');
            Route::get('/{examen_tipo_id}','App\Http\Controllers\Api\ExamenTiposController@get');
            Route::post('/', 'App\Http\Controllers\Api\ExamenTiposController@create');
            Route::put('/{examen_tipo_id}', 'App\Http\Controllers\Api\ExamenTiposController@update');
            Route::delete('/{examen_tipo_id}','App\Http\Controllers\Api\ExamenTiposController@delete');

        });

        Route::group([
            'prefix' => 'examenes-banco-preguntas',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\ExamenesBancoPreguntasController@list');
            Route::get('/{examenesBancoPreguntas_id}','App\Http\Controllers\Api\ExamenesBancoPreguntasController@get');
            Route::post('/', 'App\Http\Controllers\Api\ExamenesBancoPreguntasController@create');
            Route::post('/{examenesBancoPreguntas_id}', 'App\Http\Controllers\Api\ExamenesBancoPreguntasController@update');
            Route::delete('/{examenesBancoPreguntas_id}','App\Http\Controllers\Api\ExamenesBancoPreguntasController@delete');
        });

        Route::group([
            'prefix' => 'conferencias',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\ConferenciasController@list');
            Route::get('/{conferencia_id}','App\Http\Controllers\Api\ConferenciasController@get');
            Route::put('/{conferencia_id}', 'App\Http\Controllers\Api\ConferenciasController@update');
            Route::delete('/{conferencia_id}','App\Http\Controllers\Api\ConferenciasController@delete');
        });

        Route::group([
            'prefix' => 'encuestas',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\EncuestasController@list');
            Route::get('/{encuesta_id}','App\Http\Controllers\Api\EncuestasController@get')->where('encuesta_id', '[0-9]+');
            Route::post('/', 'App\Http\Controllers\Api\EncuestasController@create');
            Route::put('/{encuesta_id}', 'App\Http\Controllers\Api\EncuestasController@update');
            Route::delete('/{encuesta_id}','App\Http\Controllers\Api\EncuestasController@delete');

            Route::group([
                'prefix' => '{encuesta_id}/encuestas-respuestas',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\EncuestasRespuestasController@list');
                Route::get('/{encuestaRespuesta_id}','App\Http\Controllers\Api\EncuestasRespuestasController@get');
                Route::post('/', 'App\Http\Controllers\Api\EncuestasRespuestasController@create');
                Route::put('/{encuestaRespuesta_id}', 'App\Http\Controllers\Api\EncuestasRespuestasController@update');
                Route::delete('/{encuestaRespuesta_id}','App\Http\Controllers\Api\EncuestasRespuestasController@delete');
            });

            Route::group([
                'prefix' => '{encuesta_id}/encuestas-preguntas',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\EncuestasPreguntasController@list');
                Route::get('/{encuestaPregunta_id}','App\Http\Controllers\Api\EncuestasPreguntasController@get');
                Route::post('/', 'App\Http\Controllers\Api\EncuestasPreguntasController@create');
                Route::post('/{encuestaPregunta_id}', 'App\Http\Controllers\Api\EncuestasPreguntasController@update');
                Route::delete('/{encuestaPregunta_id}','App\Http\Controllers\Api\EncuestasPreguntasController@delete');
            });
        });

        Route::group([
            'prefix' => 'avisos',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\AvisosController@list');
            Route::get('/{aviso_id}','App\Http\Controllers\Api\AvisosController@get');
            Route::post('/', 'App\Http\Controllers\Api\AvisosController@create');
            Route::post('/{aviso_id}', 'App\Http\Controllers\Api\AvisosController@update');
            Route::delete('/{aviso_id}','App\Http\Controllers\Api\AvisosController@delete');
        });

        Route::group([
            'prefix' => 'eventos',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\EventosController@list');
            Route::get('/{evento_id}','App\Http\Controllers\Api\EventosController@get');
            Route::post('/', 'App\Http\Controllers\Api\EventosController@create');
            Route::post('/{evento_id}', 'App\Http\Controllers\Api\EventosController@update');
            Route::delete('/{evento_id}','App\Http\Controllers\Api\EventosController@delete');
        });

        Route::group([
            'prefix' => 'componentes',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\ComponentesController@list');
            Route::get('/{componente_id}','App\Http\Controllers\Api\ComponentesController@get');
            Route::post('/', 'App\Http\Controllers\Api\ComponentesController@create');
            Route::put('/{componente_id}', 'App\Http\Controllers\Api\ComponentesController@update');
            Route::delete('/{componente_id}','App\Http\Controllers\Api\ComponentesController@delete');
        });

        Route::group([
            'prefix' => 'configuracion-admision',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\ConfiguracionAdmisionController@list');
            Route::post('/', 'App\Http\Controllers\Api\ConfiguracionAdmisionController@create');
            Route::post('/{configuracion_admision_id}', 'App\Http\Controllers\Api\ConfiguracionAdmisionController@update');
            Route::delete('/{configuracion_admision_id}','App\Http\Controllers\Api\ConfiguracionAdmisionController@delete');

        });

        Route::group([
            'prefix' => 'tipos-material',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\MaterialesTiposController@list');
            Route::get('/{materialTipo_id}','App\Http\Controllers\Api\MaterialesTiposController@get');
            Route::post('/', 'App\Http\Controllers\Api\MaterialesTiposController@create');
            Route::put('/{materialTipo_id}', 'App\Http\Controllers\Api\MaterialesTiposController@update');
            Route::delete('/{materialTipo_id}','App\Http\Controllers\Api\MaterialesTiposController@delete');

        });

        Route::group([
            'prefix' => 'especialidades',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\EspecialidadesController@list');
            Route::get('/{especialidad_id}','App\Http\Controllers\Api\EspecialidadesController@get');
            Route::post('/', 'App\Http\Controllers\Api\EspecialidadesController@create');
            Route::post('/{especialidad_id}', 'App\Http\Controllers\Api\EspecialidadesController@update');
            Route::delete('/{especialidad_id}','App\Http\Controllers\Api\EspecialidadesController@delete');

            Route::group([
                'prefix' => '{especialidad_id}/materias',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\MateriasController@list');
                Route::get('/{materia_id}','App\Http\Controllers\Api\MateriasController@get');
                Route::post('/', 'App\Http\Controllers\Api\MateriasController@create');
                Route::post('/{materia_id}', 'App\Http\Controllers\Api\MateriasController@update');
                Route::delete('/{materia_id}','App\Http\Controllers\Api\MateriasController@delete');

                Route::group([
                    'prefix' => '{materia_id}/unidades',
                ], function () {
                    Route::get('/', 'App\Http\Controllers\Api\UnidadesController@list');
                    Route::get('/{unidad_id}','App\Http\Controllers\Api\UnidadesController@get');
                    Route::post('/', 'App\Http\Controllers\Api\UnidadesController@create');
                    Route::put('/{unidad_id}', 'App\Http\Controllers\Api\UnidadesController@update');
                    Route::delete('/{unidad_id}','App\Http\Controllers\Api\UnidadesController@delete');

                    Route::group([
                        'prefix' => '{unidad_id}/tareas',
                    ], function () {
                        Route::get('/', 'App\Http\Controllers\Api\TareasController@index');
                        Route::get('/{tarea_id}','App\Http\Controllers\Api\TareasController@show');
                        Route::post('/', 'App\Http\Controllers\Api\TareasController@store');
                        Route::post('/{tarea_id}', 'App\Http\Controllers\Api\TareasController@update');
                        Route::delete('/{tarea_id}','App\Http\Controllers\Api\TareasController@destroy');
                    });

                    Route::group([
                        'prefix' => '{unidad_id}/temas',
                    ], function () {
                        Route::get('/', 'App\Http\Controllers\Api\TemasController@list');
                        Route::get('/{tema_id}','App\Http\Controllers\Api\TemasController@get');
                        Route::post('/', 'App\Http\Controllers\Api\TemasController@create');
                        Route::put('/{tema_id}', 'App\Http\Controllers\Api\TemasController@update');
                        Route::delete('/{tema_id}','App\Http\Controllers\Api\TemasController@delete');

                        Route::group([
                            'prefix' => '{tema_id}/materiales-didacticos',
                        ], function () {
                            Route::get('/', 'App\Http\Controllers\Api\MaterialesDidacticosController@list');
                            Route::get('/{repositorio_id}','App\Http\Controllers\Api\MaterialesDidacticosController@get');
                            Route::post('/', 'App\Http\Controllers\Api\MaterialesDidacticosController@create');
                            Route::post('/{repositorio_id}', 'App\Http\Controllers\Api\MaterialesDidacticosController@update')->where('repositorio_id', '[0-9]+');
                            Route::delete('/{repositorio_id}','App\Http\Controllers\Api\MaterialesDidacticosController@delete');
                        });
                    });

                });

            });
        });

        Route::prefix('periodos')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\PeriodoController::class, 'list']);
            Route::get('/{periodo_id}', [App\Http\Controllers\Api\PeriodoController::class, 'get']);

            Route::group([
                'prefix' => '{periodo_id}/contenidos-extra',
            ], function () {
                Route::get('/', 'App\Http\Controllers\Api\ContenidosExtrasController@list');
                Route::get('/{ContenidosExtra_id}','App\Http\Controllers\Api\ContenidosExtrasController@get');
                Route::post('/', 'App\Http\Controllers\Api\ContenidosExtrasController@create');
                Route::post('/{ContenidosExtra_id}', 'App\Http\Controllers\Api\ContenidosExtrasController@update');
                Route::delete('/{ContenidosExtra_id}','App\Http\Controllers\Api\ContenidosExtrasController@delete');
            });
        });

        Route::group([
            'prefix' => 'grupos',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\GruposController@list');
            Route::get('/{grupo_id}','App\Http\Controllers\Api\GruposController@get');
            Route::post('/', 'App\Http\Controllers\Api\GruposController@create');
            Route::put('/{grupo_id}', 'App\Http\Controllers\Api\GruposController@update');
            Route::delete('/{grupo_id}','App\Http\Controllers\Api\GruposController@delete');

        });

        Route::group([
            'prefix' => 'rubrica',
        ], function () {
            Route::get('/{rubrica_id}','App\Http\Controllers\Api\RubricaController@get');
            Route::post('/', 'App\Http\Controllers\Api\RubricaController@createUpdate');
        });

        Route::group([
            'prefix' => 'preguntas-tipo',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\PreguntasTipoController@list');
            Route::get('/{pregunta_tipo_id}','App\Http\Controllers\Api\PreguntasTipoController@get');
        });

        Route::group([
            'prefix' => 'permisos/*',
        ], function () {
            Route::post('usuarios', 'App\Http\Controllers\SpecialMethods\PermisoController@syncPermissionForUsers');
            Route::put('usuarios/{usuario_id}', 'App\Http\Controllers\SpecialMethods\PermisoController@syncPermissionToUser');
        });

        Route::group([
            'prefix' => 'bajas-tipo',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\BajasTipoController@list');
            Route::get('/{baja_tipo_id}','App\Http\Controllers\Api\BajasTipoController@get');
        });

        Route::group([
            'prefix' => 'infracciones',
        ], function () {
            Route::get('/', 'App\Http\Controllers\Api\InfraccionesController@list');
            Route::get('/{infraccion_id}','App\Http\Controllers\Api\InfraccionesController@get');
            Route::post('/', 'App\Http\Controllers\Api\InfraccionesController@create');
            Route::post('/{infraccion_id}', 'App\Http\Controllers\Api\InfraccionesController@update');
            Route::delete('/{infraccion_id}','App\Http\Controllers\Api\InfraccionesController@delete');
        });

        // RUTAS ESPECIALES
        //Especialidades Periodos
        Route::get('especialidades-periodos', [App\Http\Controllers\SpecialMethods\EspecialidadesPeriodosController::class, 'list']);
        Route::get('especialidades-periodos/{especialidad_periodo_id}', [App\Http\Controllers\SpecialMethods\EspecialidadesPeriodosController::class, 'get']);
        
        //Grupos Materias
        Route::get('especialidades-periodos/{especialidad_periodo_id}/grupos-materias', [App\Http\Controllers\SpecialMethods\GruposMateriasController::class, 'list']);

        // Examenes
        Route::post('usuarios/{usuario_id}/examenes/{examen_id}:rate', [App\Http\Controllers\SpecialMethods\ExamenesCalificacionesController::class, 'rate']);
        Route::get('examenes:search','App\Http\Controllers\SpecialMethods\ExamenesController@search');
        Route::post('examenes/examen/{examen_id}:copy', [App\Http\Controllers\SpecialMethods\ExamenesController::class, 'copy']);
        Route::get('examenes-banco-preguntas:search','App\Http\Controllers\SpecialMethods\ExamenesBancoPreguntasController@search');
        Route::post('examenes/{examen_id}/temas/{tema_id}:bind', 'App\Http\Controllers\SpecialMethods\ExamenesController@bindTema');
        Route::post('examenes/{examen_id}/temas/{tema_id}:unbind', 'App\Http\Controllers\SpecialMethods\ExamenesController@unbindTema');
        Route::post('examenes/{examen_id}/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}:bind', 'App\Http\Controllers\SpecialMethods\ExamenesController@bindEspecialidadPeriodoGrupo');
        Route::post('examenes/{examen_id}/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}:unbind', 'App\Http\Controllers\SpecialMethods\ExamenesController@unbindEspecialidadPeriodoGrupo');
        Route::get('examenes/{examen_id}/verificar-estado-respondido','App\Http\Controllers\SpecialMethods\ExamenesController@verificarEstadoRespondido');

        // Aspirantes
        Route::put('aspirantes/{usuario_id}/status', 'App\Http\Controllers\SpecialMethods\ExamenesCalificacionesController@changeStatusAspirante');
        Route::get('aspirantes', 'App\Http\Controllers\SpecialMethods\AspirantesController@list');
        Route::get('aspirantes/{usuario_id}/correoTerminoExamen', 'App\Http\Controllers\SpecialMethods\AspirantesController@enviarCorreoTerminoExamen');
        Route::post(
            'aspirantes/{usuario_id}/examenes/{examen_id}/examenes-preguntas/{examenes_preguntas_id}/examenes-respuestas',
            'App\Http\Controllers\SpecialMethods\AspirantesExamenRespuestasController@evaluateAnswerExam'
            )->where(['usuario_id' => '[0-9]+','examen_id' => '[0-9]+','examenes_preguntas_id' => '[0-9]+',]);
        Route::get('aspirantes/*/{curp}', 'App\Http\Controllers\SpecialMethods\AspirantesController@curp');

        // Usuarios
        Route::get('usuarios:search', 'App\Http\Controllers\SpecialMethods\UsuariosController@search');
        Route::get('usuarios/*/getFormatImportUsers', 'App\Http\Controllers\SpecialMethods\UsuariosController@getFormatImportUsers');
        Route::post('usuarios/*/importUsers', 'App\Http\Controllers\SpecialMethods\UsuariosController@importUsers');
        Route::get('usuarios/*/exportUsers', 'App\Http\Controllers\SpecialMethods\UsuariosController@exportUsers');

        // Filtros
        Route::get('periodos:search', 'App\Http\Controllers\SpecialMethods\PeriodosController@search');
        Route::get('materias:search', 'App\Http\Controllers\SpecialMethods\MateriasController@search');
        Route::get('unidades:search', 'App\Http\Controllers\SpecialMethods\UnidadesController@search');
        Route::get('temas:search', 'App\Http\Controllers\SpecialMethods\TemasController@search');
        Route::get('tareas:search', 'App\Http\Controllers\SpecialMethods\TareasController@search');
        Route::get('grupos:search', 'App\Http\Controllers\SpecialMethods\GruposController@search');
        Route::get('materiales-didacticos:search', 'App\Http\Controllers\SpecialMethods\MaterialesDidacticosController@search');
        Route::get('avisos:search', 'App\Http\Controllers\SpecialMethods\AvisosController@search');
        Route::get('eventos:search', 'App\Http\Controllers\SpecialMethods\EventosController@search');
        Route::get('tutorias:search', 'App\Http\Controllers\SpecialMethods\TutoriasController@search');
        Route::get('conferencias:search', 'App\Http\Controllers\SpecialMethods\ConferenciasController@search');
        Route::get('encuestas:search', 'App\Http\Controllers\SpecialMethods\EncuestasController@search');
        Route::get('infracciones:search', 'App\Http\Controllers\SpecialMethods\InfraccionesController@search');

        // Encuestas
        Route::get('encuestas/{encuesta_id}:copy', 'App\Http\Controllers\SpecialMethods\EncuestasController@copy');
        Route::get('encuestas/{encuesta_id}/resultados', 'App\Http\Controllers\SpecialMethods\EncuestasController@resultadoEncuesta');
        Route::get('encuestas/{encuesta_id}/exportar', 'App\Http\Controllers\SpecialMethods\EncuestasController@exportarResultadoEncuesta');

        // Especialidades
        Route::get('especialidades:batchGet/{especialidad_id}', 'App\Http\Controllers\SpecialMethods\EspecialidadesController@batchGet');

        // Materiales didácticos
        Route::post(
            'especialidades/{especialidad_id}/materias/{materia_id}/unidades/{unidad_id}/temas/{tema_id}/materiales-didacticos/{repositorio_id}:copy',
            'App\Http\Controllers\SpecialMethods\MaterialesDidacticosController@copy'
        );
        Route::put(
            'especialidades/{especialidad_id}/materias/{materia_id}/unidades/{unidad_id}/temas/{tema_id}/materiales-didacticos/{repositorio_id}:check',
            'App\Http\Controllers\SpecialMethods\MaterialesDidacticosController@check'
        );

        // Periodos
        Route::post('periodos:bind', 'App\Http\Controllers\SpecialMethods\PeriodosController@bind');
        Route::post('periodos:unbind', 'App\Http\Controllers\SpecialMethods\PeriodosController@unbind');

        // Tareas
        Route::post('tareas/{tarea_id}/grupos/{grupo_id}:bind', 'App\Http\Controllers\SpecialMethods\TareasController@bindGrupo');
        Route::delete('tareas/{tarea_id}/grupos/{grupo_id}:unbind', 'App\Http\Controllers\SpecialMethods\TareasController@unBindGrupo');
        Route::post('especialidades/{especialidad_id}/periodos/{periodo_id}/materias/{materia_id}/unidades/{unidad_id}/tareas/{tarea_id}:copy', 'App\Http\Controllers\SpecialMethods\TareasController@copy');
        Route::post('tareas/{tarea_id}/temas/{tema_id}:bind', 'App\Http\Controllers\SpecialMethods\TareasTemasController@bindTareaTema');
        Route::delete('tareas/{tarea_id}/temas/{tema_id}:unbind', 'App\Http\Controllers\SpecialMethods\TareasTemasController@unbindTareaTema');

        //bajas
        Route::post('usuarios/{usuario_id}/:baja','App\Http\Controllers\SpecialMethods\UsuariosController@darDeBajaUsuario');
        Route::post('usuarios/{usuario_id}/:reingreso','App\Http\Controllers\SpecialMethods\UsuariosController@reingresarUsuario');

        //Docentes Materias
        Route::post('docentes/{docente_id}/materias/{materia_id}:bind', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'bindMateria']);
        Route::post('docentes/{docente_id}/materias/{materia_id}:unbind', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'unbindMateria']);
        Route::get('docentes/{docente_id}/materias/{materia_id}/grupos', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'listGrupos']);
        Route::get('docentes/*/calificar-tarea/{tarea_id}', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'listCalificarTareaAlumnos']);
        Route::post('docentes/*/calificar-tarea/{tarea_id}/alumnos/{alumno_id}', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'createUpdateCalificarTareaAlumnos']);
        Route::get('docentes/*/calificar-examenes/{examen_id}', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'listCalificarExamenAlumnos']);
        Route::get('docentes/{docente_id}/materias/{materia_id}', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'getDocenteMateria']);
        Route::get('materias:batchGet/{materia_id}', 'App\Http\Controllers\SpecialMethods\MateriasController@batchGet');
        Route::get('docentes/{docente_id}/calendario', 'App\Http\Controllers\SpecialMethods\DocentesController@getTareasExamenesCalendario');
        Route::get('docentes/*/tabla-calificacion/{docente_materia_id}', 'App\Http\Controllers\SpecialMethods\DocentesController@getTablaCalificacion');
        Route::get('docentes/*/tabla-calificacion/{docente_materia_id}/alumno/{alumno_id}', 'App\Http\Controllers\SpecialMethods\DocentesController@getTablaCalificacionDetalleAlumno');
        Route::get('docentes/*/alumnos-grupo/{especialidad_periodo_grupo_id}', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'listAlumnosGrupo']);
        Route::post('docentes/{docente_id}/pase-lista', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'createUpdateAsistenciasGrupo']);
        Route::put('docentes/{docente_id}/pase-lista/*/alumnos/{alumno_id}', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'createUpdateAsistenciaAlumno']);
        Route::get('docentes/{docente_id}/pase-lista:search', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'searchAsistenciaAlumno']);
        Route::get('docentes/{docente_id}/materias', 'App\Http\Controllers\SpecialMethods\DocentesController@getMateriasGrupo');
        Route::get('docentes/{docente_id}/especialidades-periodos-grupos', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'getEspecialidadPeriodoGrupoPorDocente']);
        Route::get('especialidades-periodos-grupos/{especialidad_periodo_grupo_id}/docentes/{docente_id}/materias', [App\Http\Controllers\SpecialMethods\DocentesController::class, 'getMateriasByEspecialidadPeriodoGrupo']);
        Route::get('docentes/*/exportsExamenesGrupo/{docente_materia_id}', 'App\Http\Controllers\SpecialMethods\DocentesController@exportarExamenesGrupo');
        Route::get('docentes/*/tabla-rubrica/{docente_materia_id}', 'App\Http\Controllers\SpecialMethods\DocentesController@getTablaRubrica');
        Route::put('docentes/*/alumnos/*/mis-actividades/{id}', 'App\Http\Controllers\SpecialMethods\DocentesController@updateCalificacionMisActividades');
        Route::get('docentes/{docente_id}/alumnos', 'App\Http\Controllers\SpecialMethods\DocentesController@obtenerListadoDeAlumnosDelDocente');

        // Alumnos
        Route::get('alumnos/{alumno_id}/tareas:search', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'listTareas']);
        Route::get('alumnos/{alumno_id}/tareas/*/tareas-enviadas:search', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'listTareasEnviadas']);
        Route::get('alumnos/{alumno_id}/tareas/{tarea_id}', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'getTarea']);
        Route::post('alumnos/{alumno_id}/tareas/{tarea_id}/tareas-enviadas', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'createUpdateTareaEnviada']);
        Route::get('alumnos/{alumno_id}/mis-actividades', [App\Http\Controllers\SpecialMethods\AlumnosMisActividadesController::class, 'search']);
        Route::get('alumnos/{alumno_id}/examenes:search', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'listExamenes']);
        Route::get('alumnos/{alumno_id}/examenes/{examen_id}', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'getExamen']);
        Route::get('alumnos/{alumno_id}/tabla-calificacion', [App\Http\Controllers\SpecialMethods\AlumnosController::class, 'getTablaCalificacion']);

        //EspecialidadPeriodoGrupo
        Route::get('especialidad-periodo-grupo','App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@list');
        Route::get('especialidad-periodo-grupo/{especialidad_periodo_grupo_id}', 'App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@get');
        Route::get('especialidad-periodo-grupo:search', 'App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@search');
        Route::get('generaciones', 'App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@generaciones');
        Route::delete('especialidad-periodo-grupo/{especialidad_periodo_grupo_id}', 'App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@delete');
        Route::get(
            'especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/alumnos/*',
            'App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@alumnos'
        );
        Route::get('especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/materias', 'App\Http\Controllers\SpecialMethods\EspecialidadPeriodoGrupoController@listMaterias');

        // Grupo
        Route::post('grupos/{grupo_id}/alumnos/*:bind', 'App\Http\Controllers\SpecialMethods\GruposController@bindAlumnos');
        Route::post('grupos/{grupo_id}/alumnos/{alumno_id}:bind', 'App\Http\Controllers\SpecialMethods\GruposController@bindAlumno');
        //tiempo en examen
        Route::post('usuarios/{usuario_id}/examenes/{examen_id}/timestamp:start', 'App\Http\Controllers\SpecialMethods\MarcaDeTiempoController@getTimestamp');
        Route::post('usuarios/{usuario_id}/examenes/{examen_id}/timestamp:end', 'App\Http\Controllers\SpecialMethods\MarcaDeTiempoController@endTimestamp');
        Route::get('grupos/*/alumnos/{usuario_id}/calendario', 'App\Http\Controllers\SpecialMethods\GruposController@alumnosGrupoCalendario');

        // Zoom
        Route::post('zoom/crearZoomMeeting', 'App\Http\Controllers\Api\ZoomController@crearZoomMeeting');
        Route::get('zoom/generarSignature', 'App\Http\Controllers\Api\ZoomController@generarSignature');

        // Rubrica
        Route::get('rubrica-docente-materia/{docente_materia_id}','App\Http\Controllers\Api\RubricaController@getByDocenteMateriaId');

        // Calificacion Parcial
        Route::get(
            'califacion-parcial/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/materias/{materia_id}/alumnos',
            'App\Http\Controllers\SpecialMethods\CalificacionParcialController@listAlumnos'
        );
        Route::post(
            'califacion-parcial/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/materias/{materia_id}/alumnos/{alumno_id}',
            'App\Http\Controllers\SpecialMethods\CalificacionParcialController@createUpdate'
        );
        Route::get('califacion-parcial/*/alumnos/{alumno_id}/kardex','App\Http\Controllers\SpecialMethods\CalificacionParcialController@kardexPorAlumno');
        Route::get('califacion-parcial/*/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/kardex','App\Http\Controllers\SpecialMethods\CalificacionParcialController@kardexAlumnosGrupo');
        Route::get('califacion-parcial/*/alumno/{alumno_id}/rendimiento','App\Http\Controllers\SpecialMethods\CalificacionParcialController@rendimientoAlumno');
        Route::get('califacion-parcial/*/alumnos/{alumno_id}/boleta','App\Http\Controllers\SpecialMethods\CalificacionParcialController@boletaPorAlumno');
        Route::get('califacion-parcial/*/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/boleta','App\Http\Controllers\SpecialMethods\CalificacionParcialController@boletaAlumnosGrupo');
        Route::get('califacion-parcial/*/especialidad-periodo-grupo/{especialidad_periodo_grupo_id}/materias/{materia_id}/acta-seguimiento','App\Http\Controllers\SpecialMethods\CalificacionParcialController@actaSeguimientoGrupo');
    });

});
