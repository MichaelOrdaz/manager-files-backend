<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use League\Flysystem\Util;

class FilePostUpdateRequest extends FilePostRequest
{
    public function rules()
    {
        $MAX_FILE_SIZE = (100 * Dixa::MB);
        $rules = parent::rules();
        $rules['file'] = "nullable|mimes:pdf|max:{$MAX_FILE_SIZE}";
        return $rules;
    }

    public function getData()
    {
        $document = Document::findOrFail($this->route('document_id'));
        if ($document->type->name !== Dixa::FILE) {
            throw ValidationException::withMessages([
                'name' => 'El recurso no es un archivo'
            ]);
        }

        $validated = $this->validated();
        
        $location = '';
        if (isset($validated['parent_id'])) {
            $parent = Document::findOrFail($validated['parent_id']);
            $validated['parent'] = $parent;
            $location = $parent->location;
        }

        $name = Util::normalizePath(basename($validated['name'], '.pdf'));
        $nameAlreadyExistsAtSameLevel = Document::where('name', $name)
        ->where(function ($query) use ($validated) {
            if (isset($validated['parent_id'])) {
                $query->where('parent_id', $validated['parent_id']);
            } else {
                $query->whereNull('parent_id');
            }
        })
        ->where('id', '!=', $document->id)
        ->first();

        if ($nameAlreadyExistsAtSameLevel) {
            throw ValidationException::withMessages([
                'name' => 'El nombre del archivo ya existe'
            ]);
        }

        $currentPathOnDisk = Dixa::storageRootPath($document->location);
        $validated['name'] = $name;

        if ($this->file('file')) { // save upload
            $extension = $this->file('file')->extension();
            $filename = "{$name}.{$extension}";
            
            $sectionFiles = Dixa::PATH_FILES . DIRECTORY_SEPARATOR;
            $path = $sectionFiles . $location;

            $path = $this->file('file')->storeAs(
                Util::normalizePath($path), 
                $filename, 
                'public'
            );
            if (!$path) {
                throw ValidationException::withMessages([
                    'name' => 'El nombre del archivo es inválido'
                ]);
            }
            $path = str_replace($sectionFiles, '', $path);
            $validated['location'] = $path;
            if (file_exists($currentPathOnDisk))
                unlink($currentPathOnDisk);
        } else { // rename
            
            $extension = pathinfo($document->location, PATHINFO_EXTENSION);
            $filename = "{$name}.{$extension}";
            
            $location = explode('/', $document->location);
            array_splice($location, -1, 1, $filename);
            $location = implode('/', $location);
            if (file_exists($currentPathOnDisk))
                rename($currentPathOnDisk, Dixa::storageRootPath($location));

            $validated['location'] = $location;
        }
        return $validated;
    }
}
