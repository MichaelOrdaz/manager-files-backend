<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\BasicDocumentResource;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $carpeta = DocumentType::where('name', 'Carpeta')->first();
            Document::where('type_id', $carpeta->id)
            ->firstOrFail($parentId);
        }

        $documents = Document::with([
            'type',
            'parent',
        ])->where('department_id', $user->department_id)
        ->where('parent_id', $parentId)
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
    public function store(Request $request)
    {
        //
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
