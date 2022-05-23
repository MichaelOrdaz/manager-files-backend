<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Dixa;
use App\Http\Controllers\Controller;
use App\Http\Resources\SharedDocumentPermissions;
use App\Models\DocumentUser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class SharePermissionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', DocumentUser::class);

        $permissions = Permission::whereIn('name', Dixa::SHARE_DOCUMENT_PERMISSIONS)->get();

        return SharedDocumentPermissions::collection($permissions)->additional([
            'message' => 'Shared document permissions successfully retrieved',
            'success' => true,
        ]);
    }
}
