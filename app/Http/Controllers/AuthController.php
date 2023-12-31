<?php
namespace App\Http\Controllers;

use App\Helpers\Dixa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\LoginFormRequest;
use App\Http\Controllers\Api\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;


class AuthController extends Controller
{

    public function login(LoginFormRequest $request)
    {
        $credentials = $request->getData();

        $user = User::where('email', $credentials['email'])->firstOrFail();

        if (!Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse([
                'errors' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $user->createToken('authToken')->accessToken;

        $user->load('department');
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
        $user = User::find(Auth::id());
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
                'path' => $item->is_view,
            ];
        }
        $viewPermissions = $tmp;
        
        $user->load('department');

        return $this->successResponse('Ok', [
            'user' => new UserResource($user),
            'roles' => $user->getRoleNames()->map(fn ($rol) => Dixa::SPANISH_ROLES[$rol]),
            'permissions' => $regularPermissions,
            'views' => $viewPermissions,
        ]);
    }

    public function verifyAuth()
    {
        return response()->json([
            'isAuth' => Auth::check()
        ]);
    }
}
