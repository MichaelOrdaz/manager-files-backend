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
use Illuminate\Validation\ValidationException;
use League\Flysystem\Util;

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

        $typeFolder = DocumentType::where('name', Dixa::FOLDER)->first();
        $parentId = $validated['parent'] ?? null;
        if ($parentId) {
            Document::where('type_id', $typeFolder->id)
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
        ->withCount(['sons' => fn ($query) => $query->where('type_id', $typeFolder->id)])
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
            'share',
            'share.department',
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
    public function destroy($documentId)
    {
        $document = Document::findOrFail($documentId);
        $this->authorize('delete', $document);

        $document->load([
            'type',
            'parent',
        ]);

        if ($document->type->name === Dixa::FOLDER) {
            $childrenDocuments = Dixa::getChildrenProperty($document->children, 'id');
            $childrenDocuments->push($document->id);
            $total = Document::destroy($childrenDocuments);
        } elseif ($document->type->name === Dixa::FILE) {
            $total = Document::destroy($document->id);
        }

        return (new BasicDocumentResource($document))->additional([
            'total' => $total,
            'message' => 'Document successfully deleted',
            'success' => true
        ]);
    }

    public function rename(Request $request, $documentId)
    {
        $document = Document::findOrFail($documentId);
        $this->authorize('update', $document);
        
        $validated = $request->validate([
            'name' => [
                'required',
                'regex:/^[a-z0-9_\-\s]+$/i',
                'min:1',
                'max:255'
            ]
        ]);

        $name = Util::normalizePath($validated['name']);
        $nameAlreadyExistsAtSameLevel = Document::where('id', '!=', $document->id)
        ->where('name', $name)
        ->where(function ($query) use ($document) {
            if (isset($document->parent_id)) {
                $query->where('parent_id', $document->parent_id);
            } else {
                $query->whereNull('parent_id');
            }
        })
        ->first();
        if ($nameAlreadyExistsAtSameLevel) {
            throw ValidationException::withMessages([
                'name' => 'El nombre de la recurso ya existe'
            ]);
        }

        $document->update([
            'name' => $validated['name']
        ]);

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
}
