<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Dixa;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnalystPermissionResource;
use App\Http\Resources\SharedDocumentPermissions;
use App\Models\DocumentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class AnalystPermissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole(['Head of Department', 'Admin'])) {
          if ($user->hasRole('Analyst') && $user->cannot(Dixa::ANALYST_WRITE_PERMISSION)) {
            return response()->json([
              'errors' => 'This action is unauthorize',
              'success' => false
            ], 403);
          }
        }

        $permissions = Permission::whereIn('name', Dixa::ANALYST_PERMISSIONS)->get();

        return AnalystPermissionResource::collection($permissions)->additional([
            'message' => 'Permissions successfully retrieved',
            'success' => true,
        ]);
    }
}
