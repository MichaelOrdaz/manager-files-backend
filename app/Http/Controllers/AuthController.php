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
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(SignUpFormRequest $request)
    {
        $data = $request->getData();

        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->save();
        $user->assignRole($data['role']);

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(LoginFormRequest $request)
    {

        $credentials = $request->getData();

        $usuario = User::where('email', $credentials['email'])
        ->firstOrFail();

        $credentials = [
            'email' => $usuario->email,
            'password' => $credentials['password']
        ];

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
                'success' => false,
            ], 400);
        }

        if (auth()->user()->hasRole(['Deshabilitado'])) {
            return $this->errorResponse('No tienes autorización para acceder al sistema', 403);
        }

        if(auth()->user()->email_verified_at == null ){
            return response()->json([
                'message' => 'Correo no verificado',
                'success' => false,
            ], 400);
        }

        $tokenResult = auth()->user()->createToken('authToken');

        $token = $tokenResult->token;

        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'success' => true,
            'message' => 'Ok',
        ], 200);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
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


        return response()->json([
            "user" => $userData,
            "permissions" => $permissions,
            "views" => $views,
            "roles" => $userObject->getRoleNames(),
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
