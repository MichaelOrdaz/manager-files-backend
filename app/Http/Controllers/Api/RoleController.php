<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::all();
        return RoleResource::collection($roles)->additional([
            'message' => 'Roles successfully retrieved',
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($roleId)
    {
        $role = Role::findOrFail($roleId);
        $this->authorize('view', $role);

        return (new RoleResource($role))->additional([
            'message' => 'Role successfully retrieved',
            'success' => true,
        ]);
    }
}
