<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Dixa;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\FilePostCreateRequest;
use App\Http\Requests\FilePostUpdateRequest;
use App\Http\Requests\FolderPostRequest;
use App\Http\Resources\BasicDocumentResource;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use League\Flysystem\Util;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

class UserDocumentController extends Controller
{
    const NUMBER_OF_RECORDS = 100;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = Auth::user();

        $documentTypes = DocumentType::all();

        $validated = $request->validate([
            'parent' => 'nullable|integer',
            'name' => 'nullable|min:2',
            //advanced filters
            'all' => 'nullable|boolean',
            'type' => "nullable|in:{$documentTypes->pluck('name')->join(',')}",
            'tags' => 'array|nullable',
            'tags.*' => 'regex:/^[a-z0-9_\-\s\.]+$/i|max:80',
            'identifiers' => 'nullable|array',
            'identifiers.*' => 'regex:/^[0-9]+$/',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d',
            // pagination query params
            'page' => 'nullable|integer',
            'perPage' => 'nullable|integer',
            'sortBy' => 'nullable|string',
            'order' => 'nullable|string|in:asc,desc',
        ]);

        $name = $validated['name'] ?? null;
        //advanced filters
        $all = $validated['all'] ?? false;
        $type = $validated['type'] ?? null;
        $tags = $validated['tags'] ?? null;
        $identifiers = $validated['identifiers'] ?? null;
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;
        // pagination query params
        $perPage = $validated['perPage'] ?? self::NUMBER_OF_RECORDS;
        $perPage = $perPage > self::NUMBER_OF_RECORDS ? self::NUMBER_OF_RECORDS : $perPage;
        $sortBy = $validated['sortBy'] ?? 'name';
        $order = $validated['order'] ?? 'asc';

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
        ])
        ->where('department_id', $user->department_id)
        ->where(function ($query) use ($parentId, $all) {
            if ($parentId) {
                $query->where('parent_id', $parentId);
            } elseif (!$all) {
                $query->whereNull('parent_id');
            }
        })
        ->withCount(['sons' => fn ($query) => $query->where('type_id', $typeFolder->id)])
        //advanced filters
        ->when($name, function ($query, $name) {
            $query->where('name', 'like', "%$name%");
        })
        ->when($type, function ($query, $type) use ($documentTypes) {
            $documentType = $documentTypes->where('name', $type)->first();
            $query->where('type_id', $documentType->id);
        })
        ->when($tags, function ($query, $tags) {
            $query->where(function($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            });
        })
        ->where(function ($query) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate);
            } elseif ($startDate) {
                $query->whereDate('date', $startDate);
            } elseif ($endDate) {
                $query->whereDate('date', $endDate);
            }
        })
        ->when($identifiers, function ($query, $identifiers) {
            $query->where(function ($query) use ($identifiers) {
                foreach ($identifiers as $identifier) {
                    $identifier = (int) $identifier;
                    $query->orWhere(function ($query) use ($identifier) {
                        $query->whereRaw("{$identifier} BETWEEN min_identifier AND IFNULL(max_identifier, min_identifier)");
                    });
                }
            });
        })
        ->orderBy($sortBy, $order)
        ->paginate($perPage);

        return new DocumentCollection($documents);
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

    public function storeFile(FilePostCreateRequest $request)
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
    public function update(FilePostUpdateRequest $request, $documentId)
    {
        $document = Document::findOrFail($documentId);
        $this->authorize('update', $document);
        $data = $request->getData();
        
        $document->update($data);

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

        $currentPathOnDisk = Dixa::storageRootPath($document->location);
        $location = explode('/', $document->location);
        array_splice($location, -1, 1, $name);
        $location = implode('/', $location);
        if (file_exists($currentPathOnDisk))
            rename($currentPathOnDisk, Dixa::storageRootPath($location));

        $document->update([
            'name' => $name,
            'location' => $location,
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

    public function downloadFolder($documentId)
    {
        $document = Document::findOrFail($documentId);

        $pathDocument = Dixa::storageRootPath($document->location);

        $filenameZip = storage_path("zip/{$document->name}.zip");

        $rootPath = $pathDocument;

        $zip = new ZipArchive();

        $zip->open($filenameZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($document->type->name === Dixa::FOLDER) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir())
                {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        } else {
            $zip->addFile($pathDocument . DIRECTORY_SEPARATOR . $document->name, "{$document->name}.pdf");
        }
        $zip->close();

        return response()->download($filenameZip)->deleteFileAfterSend(true);
    }
}
