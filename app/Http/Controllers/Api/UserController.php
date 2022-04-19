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
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $validated = $request->validate([
            'nombre' => 'nullable',
            'role' => 'nullable|integer',
        ]);

        $nombre = $validated['nombre'] ?? null;
        $roleId = $validated['role'] ?? null;

        $users = User::with(['roles', 'departamento'])
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
            'role' => 'nullable|integer',
            'sortBy' => 'nullable|string',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        $perPage = $validated['perPage'] ?? 10;
        $sortBy = $validated['sortBy'] ?? 'id';
        $order = $validated['order'] ?? 'asc';
        
        $nombre = $validated['nombre'] ?? null;
        $roleId = $validated['role'] ?? null;

        $users = User::with(['roles', 'departamento'])
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
