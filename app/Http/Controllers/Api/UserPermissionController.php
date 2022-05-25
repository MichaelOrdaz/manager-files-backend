<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPermissionPostRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('assignPermission', $user);

        $validated = $request->validate([
            'permission' => 'nullable|string'
        ]);

        $permissions = [];
        if (isset($validated['permission'])) {
            $permissions[] = Permission::findByName($validated['permission']);
        }
        $user->syncPermissions($permissions);

        $user->load(['roles', 'department', 'permissions']);

        return (new UserResource($user))->additional([
            'message' => 'user retrieved successfully',
            'success' => true,
        ]);
    }

    public function storeMany(UserPermissionPostRequest $request)
    {
        $this->authorize('assignPermission', User::class);

        $data = $request->getData();
        $users = $data->map(function ($item) {
            $permissions = [];
            if (isset($item['permission'])) {
                $permissions[] = Permission::findByName($item['permission']);
            }
            $item['user']->syncPermissions($permissions);
            $item['user']->load(['roles', 'department', 'permissions']);
            return $item['user'];
        });

        return (UserResource::collection($users))->additional([
            'message' => 'users updated successfully',
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
