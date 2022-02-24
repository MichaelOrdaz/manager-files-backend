<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Cocur\Slugify\Slugify;


class EstadosFormRequest extends FormRequest
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
        $rules = [
            'nombre' => 'required|string',
            'activo' => 'boolean',
        ];

        return $rules;
    }

    /**
     * Get the request's data from the request.
     *
     *
     * @return array
     */
    public function getData()
    {
        $data = $this->only(['nombre', 'activo']);
        if($this->has('activo')){ $data['activo'] = filter_var($data['activo'], FILTER_VALIDATE_BOOLEAN); }
        return $data;
    }


    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new \Illuminate\Http\Response([
            'errors' => $validator->errors()->all(),
            'success' => false,
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
