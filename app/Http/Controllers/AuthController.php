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
}
