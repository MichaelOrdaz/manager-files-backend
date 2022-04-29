<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserResetPasswordController extends Controller
{
    public function update(UserResetPasswordRequest $request, $userId)
    {
        $user = User::with('department')->findOrFail($userId);
        $this->authorize('resetPassword', $user);

        $data = $request->getData();
        $user->password = $data['newPasswordHashed'];
        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Password succesfully updated',
            'success' => true
        ]);
    }
}
