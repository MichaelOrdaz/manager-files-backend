<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUserPostRequest;
use App\Http\Resources\UserWithSharePermissionResource;
use App\Models\Document;
use App\Models\DocumentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareDocumentController extends Controller
{

    public function index(Request $request, $documentId)
    {
        $this->authorize('viewAny', DocumentUser::class);

        $document = Document::findOrFail($documentId);

        $validated = $request->validate([
            'department_id' => 'nullable|integer',
        ]);

        $departmentId = $validated['department_id'] ?? null;

        $users = User::with([
            'roles', 
            'department',
            'share' => function ($query) use ($document) {
                $query->where('document_id', $document->id);
            }
        ])
        ->when($departmentId, function ($query, $departmentId) {
            return $query->whereHas('department', function ($query) use ($departmentId) {
                $query->where('id', $departmentId);
            });
        })
        ->get();

        return UserWithSharePermissionResource::collection($users)->additional([
            'success' => true,
            'message' => 'user created successfully'
        ]);;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentUserPostRequest $request, $documentId)
    {
        $this->authorize('create', DocumentUser::class);
        
        $user = Auth::user();
        $document = Document::findOrFail($documentId);

        $data = $request->getData();

        $usersToSync = $data->mapWithKeys(function ($item, $key) use ($user) {
            return [
                $item['user']->id => [
                    'permission' => $item['permission'],
                    'granted_by' => $user->id,
                ]
            ];
        });

        $result = $document->share()->sync($usersToSync);

        return response()->json([
            'data' => $result,
            'success' => true,
            'message' => 'Related permissions successfully'
        ]);
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
