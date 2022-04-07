<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginFormRequest;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\SignUpFormRequest;

class AuthController extends Controller
{

    public function login(LoginFormRequest $request)
    {
        $credentials = $request->getData();

        if (!Auth::attempt($credentials, true)) {
            return $this->errorResponse([
                'errors' => 'Credenciales incorrectas'
            ], 403);
        }

        $token = Auth::user()->createToken('authToken')->accessToken;
        
        return $this->successResponse('Ok', [
            'token' => $token,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->errorResponse('Successfully logged out');
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function account_data()
    {
        /*
            NOTE:
            Aunque pareciera que $userObject y $userData tienen el mismo valor. Despues de
            llamar a getAllPermissions se precargan al modelo usuario el atributo roles []
            Para no incluirlo en la peticion se usan diferentes referencias
                $userObject y $userData
        */
        $authUser = Auth::user();
        $id = $authUser->id;
        $userObject = User::findOrFail($id);

        $userData = User::findOrFail($id);
        $temp1 = $userObject->getAllPermissions()->whereNull('is_view');
        $temp2 = $userObject->getAllPermissions()->whereNotNull('is_view');

        $permissions = $this->TransformPermissions($temp1);
        $views = $this->TransformPermissions($temp2);

        $especialidadPeriodoGrupo = optional($userObject->alumnoGrupo)->especialidadPeriodoGrupo;
        $userData->especialidadPeriodoGrupo = is_null($especialidadPeriodoGrupo) 
        ? []
        : [$especialidadPeriodoGrupo];
        $especialidadPeriodo = optional($especialidadPeriodoGrupo)->EspecialidadPeriodo;

        $user_data = [
            "nombre" => optional($userData->datosGenerales)->nombre,
            "apellido_paterno" => optional($userData->datosGenerales)->apellido_paterno,
            "apellido_materno" => optional($userData->datosGenerales)->apellido_materno,
            "imagen_url" => getS3url( optional($userData->datosGenerales)->imagen_url, '+8 hours'),
            "especialidad_id" => optional($especialidadPeriodo)->especialidad_id,
            "especialidad" => optional($especialidadPeriodo)->especialidad,
            "periodo_id" => optional($especialidadPeriodo)->periodo_id,
            "periodo" => optional($especialidadPeriodo)->periodo,
            "grupo_id" => optional(optional($especialidadPeriodoGrupo)->grupo)->id,
            "grupo" => optional($especialidadPeriodoGrupo)->grupo,
            "especialidad_periodo_grupo_id" => optional($especialidadPeriodoGrupo)->id,
        ];

        if ($userObject->hasRole('Alumno')) {
            $user_data['tutor'] = $userObject->tutor;
            $user_data['datosAcademicos'] = $userObject->datosAcademicos;
        }
        if ($userObject->hasRole('Padre de familia')) {
            $user_data['tutorados'] = $userObject->tutorados;
        }

        if (optional($userData->datosGenerales)->imagen_url) {
            $userData->datosGenerales->imagen_url = getS3url($userData->datosGenerales->imagen_url, '+8 hours');
        }

        return response()->json([
            "user" => $userData,
            "permissions" => $permissions,
            "views" => $views,
            "roles" => $userObject->getRoleNames(),
            "user_data" => $user_data,
        ]);
    }

    public function TransformPermissions( $perms ) {
        $permissions = [];
        foreach ($perms as $permission) {
            if ($permission->is_view) {                         //NOTE: Views Format
                $permissions[$permission->name] = [
                    "ruta" => $permission->is_view,
                    "id" => $permission->id
                ];
            } else {                                            //NOTE: Permissions Format
                $permissions[$permission->name] = [
                    "id" => $permission->id,
                    "created_at" => $permission->created_at,
                    "updated_at" => $permission->updated_at,
                    "origin_role_id" => $permission->pivot ? $permission->pivot->role_id : null,
                ];
            }
        }
        return $permissions;
    }

}
