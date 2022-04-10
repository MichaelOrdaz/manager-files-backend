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
        
        $user = Auth::user();
        $user->load('departamento');
        return $this->successResponse('Ok', [
            'token' => $token,
            'user' => $user,
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
        return $this->successResponse('Successfully logged out', []);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
        return $this->successResponse('ok', [
            'user' => $request->user()
        ]);
    }

    public function account_data()
    {
        $user = Auth::user();
        $permissions = $user->getAllPermissions();

        $regularPermissions = $permissions->whereNull('is_view');
        $tmp = [];
        foreach ($regularPermissions as $item) {
            $tmp[$item->name] = [
                'id' => $item->id,
            ];
        }
        $regularPermissions = $tmp;
        $tmp = [];
        $viewPermissions = $permissions->whereNotNull('is_view');
        foreach ($viewPermissions as $item) {
            $tmp[$item->name] = [
                'id' => $item->id,
                'ruta' => $item->is_view,
            ];
        }
        $viewPermissions = $tmp;
        
        $userFresh = $user->fresh();
        $userFresh->load('departamento');

        return $this->successResponse('Ok', [
            'user' => $userFresh,
            'roles' => $user->getRoleNames(),
            'permissions' => $regularPermissions,
            'views' => $viewPermissions,
        ]);
    }
}
