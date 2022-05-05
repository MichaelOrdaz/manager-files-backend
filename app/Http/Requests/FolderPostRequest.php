<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required|string|alpha_dash',
            'parent_id' => 'nullable|integer',
        ];
    }

    public function getData()
    {
        $validated = $this->validated();
        if (isset($validated['parent_id'])) {
            $parent = Document::findOrFail($validated['parent_id']);
            $validated['parent'] = $parent;
        }
        return $validated;
    }
}
