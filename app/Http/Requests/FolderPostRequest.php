<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use League\Flysystem\Util;

class FolderPostRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'regex:/^[a-z0-9_\-\s]+$/i',
                'min:1',
                'max:255',
            ],
            'parent_id' => 'nullable|integer',
        ];
    }

    public function getData()
    {
        $validated = $this->validated();
        
        $location = [];
        if (isset($validated['parent_id'])) {
            $parent = Document::findOrFail($validated['parent_id']);
            $validated['parent'] = $parent;
            $location = array_filter(explode('/', $validated['parent']->location));
        }

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
                'name' => 'El nombre de la carpeta ya existe'
            ]);
        }
        $location[] = $name;
        $location = implode('/', $location);

        $pathOnDisk = Dixa::storageRootPath($location);
        $isCreated = Dixa::useFolder($pathOnDisk);
        if (!$isCreated) {
            throw ValidationException::withMessages([
                'name' => 'Nombre de la carpeta inválido'
            ]);
        }
        $validated['location'] = $location;
        $validated['name'] = $name;
        return $validated;
    }

}
