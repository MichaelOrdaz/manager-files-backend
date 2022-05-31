<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Dixa;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUserPostRequest;
use App\Http\Resources\BasicDocumentResource;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareDocumentController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = $request->user();

        $validated = $request->validate([
            'parent' => 'nullable|integer'
        ]);

        $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();
        $parentId = $validated['parent'] ?? null;
        if ($parentId) {
            Document::where('type_id', $typeFolder->id)
            ->where('id', $parentId)
            ->firstOrFail();
        }

        $builder = null;

        if ($parentId) {
            $builder = Document::when($parentId, function ($query, $parentId) {
                $query->where('parent_id', $parentId);
            });
        } else {
            $builder = $user->sharedGranted()
            ->groupBy('documents.id');
        }
        $userSharedDocuments = $builder->with([
            'type',
            'parent',
        ])
        ->where('department_id', $user->department_id)
        ->withCount(['sons' => fn ($query) => $query->where('type_id', $typeFolder->id)])
        ->get();

        return (BasicDocumentResource::collection($userSharedDocuments))->additional([
            'message' => 'Documents successfully retrieved',
            'success' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentUserPostRequest $request, $documentId)
    {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
