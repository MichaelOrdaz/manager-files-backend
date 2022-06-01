<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Dixa;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUserPostRequest;
use App\Http\Resources\BasicDocumentResource;
use App\Http\Resources\BasicDocumentShareForMeResource;
use App\Http\Resources\DocumentResource;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;

class ShareDocumentForMeController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = $request->user();

        $validated = $request->validate([
            'parent' => 'nullable|integer',
            'department_id' => 'nullable|integer'
        ]);
        $departmentId = $validated['department_id'] ?? null;
        if ($departmentId) {
            Department::findOrFail($departmentId);
        }

        $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();
        $parentId = $validated['parent'] ?? null;
        $userShared = null;
        if ($parentId) {
            $document = Document::where('type_id', $typeFolder->id)
            ->where('id', $parentId)
            ->firstOrFail();

            $userShared = User::userHasAuthorizationToAccessDocument($user, $document);
            if (!$userShared) {
              throw new AuthorizationException('You are not authorized to perform this action');
            }
        }

        $builder = null;

        if ($parentId) {
            $builder = Document::when($parentId, function ($query, $parentId) {
                $query->where('parent_id', $parentId);
            });
        } else {
            $builder = $user->share();
        }
        $userSharedDocuments = $builder->with([
            'type',
            'parent',
            'department',
            'creator'
        ])
        ->when($departmentId, function ($query, $departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->withCount(['sons' => fn ($query) => $query->where('type_id', $typeFolder->id)])
        ->get();

        if ($parentId) {
          $userSharedDocuments = $userSharedDocuments->map(function ($item) use ($userShared) {
            $item->permission = $userShared->pivot->permission;
            $item->granted_by = $userShared->pivot->granted_by;
            return $item;
          });
        }

        return (BasicDocumentShareForMeResource::collection($userSharedDocuments))->additional([
            'message' => 'Documents successfully retrieved',
            'success' => true
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($documentId)
    {
      $document = Document::findOrFail($documentId);
      $this->authorize('viewShared', $document);

      $document->load([
          'creator',
          'type',
          'parent',
          'department',
          'creator.roles',
          'historical',
          'historical.user',
          'historical.action',
          'share',
          'share.department',
      ]);

      return (new DocumentResource($document))->additional([
          'message' => 'Documents successfully retrieved',
          'success' => true
      ]);
    }

}
