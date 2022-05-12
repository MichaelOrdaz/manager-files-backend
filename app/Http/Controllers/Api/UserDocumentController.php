<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Dixa;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\FilePostRequest;
use App\Http\Requests\FolderPostRequest;
use App\Http\Resources\BasicDocumentResource;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class UserDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = Auth::user();

        $validated = $request->validate([
            'parent' => 'nullable|integer'
        ]);

        $parentId = $validated['parent'] ?? null;
        if ($parentId) {
            $folder = DocumentType::where('name', Dixa::FOLDER)->first();
            Document::where('type_id', $folder->id)
            ->where('id', $parentId)
            ->firstOrFail();
        }

        $documents = Document::with([
            'type',
            'parent',
        ])->where('department_id', $user->department_id)
        ->when($parentId, function ($query, $parentId) {
            $query->where('parent_id', $parentId);
        }, function ($query) {
            $query->whereNull('parent_id');
        })
        ->get();

        return (BasicDocumentResource::collection($documents))->additional([
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
    public function storeFolder(FolderPostRequest $request)
    {
        $this->authorize('create', Document::class);
        $data = $request->getData();
        $user = Auth::user();
        $type = DocumentType::where('name', Dixa::FOLDER)->first();

        $document = Document::create($data);
        $document->creator()->associate($user);
        $document->type()->associate($type);
        $document->department()->associate($user->department);
        $document->parent()->associate($data['parent'] ?? null);
        $document->save();

        $document->load([
            'creator',
            'type',
            'parent',
            'department',
        ]);

        return (new DocumentResource($document))->additional([
            'message' => 'Document successfully retrieved',
            'success' => true,
        ]);
    }

    public function storeFile(FilePostRequest $request)
    {
        $this->authorize('create', Document::class);
        $data = $request->getData();
        $user = Auth::user();
        $type = DocumentType::where('name', Dixa::FILE)->first();

        $document = Document::create($data);
        $document->creator()->associate($user);
        $document->type()->associate($type);
        $document->department()->associate($user->department);
        $document->parent()->associate($data['parent'] ?? null);
        $document->save();

        $document->load([
            'creator',
            'type',
            'parent',
            'department',
        ]);

        return (new DocumentResource($document))->additional([
            'message' => 'Document successfully retrieved',
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
        $this->authorize('view', $document);
        
        $document->load([
            'creator',
            'type',
            'parent',
            'department',
            'creator.roles',
            'historical',
            'historical.user',
            'historical.action',
            'shared'
        ]);

        return (new DocumentResource($document))->additional([
            'message' => 'Documents successfully retrieved',
            'success' => true
        ]);
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
