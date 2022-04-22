<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserPasswordController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserPasswordRequest $request, $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        $data = $request->getData();

        if (!Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('The password is not correct', 422);
        }

        $user->password = $data['newPasswordHashed'];
        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Password succesfully updated',
            'success' => true
        ]);
    }
}
