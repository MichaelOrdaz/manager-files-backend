<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use Illuminate\Foundation\Http\FormRequest;

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
            'min_identifier' => 'required|regex:/^[0-9]+$/',
            'max_identifier' => 'nullable|regex:/^[0-9]+$/',
            'parent_id' => 'nullable|integer',
            'file' => "required|mimes:pdf|max:{$MAX_FILE_SIZE}",
        ];
    }
}
