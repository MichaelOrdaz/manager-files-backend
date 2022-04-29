<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\UserAvatarRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserAvatarController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserAvatarRequest $request)
    {
        $user = Auth::user();
        $this->authorize('update', $user);
        $user->load('department');

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = $request->file('image')->store('profiles', 'public');
            $user->image = $path;
            $user->save();
            return (new UserResource($user))->additional([
                'message' => 'Image succesfully updated',
                'success' => true
            ]);
        } else {
            return $this->errorResponse('Invalid file proccessed', 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = Auth::user();
        $this->authorize('update', $user);
        $user->load('department');

        $user->image = null;
        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Image succesfully deleted',
            'success' => true
        ]);
    }
}
