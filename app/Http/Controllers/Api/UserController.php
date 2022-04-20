<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;

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

        $validated = $request->validate([
            'nombre' => 'nullable',
            'role' => 'nullable|integer',
        ]);

        $nombre = $validated['nombre'] ?? null;
        $roleId = $validated['role'] ?? null;

        $users = User::with(['roles', 'department'])
        ->when($nombre, function ($query, $nombre) {
            return $query->whereRaw("CONCAT(nombre,' ',paterno,' ',materno) LIKE ?", "%{$nombre}%");
        })
        ->when($roleId, function ($query, $roleId) {
            return $query->whereHas('roles', function ($query) use ($roleId) {
                $query->where('id', $roleId);
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
        $user = User::create($data);
        $user->assignRole($data['role']->name);
        
        $user->department()->associate($data['departamento']);
        $user->save();
        
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

        $user->load(['roles', 'department']);

        return (new UserResource($user))->additional([
            'message' => 'user retrieved successfully',
            'success' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $historial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $historial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $historial
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $historial)
    {
        //
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'perPage' => 'nullable|integer',
            'nombre' => 'nullable',
            'role' => 'nullable|integer',
            'sortBy' => 'nullable|string',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        $perPage = $validated['perPage'] ?? 10;
        $sortBy = $validated['sortBy'] ?? 'id';
        $order = $validated['order'] ?? 'asc';
        
        $nombre = $validated['nombre'] ?? null;
        $roleId = $validated['role'] ?? null;

        $users = User::with(['roles', 'department'])
        ->when($nombre, function ($query, $nombre) {
            return $query->whereRaw("CONCAT(nombre,' ',paterno,' ',materno) LIKE ?", "%{$nombre}%");
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
