<?php

namespace App\Http\Requests;

use App\Helpers\Dixa;
use Illuminate\Foundation\Http\FormRequest;

class UserAvatarRequest extends FormRequest
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
        $MAX_FILE_SIZE = (8 * Dixa::MB);
        return [
            'image' => "required|image|max:{$MAX_FILE_SIZE}",
        ];
    }
}
