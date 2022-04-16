<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserCollection;
use App\Models\Historial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with(['roles', 'departamento'])->get();
        return new UserCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function show(Historial $historial)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Historial $historial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Historial  $historial
     * @return \Illuminate\Http\Response
     */
    public function destroy(Historial $historial)
    {
        //
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'perPage' => 'nullable|integer',
            'nombre' => 'nullable',
            'role' => 'nullable',
            'sortBy' => 'nullable|string',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        $perPage = $validated['perPage'] ?? 10;
        $sortBy = $validated['sortBy'] ?? 'id';
        $order = $validated['order'] ?? 'asc';
        
        $nombre = $validated['nombre'] ?? null;
        $role = $validated['role'] ?? null;

        $users = User::with(['roles', 'departamento'])
        ->where(function ($query) use ($nombre, $role) {
            if (isset($nombre)) {
                $query->orWhere('nombre', 'like', "%{$nombre}%");
                $query->orWhere('paterno', 'like', "%{$nombre}%");
                $query->orWhere('materno', 'like', "%{$nombre}%");
            }
            if (isset($role)) {
                $query->whereHas('roles', function ($query) use ($role) {
                    $query->where('name', $role);
                });
            }
        })
        ->orderBy($sortBy, $order)
        ->paginate($perPage);
        return new UserCollection($users);
    }
}
