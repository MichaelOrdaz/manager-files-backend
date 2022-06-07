<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use League\Flysystem\Util;

class FilePostCreateRequest extends FilePostRequest
{
    public function getData()
    {
        $validated = $this->validated();
        
        $location = '';
        if (isset($validated['parent_id'])) {
            $parent = Document::findOrFail($validated['parent_id']);
            $validated['parent'] = $parent;
            $location = $parent->location;
        }
        $extension = $this->file('file')->extension();
        $name = Util::normalizePath(basename($validated['name'], '.pdf'));
        $nameAlreadyExistsAtSameLevel = Document::where('name', $name)
        ->where(function ($query) use ($validated) {
            if (isset($validated['parent_id'])) {
                $query->where('parent_id', $validated['parent_id']);
            } else {
                $query->whereNull('parent_id');
            }
        })
        ->first();
        if ($nameAlreadyExistsAtSameLevel) {
            throw ValidationException::withMessages([
                'name' => 'El nombre del archivo ya existe'
            ]);
        }
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
        return $validated;
    }
}
