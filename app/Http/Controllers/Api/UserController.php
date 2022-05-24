<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Department;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $user = $request->user();

        $validated = $request->validate([
            'name' => 'nullable',
            'role' => 'nullable|integer',
            'department_id' => 'nullable|integer',
        ]);

        $name = $validated['name'] ?? null;
        $roleId = $validated['role'] ?? null;
        $departmentId = $validated['department_id'] ?? null;

        if ($user->hasRole('Head of Department')) {
            if ($user->department_id !== (int) $departmentId) {
                return response()->json([
                    'errors' => 'This action is not allowed',
                    'success' => false,
                ], 403);
            }
        }

        $users = User::with(['roles', 'department', 'permissions'])
        ->when($name, function ($query, $name) {
            return $query->whereRaw("CONCAT(name,' ',lastname,' ',second_lastname) LIKE ?", "%{$name}%");
        })
        ->when($roleId, function ($query, $roleId) {
            return $query->whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
            });
        })
        ->when($departmentId, function ($query, $departmentId) {
            return $query->whereHas('department', function ($query) use ($departmentId) {
                $query->where('id', $departmentId);
            });
        })
        ->get();
        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);
        
        $data = $request->getData();

        $role = Role::findOrFail($data['role_id']);
        
        $department = null;
        if (isset($data['department_id'])) {
            $department = Department::findOrFail($data['department_id']);
        }

        $user = User::create($data);
        $user->assignRole($role->name);
        
        $user->department()->associate($department);
        $user->save();
        $user->load('department');
        
        return (new UserResource($user))->additional([
            'success' => true,
            'message' => 'user created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('view', $user);

        $user->load(['roles', 'department', 'permissions']);

        return (new UserResource($user))->additional([
            'message' => 'user retrieved successfully',
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $userId)
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        $data = $request->getData();
        $role = Role::findOrFail($data['role_id']);
        
        $department = null;
        if (isset($data['department_id'])) {
            $department = Department::findOrFail($data['department_id']);
        }

        $user->update($data);
        $user->syncRoles($role->name);
        
        if (array_key_exists('department_id', $data)) {
            $user->department()->associate($department);
            $user->save();
        }
        $user->load('department');
        
        return (new UserResource($user))->additional([
            'success' => true,
            'message' => 'user updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $userId
     * @return \Illuminate\Http\Response
     */
    public function destroy($userId)
    {
        $user = User::with(['roles', 'department'])->findOrFail($userId);
        $this->authorize('delete', $user);

        $user->delete();

        return (new UserResource($user))->additional([
            'success' => true,
            'message' => 'user deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'perPage' => 'nullable|integer',
            'name' => 'nullable',
            'role' => 'nullable|integer',
            'sortBy' => 'nullable|string',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        $perPage = $validated['perPage'] ?? 10;
        $sortBy = $validated['sortBy'] ?? 'id';
        $order = $validated['order'] ?? 'asc';
        
        $name = $validated['name'] ?? null;
        $roleId = $validated['role'] ?? null;

        $users = User::with(['roles', 'department'])
        ->when($name, function ($query, $name) {
            return $query->whereRaw("CONCAT(name,' ',lastname,' ',second_lastname) LIKE ?", "%{$name}%");
        })
        ->when($roleId, function ($query, $roleId) {
            return $query->whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
            });
        })
        ->orderBy($sortBy, $order)
        ->paginate($perPage);
        return new UserCollection($users);
    }
}
