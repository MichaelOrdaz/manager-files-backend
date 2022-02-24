<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Traits\ListTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\Controller;
use Illuminate\Auth\Access\Response;

class RolesController extends Controller
{
    use ListTrait;
    /**
     * Display a listing of the assets.
     *
     * @return Roles
     */
    public function list()
    {

        if(!Auth::user()->can('consultar roles'))
        {
            return response()->json([
                "errors" => ['No tienes autorización para ver este recurso'],
                "success" => false,
            ],403);
        }

        $roles = DB::table('roles')->get();

        $datos = $roles->transform(function ($rol) {
            return $this->transform($rol);
        });

        return $this->successResponse(
            'Roles se recuperaron correctamente.',
            $datos,
        );
    }

    /**
     * Transform the giving materia to public friendly array
     *
     * @param App\Models\Materia $materia
     *
     * @return array
     */
    protected function transform($rol)
    {
        return [
            'id' => $rol->id,
            'nombre' => $rol->name,
        ];
    }
}
