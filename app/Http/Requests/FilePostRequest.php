<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use League\Flysystem\Util;

class FilePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $MAX_FILE_SIZE = (100 * Dixa::MB);
        return [
            'name' => 'required|regex:/^[a-z0-9_\-\s\.]+$/i|min:1|max:255',
            'description' => 'required|min:1|max:65000',
            'date' => 'required|date_format:Y-m-d',
            'min_identifier' => 'required|integer',
            'max_identifier' => 'nullable|integer',
            'parent_id' => 'nullable|integer',
            'file' => "required|mimes:pdf|max:{$MAX_FILE_SIZE}",
        ];
    }

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
        $name = Util::normalizePath($validated['name']);
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
                'name' => 'Nombre de la carpeta inválido'
            ]);
        }
        $path = str_replace($sectionFiles, '', $path);
        $validated['location'] = $path;
        return $validated;
    }
}
