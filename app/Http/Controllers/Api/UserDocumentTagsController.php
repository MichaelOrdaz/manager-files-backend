<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;

class UserDocumentTagsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $documentId)
    {
        $document = Document::findOrFail($documentId);
        $this->authorize('update', $document);

        $validated = $request->validate([
            'tags' => 'array|nullable',
            'tags.*' => 'string|min:2|max:80'
        ]);

        $document->update([
            'tags' => $validated['tags'] ?? []
        ]);

        $document->load(['type']);

        return (new DocumentResource($document))->additional([
            'message' => 'Documents successfully updated',
            'success' => true
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($documentId, $tagName)
    {
        $document = Document::findOrFail($documentId);
        $this->authorize('delete', $document);

        $tags = $document->tags;
        $tags = array_filter($tags, fn ($tag) => $tag !== $tagName);
        $document->update([
            'tags' => $tags
        ]);

        $document->load(['type']);

        return (new DocumentResource($document))->additional([
            'message' => 'Documents successfully updated',
            'success' => true
        ]);
    }
}
